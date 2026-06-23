<?php

namespace App\Livewire\Admin;

use App\Models\PortfolioProject;
use App\Models\PortfolioTechnology;
use App\Support\TechnologyIcon;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Portfolio Projesi')]
class PortfolioEditor extends Component
{
    use WithFileUploads;

    private const MAX_IMAGES = 6;

    private const LANGUAGES = ['tr', 'en'];

    private const TRANSLATABLE_FIELDS = [
        'title',
        'short_description',
        'detailed_description',
        'project_type',
        'role',
        'duration',
        'platform',
    ];

    private const REPEATER_SCHEMAS = [
        'features' => ['icon' => 'sparkles', 'title' => '', 'description' => ''],
        'technical_decisions' => ['label' => '', 'value' => ''],
        'metrics' => ['icon' => 'chart-column', 'value' => '', 'label' => ''],
    ];

    public ?int $projectId = null;

    public string $activeLang = 'tr';

    public string $slug = '';

    public string $status = 'draft';

    public ?string $project_date = null;

    public string $live_url = '';

    public string $repository_url = '';

    public bool $is_featured = false;

    public bool $is_published = false;

    public int $sort_order = 0;

    public array $translations = [
        'tr' => [
            'title' => '',
            'short_description' => '',
            'detailed_description' => '',
            'project_type' => '',
            'role' => '',
            'duration' => '',
            'platform' => '',
        ],
        'en' => [
            'title' => '',
            'short_description' => '',
            'detailed_description' => '',
            'project_type' => '',
            'role' => '',
            'duration' => '',
            'platform' => '',
        ],
    ];

    public array $features = ['tr' => [], 'en' => []];

    public array $technical_decisions = ['tr' => [], 'en' => []];

    public array $metrics = ['tr' => [], 'en' => []];

    public array $repeaterOrder = [
        'features' => [],
        'technical_decisions' => [],
        'metrics' => [],
    ];

    public array $technologySlugs = [];

    public array $technologyCatalog = [];

    public array $existingImages = [];

    public array $existingImageOrder = [];

    public array $removedImageIds = [];

    public array $uploads = [];

    public array $uploadOrder = [];

    public array $uploadTranslations = [];

    public function mount(?PortfolioProject $project = null): void
    {
        $this->technologyCatalog = $this->technologyOptions();

        if (! $project?->exists) {
            $this->sort_order = ((int) PortfolioProject::max('sort_order')) + 1;

            return;
        }

        $this->projectId = $project->id;
        $this->slug = $project->slug;
        $this->status = $project->status;
        $this->project_date = $project->project_date?->format('Y-m-d');
        $this->live_url = $project->live_url ?? '';
        $this->repository_url = $project->repository_url ?? '';
        $this->is_featured = (bool) $project->is_featured;
        $this->is_published = (bool) $project->is_published;
        $this->sort_order = (int) ($project->sort_order ?? 0);
        $this->technologySlugs = $project->technologies ?? [];

        foreach (self::LANGUAGES as $lang) {
            foreach (self::TRANSLATABLE_FIELDS as $field) {
                $this->translations[$lang][$field] = $project->getTranslation($field, $lang, false) ?? '';
            }

            foreach (self::REPEATER_SCHEMAS as $field => $schema) {
                $items = $project->getTranslation($field, $lang, false);
                $this->{$field}[$lang] = $this->normalizeItems($items, $schema);
            }
        }

        foreach (array_keys(self::REPEATER_SCHEMAS) as $field) {
            $this->assignRowKeys($field);
        }

        $project->images->each(function ($image): void {
            $key = (string) Str::uuid();

            $this->existingImages[$key] = [
                'id' => $image->id,
                'path' => $image->path,
                'translations' => [
                    'tr' => [
                        'title' => $image->getTranslation('title', 'tr', false) ?? '',
                        'description' => $image->getTranslation('description', 'tr', false) ?? '',
                    ],
                    'en' => [
                        'title' => $image->getTranslation('title', 'en', false) ?? '',
                        'description' => $image->getTranslation('description', 'en', false) ?? '',
                    ],
                ],
            ];
            $this->existingImageOrder[] = $key;
        });
    }

