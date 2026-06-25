<?php

namespace App\Livewire\Admin;

use App\Models\CvRecord;
use Flux\Flux;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('CV Yönetimi')]
class CvManager extends Component
{
    private const LANGUAGES = ['tr', 'en'];

    private const TEXT_FIELDS = [
        'job_title',
        'about_content',
        'classic_profile_summary',
    ];

    private const REPEATER_SCHEMAS = [
        'quick_infos' => ['icon' => 'info', 'title' => '', 'value' => ''],
        'educations' => ['icon' => 'graduation-cap', 'degree' => '', 'school' => ''],
        'experiences' => ['icon' => 'briefcase-business', 'company' => '', 'description' => '', 'detailed_description' => ''],
        'skills' => ['icon' => 'code-xml', 'category' => '', 'details' => ''],
        'project_types' => ['icon' => 'folder-kanban', 'type' => '', 'description' => ''],
    ];

    public string $activeLang = 'tr';

    public string $full_name = '';

    public string $qr_url = '';

    public array $translations = [
        'tr' => [
            'job_title' => '',
            'about_content' => '',
            'classic_profile_summary' => '',
        ],
        'en' => [
            'job_title' => '',
            'about_content' => '',
            'classic_profile_summary' => '',
        ],
    ];

    public array $quick_infos = ['tr' => [], 'en' => []];

    public array $educations = ['tr' => [], 'en' => []];

    public array $experiences = ['tr' => [], 'en' => []];

    public array $skills = ['tr' => [], 'en' => []];

    public array $project_types = ['tr' => [], 'en' => []];

    public array $repeaterOrder = [
        'quick_infos' => [],
        'educations' => [],
        'experiences' => [],
        'skills' => [],
        'project_types' => [],
    ];

    public function mount(): void
    {
        $record = CvRecord::first();

        if (! $record) {
            return;
        }

        $this->full_name = $record->full_name ?? '';
        $this->qr_url = $record->qr_url ?? '';

        foreach (self::LANGUAGES as $lang) {
            foreach (self::TEXT_FIELDS as $field) {
                $this->translations[$lang][$field] = $record->getTranslation($field, $lang, false) ?? '';
            }

            foreach (self::REPEATER_SCHEMAS as $field => $schema) {
                $items = $record->getTranslation($field, $lang, false);
                $this->{$field}[$lang] = $this->normalizeItems($items, $schema);
            }
        }

        foreach (array_keys(self::REPEATER_SCHEMAS) as $field) {
            $this->assignRowKeys($field);
        }
    }

    public function switchLang(string $lang): void
    {
        if (in_array($lang, self::LANGUAGES, true)) {
            $this->activeLang = $lang;
        }
    }

    public function updated(string $property, mixed $value): void
    {
        if (! preg_match('/^([a-z_]+)\.(tr|en)\.([^.]+)\.icon$/', $property, $matches)) {
            return;
        }

        [, $field, $lang, $rowKey] = $matches;

        if (! isset(self::REPEATER_SCHEMAS[$field])) {
            return;
        }

        $otherLang = $lang === 'tr' ? 'en' : 'tr';
        $items = $this->{$field};
        if (! isset($items[$otherLang][$rowKey])) {
            return;
        }

        $items[$otherLang][$rowKey]['icon'] = (string) $value;
        $this->{$field} = $items;
    }

    public function addItem(string $field): void
    {
        if (! isset(self::REPEATER_SCHEMAS[$field])) {
            return;
        }

        $rowKey = (string) Str::uuid();

        foreach (self::LANGUAGES as $lang) {
            $this->{$field}[$lang][$rowKey] = self::REPEATER_SCHEMAS[$field];
        }

        $this->repeaterOrder[$field][] = $rowKey;
    }

    public function removeItem(string $field, string $rowKey): void
    {
        if (! isset(self::REPEATER_SCHEMAS[$field])) {
            return;
        }

        foreach (self::LANGUAGES as $lang) {
            unset($this->{$field}[$lang][$rowKey]);
        }

        $this->repeaterOrder[$field] = array_values(array_filter(
            $this->repeaterOrder[$field],
            fn (string $key): bool => $key !== $rowKey,
        ));
    }

    public function moveItemUp(string $field, string $rowKey): void
    {
        $this->moveItem($field, $rowKey, -1);
    }

    public function moveItemDown(string $field, string $rowKey): void
    {
        $this->moveItem($field, $rowKey, 1);
    }

    public function save(): void
    {
        $this->validate([
            'qr_url' => ['nullable', 'url:http,https', 'max:2048'],
        ]);

        $record = CvRecord::firstOrNew();
        $record->full_name = $this->full_name;
        $record->qr_url = $this->qr_url ?: null;

        foreach (self::LANGUAGES as $lang) {
            foreach (self::TEXT_FIELDS as $field) {
                $record->setTranslation($field, $lang, $this->translations[$lang][$field]);
            }

            foreach (array_keys(self::REPEATER_SCHEMAS) as $field) {
                $record->setTranslation($field, $lang, $this->orderedItems($field, $lang));
            }
        }

        $record->save();

        Flux::toast('CV başarıyla kaydedildi!', variant: 'success');
    }

    public function render()
    {
        return view('livewire.admin.cv-manager');
    }

    private function moveItem(string $field, string $rowKey, int $direction): void
    {
        if (! isset(self::REPEATER_SCHEMAS[$field])) {
            return;
        }

        $index = array_search($rowKey, $this->repeaterOrder[$field], true);

        if ($index === false) {
            return;
        }

        $target = $index + $direction;

        if (! isset($this->repeaterOrder[$field][$target])) {
            return;
        }

        [$this->repeaterOrder[$field][$index], $this->repeaterOrder[$field][$target]] = [
            $this->repeaterOrder[$field][$target],
            $this->repeaterOrder[$field][$index],
        ];
    }

    private function normalizeItems(mixed $items, array $schema): array
    {
        if (! is_array($items)) {
            return [];
        }

        return array_map(
            fn (mixed $item): array => is_array($item) ? array_replace($schema, $item) : $schema,
            $items,
        );
    }

    private function assignRowKeys(string $field): void
    {
        $itemsByLanguage = $this->{$field};
        $keyedItems = ['tr' => [], 'en' => []];
        $maxCount = max(array_map(
            fn (string $lang): int => count($itemsByLanguage[$lang]),
            self::LANGUAGES,
        ));

        for ($index = 0; $index < $maxCount; $index++) {
            $rowKey = (string) Str::uuid();
            $this->repeaterOrder[$field][] = $rowKey;

            foreach (self::LANGUAGES as $lang) {
                $keyedItems[$lang][$rowKey] = $itemsByLanguage[$lang][$index]
                    ?? self::REPEATER_SCHEMAS[$field];
            }
        }

        $this->{$field} = $keyedItems;
    }

    private function orderedItems(string $field, string $lang): array
    {
        return array_map(
            fn (string $rowKey): array => $this->{$field}[$lang][$rowKey],
            $this->repeaterOrder[$field],
        );
    }
}
