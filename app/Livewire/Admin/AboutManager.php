<?php

namespace App\Livewire\Admin;

use App\Models\AboutSetting;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Hakkımda Yönetimi')]
class AboutManager extends Component
{
    use WithFileUploads;

    private const LANGUAGES = ['tr', 'en'];

    private const TEXT_FIELDS = [
        'eyebrow',
        'headline',
        'intro',
        'current_label',
        'current_status',
        'current_text',
        'philosophy_title',
        'philosophy_text',
        'principles_title',
        'quote',
        'quote_attribution',
        'portfolio_cta',
        'contact_cta',
    ];

    private const REPEATER_SCHEMAS = [
        'hero_panels' => ['number' => '', 'title' => ''],
        'focus_cards' => ['icon' => 'sparkles', 'title' => '', 'text' => ''],
        'principles' => ['number' => '', 'text' => ''],
    ];

    public string $activeLang = 'tr';

    public array $translations = [];

    public array $hero_panels = ['tr' => [], 'en' => []];

    public array $focus_cards = ['tr' => [], 'en' => []];

    public array $principles = ['tr' => [], 'en' => []];

    public array $repeaterOrder = [
        'hero_panels' => [],
        'focus_cards' => [],
        'principles' => [],
    ];

    public mixed $heroImage = null;

    public ?string $existingHeroImagePath = null;

    public bool $removeExistingHeroImage = false;

    public mixed $profileImage = null;

    public ?string $existingProfileImagePath = null;

    public bool $removeExistingProfileImage = false;

    public bool $profileIsPersonal = false;

    public function mount(): void
    {
        $setting = AboutSetting::first();
        $this->existingHeroImagePath = $setting?->hero_image_path ?? config('about.hero_image_path');
        $this->existingProfileImagePath = $setting?->profile_image_path ?? config('about.profile_image_path');
        $this->profileIsPersonal = (bool) $setting?->profile_is_personal;

        foreach (self::LANGUAGES as $language) {
            foreach (self::TEXT_FIELDS as $field) {
                $this->translations[$language][$field] = $setting?->getTranslation($field, $language, false)
                    ?: config("about.translations.{$language}.{$field}", '');
            }

            foreach (array_keys(self::REPEATER_SCHEMAS) as $field) {
                $items = $setting?->getTranslation($field, $language, false)
                    ?: config("about.{$field}.{$language}", []);
                $this->{$field}[$language] = $this->normalizeItems($items, self::REPEATER_SCHEMAS[$field]);
            }
        }

        foreach (array_keys(self::REPEATER_SCHEMAS) as $field) {
            $this->assignRowKeys($field);
        }
    }

    public function switchLang(string $language): void
    {
        if (in_array($language, self::LANGUAGES, true)) {
            $this->activeLang = $language;
        }
    }

    public function updated(string $property, mixed $value): void
    {
        if (! preg_match('/^(hero_panels|focus_cards|principles)\.(tr|en)\.([^.]+)\.(number|icon)$/', $property, $matches)) {
            return;
        }

        [, $field, $language, $rowKey, $sharedField] = $matches;

        if (! array_key_exists($sharedField, self::REPEATER_SCHEMAS[$field])) {
            return;
        }

        $otherLanguage = $language === 'tr' ? 'en' : 'tr';

        if (! isset($this->{$field}[$otherLanguage][$rowKey])) {
            return;
        }

        $items = $this->{$field};
        $items[$otherLanguage][$rowKey][$sharedField] = (string) $value;
        $this->{$field} = $items;
    }

    public function addItem(string $field): void
    {
        if (! isset(self::REPEATER_SCHEMAS[$field])) {
            return;
        }

        $rowKey = (string) Str::uuid();

        foreach (self::LANGUAGES as $language) {
            $this->{$field}[$language][$rowKey] = self::REPEATER_SCHEMAS[$field];
        }

        $this->repeaterOrder[$field][] = $rowKey;
    }

    public function removeItem(string $field, string $rowKey): void
    {
        if (! isset(self::REPEATER_SCHEMAS[$field])) {
            return;
        }

        foreach (self::LANGUAGES as $language) {
            unset($this->{$field}[$language][$rowKey]);
        }

        $this->repeaterOrder[$field] = array_values(array_filter(
            $this->repeaterOrder[$field],
            fn (string $key): bool => $key !== $rowKey,
        ));
    }

    public function moveItem(string $field, string $rowKey, int $direction): void
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

    public function removeHeroImage(): void
    {
        $this->heroImage = null;
        $this->removeExistingHeroImage = true;
    }