    public function updated(string $property, mixed $value): void
    {
        if (preg_match('/^(features|metrics)\.(tr|en)\.([^.]+)\.icon$/', $property, $matches)) {
            [, $field, $lang, $rowKey] = $matches;
            $otherLang = $lang === 'tr' ? 'en' : 'tr';

            if (isset($this->{$field}[$otherLang][$rowKey])) {
                $items = $this->{$field};
                $items[$otherLang][$rowKey]['icon'] = (string) $value;
                $this->{$field} = $items;
            }
        }
    }

    public function updatedUploads(): void
    {
        while (count($this->uploadOrder) < count($this->uploads)) {
            $key = (string) Str::uuid();
            $this->uploadOrder[] = $key;
            $this->uploadTranslations[$key] = [
                'tr' => ['title' => '', 'description' => ''],
                'en' => ['title' => '', 'description' => ''],
            ];
        }

        while (count($this->uploadOrder) > count($this->uploads)) {
            $key = array_pop($this->uploadOrder);
            unset($this->uploadTranslations[$key]);
        }
    }

    public function switchLang(string $lang): void
    {
        if (in_array($lang, self::LANGUAGES, true)) {
            $this->activeLang = $lang;
        }
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

    public function removeExistingImage(int $position): void
    {
        $key = $this->existingImageOrder[$position] ?? null;

        if ($key === null) {
            return;
        }

        if (! isset($this->existingImages[$key])) {
            return;
        }

        $this->removedImageIds[] = $this->existingImages[$key]['id'];
        unset($this->existingImages[$key]);
        $this->existingImageOrder = array_values(array_filter(
            $this->existingImageOrder,
            fn (string $imageKey): bool => $imageKey !== $key,
        ));
    }

    public function moveExistingImage(int $position, int $direction): void
    {
        if (! isset($this->existingImageOrder[$position])) {
            return;
        }

        $target = $position + $direction;

        if (! isset($this->existingImageOrder[$target])) {
            return;
        }

        [$this->existingImageOrder[$position], $this->existingImageOrder[$target]] = [
            $this->existingImageOrder[$target],
            $this->existingImageOrder[$position],
        ];
    }

    public function discardUpload(int $position): void
    {
        $key = $this->uploadOrder[$position] ?? null;

        if ($key === null || ! isset($this->uploads[$position])) {
            return;
        }

        array_splice($this->uploads, $position, 1);
        array_splice($this->uploadOrder, $position, 1);
        unset($this->uploadTranslations[$key]);
    }

    public function save(): mixed
    {
        $this->slug = Str::slug($this->slug ?: $this->translations['tr']['title']);

        $this->validate($this->rules());

        if (count($this->existingImages) + count($this->uploads) > self::MAX_IMAGES) {
            $this->addError('uploads', 'Bir projeye en fazla '.self::MAX_IMAGES.' görsel eklenebilir.');

            return null;
        }

        $project = $this->projectId
            ? PortfolioProject::findOrFail($this->projectId)
            : new PortfolioProject;

        $project->fill([
            'slug' => $this->slug,
            'status' => $this->status,
            'project_date' => $this->project_date ?: null,
            'live_url' => $this->live_url ?: null,
            'repository_url' => $this->repository_url ?: null,
            'technologies' => array_values(array_intersect(
                $this->technologySlugs,
                PortfolioTechnology::query()->active()->pluck('slug')->all(),
            )),
            'is_featured' => $this->is_featured,
            'is_published' => $this->is_published,
            'sort_order' => $this->sort_order,
        ]);

        foreach (self::LANGUAGES as $lang) {
            foreach (self::TRANSLATABLE_FIELDS as $field) {
                $project->setTranslation($field, $lang, $this->translations[$lang][$field]);
            }

            foreach (array_keys(self::REPEATER_SCHEMAS) as $field) {
                $project->setTranslation($field, $lang, $this->orderedItems($field, $lang));
            }
        }

        $project->save();

        $project->images()->whereIn('id', $this->removedImageIds)->get()->each->delete();

        foreach ($this->existingImageOrder as $sortOrder => $key) {
            $imageData = $this->existingImages[$key];
            $image = $project->images()->find($imageData['id']);

            if (! $image) {
                continue;
            }

            $image->sort_order = $sortOrder;
            $this->setImageTranslations($image, $imageData['translations']);
            $image->save();
        }

        foreach ($this->uploadOrder as $index => $key) {
            $upload = $this->uploads[$index];
            $directory = public_path('images/portfolio/'.$project->slug);
            File::ensureDirectoryExists($directory);

            $extension = strtolower($upload->getClientOriginalExtension() ?: 'jpg');
            $filename = Str::uuid().'.'.$extension;
            File::copy($upload->getRealPath(), $directory.DIRECTORY_SEPARATOR.$filename);

            $image = $project->images()->create([
                'path' => 'images/portfolio/'.$project->slug.'/'.$filename,
                'sort_order' => count($this->existingImages) + $index,
            ]);

            $this->setImageTranslations(
                $image,
                $this->uploadTranslations[$key] ?? [
                    'tr' => ['title' => '', 'description' => ''],
                    'en' => ['title' => '', 'description' => ''],
                ],
            );
            $image->save();
        }

        Flux::toast($this->projectId ? 'Proje güncellendi.' : 'Proje oluşturuldu.', variant: 'success');

        return $this->redirectRoute('portfolio-manager.edit', ['project' => $project], navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.portfolio-editor', [
            'technologyCatalog' => $this->technologyCatalog,
            'maxImages' => self::MAX_IMAGES,
        ]);
    }