    public function removeProfileImage(): void
    {
        $this->profileImage = null;
        $this->removeExistingProfileImage = true;
    }

    public function save(): void
    {
        $this->validate();

        $setting = AboutSetting::firstOrNew();
        $oldHeroImagePath = $setting->hero_image_path;
        $oldProfileImagePath = $setting->profile_image_path;
        $newHeroImagePath = $this->storeImage(
            $this->heroImage,
            $this->removeExistingHeroImage ? null : $this->existingHeroImagePath,
        );
        $newProfileImagePath = $this->storeImage(
            $this->profileImage,
            $this->removeExistingProfileImage ? null : $this->existingProfileImagePath,
        );

        $setting->hero_image_path = $newHeroImagePath;
        $setting->profile_image_path = $newProfileImagePath;
        $setting->profile_is_personal = $this->profileIsPersonal;

        foreach (self::LANGUAGES as $language) {
            foreach (self::TEXT_FIELDS as $field) {
                $setting->setTranslation($field, $language, $this->translations[$language][$field]);
            }

            foreach (array_keys(self::REPEATER_SCHEMAS) as $field) {
                $setting->setTranslation($field, $language, $this->orderedItems($field, $language));
            }
        }

        $setting->save();

        $this->deleteReplacedImage($oldHeroImagePath, $newHeroImagePath);
        $this->deleteReplacedImage($oldProfileImagePath, $newProfileImagePath);

        $this->existingHeroImagePath = $newHeroImagePath;
        $this->existingProfileImagePath = $newProfileImagePath;
        $this->heroImage = null;
        $this->profileImage = null;
        $this->removeExistingHeroImage = false;
        $this->removeExistingProfileImage = false;

        Flux::toast('Hakkımda sayfası kaydedildi.', variant: 'success');
    }

    public function render(): View
    {
        return view('livewire.admin.about-manager');
    }

    protected function rules(): array
    {
        return [
            'translations.tr.headline' => ['required', 'string', 'max:240'],
            'translations.en.headline' => ['required', 'string', 'max:240'],
            'translations.*.*' => ['nullable', 'string', 'max:2000'],
            'hero_panels.tr' => ['array', 'min:1', 'max:6'],
            'hero_panels.en' => ['array', 'min:1', 'max:6'],
            'hero_panels.*.*.number' => ['required', 'string', 'max:10'],
            'hero_panels.*.*.title' => ['required', 'string', 'max:100'],
            'focus_cards.tr' => ['array', 'max:8'],
            'focus_cards.en' => ['array', 'max:8'],
            'focus_cards.*.*.icon' => ['required', 'string', 'max:100'],
            'focus_cards.*.*.title' => ['required', 'string', 'max:100'],
            'focus_cards.*.*.text' => ['required', 'string', 'max:240'],
            'principles.tr' => ['array', 'max:10'],
            'principles.en' => ['array', 'max:10'],
            'principles.*.*.number' => ['required', 'string', 'max:10'],
            'principles.*.*.text' => ['required', 'string', 'max:240'],
            'heroImage' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
            'profileImage' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    private function storeImage(mixed $upload, ?string $currentPath): ?string
    {
        if (! $upload) {
            return $currentPath;
        }

        $directory = public_path('images/about/uploads');
        File::ensureDirectoryExists($directory);

        $extension = strtolower($upload->getClientOriginalExtension() ?: 'jpg');
        $filename = Str::uuid().'.'.$extension;
        File::copy($upload->getRealPath(), $directory.DIRECTORY_SEPARATOR.$filename);

        return 'images/about/uploads/'.$filename;
    }

    private function deleteReplacedImage(?string $oldPath, ?string $newPath): void
    {
        if ($oldPath && $oldPath !== $newPath && str_starts_with($oldPath, 'images/about/uploads/')) {
            File::delete(public_path($oldPath));
        }
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
            fn (string $language): int => count($itemsByLanguage[$language]),
            self::LANGUAGES,
        ));

        for ($index = 0; $index < $maxCount; $index++) {
            $rowKey = (string) Str::uuid();
            $this->repeaterOrder[$field][] = $rowKey;

            foreach (self::LANGUAGES as $language) {
                $keyedItems[$language][$rowKey] = $itemsByLanguage[$language][$index]
                    ?? self::REPEATER_SCHEMAS[$field];
            }
        }

        $this->{$field} = $keyedItems;
    }

    private function orderedItems(string $field, string $language): array
    {
        return array_map(
            fn (string $rowKey): array => $this->{$field}[$language][$rowKey],
            $this->repeaterOrder[$field],
        );
    }
}