    private function rules(): array
    {
        return [
            'slug' => [
                'required',
                'alpha_dash',
                'max:160',
                Rule::unique('portfolio_projects', 'slug')->ignore($this->projectId),
            ],
            'status' => ['required', Rule::in(['draft', 'active', 'completed', 'archived'])],
            'project_date' => ['nullable', 'date'],
            'live_url' => ['nullable', 'string', 'max:2048'],
            'repository_url' => ['nullable', 'url', 'max:2048'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_featured' => ['boolean'],
            'is_published' => ['boolean'],
            'translations.tr.title' => ['required', 'string', 'max:180'],
            'translations.en.title' => ['required', 'string', 'max:180'],
            'translations.*.*' => ['nullable', 'string', 'max:5000'],
            'features.*' => ['array'],
            'technical_decisions.*' => ['array'],
            'metrics.*' => ['array'],
            'technologySlugs' => ['array'],
            'uploads.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'existingImages.*.translations.*.title' => ['nullable', 'string', 'max:180'],
            'existingImages.*.translations.*.description' => ['nullable', 'string', 'max:500'],
            'uploadTranslations.*.*.title' => ['nullable', 'string', 'max:180'],
            'uploadTranslations.*.*.description' => ['nullable', 'string', 'max:500'],
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

    private function setImageTranslations($image, array $translations): void
    {
        foreach (self::LANGUAGES as $lang) {
            $image->setTranslation('title', $lang, $translations[$lang]['title'] ?? '');
            $image->setTranslation('description', $lang, $translations[$lang]['description'] ?? '');
        }
    }

    private function technologyOptions(): array
    {
        return PortfolioTechnology::query()
            ->active()
            ->ordered()
            ->get(['name', 'slug', 'category', 'logo_path', 'icon'])
            ->map(fn (PortfolioTechnology $technology): array => [
                'name' => $technology->name,
                'slug' => $technology->slug,
                'category' => $technology->category,
                'logo_path' => $technology->logo_path,
                'render_icon' => TechnologyIcon::resolve($technology->icon),
            ])
            ->all();
    }
}
