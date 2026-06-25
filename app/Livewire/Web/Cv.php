<?php

namespace App\Livewire\Web;

use App\Models\ContactItem;
use App\Models\ContactSetting;
use App\Models\CvRecord;
use App\Models\PortfolioProject;
use App\Models\PortfolioTechnology;
use App\Models\SiteSetting;
use App\Support\ReferenceUrl;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use Livewire\Component;

class Cv extends Component
{
    private const LANGUAGES = ['tr', 'en'];

    private const TEXT_FIELDS = [
        'job_title',
        'about_content',
        'classic_profile_summary',
    ];

    private const REPEATER_FIELDS = [
        'quick_infos',
        'educations',
        'experiences',
        'skills',
        'project_types',
    ];

    private const CV_THEMES = ['modern', 'classic'];

    public function mount(): void
    {
        $locale = request()->query('locale');

        if (is_string($locale) && in_array($locale, self::LANGUAGES, true)) {
            session()->put('locale', $locale);
            App::setLocale($locale);
        }
    }

    public function setLocale(string $lang): void
    {
        if (! in_array($lang, self::LANGUAGES, true)) {
            return;
        }

        session()->put('locale', $lang);
        App::setLocale($lang);
        $this->redirect(ReferenceUrl::route('cv', ['locale' => $lang]));
    }

    public function render(): View
    {
        $record = CvRecord::firstOrFail();
        $locale = App::getLocale();

        $cvData = [
            'full_name' => $record->full_name,
            'qr_url' => $record->qr_url ? ReferenceUrl::appendToken($record->qr_url) : null,
        ];

        foreach (self::TEXT_FIELDS as $field) {
            $cvData[$field] = $record->getTranslation($field, $locale, false) ?? '';
        }

        foreach (self::REPEATER_FIELDS as $field) {
            $value = $record->getTranslation($field, $locale, false);
            $cvData[$field] = is_array($value) ? $value : [];
        }

        $contactSettings = ContactSetting::first();
        $contactItems = ContactItem::query()->active()->ordered()->get();
        $cvData['contact_items'] = $contactItems
            ->where('show_in_cv', true)
            ->map(fn (ContactItem $item): array => [
                'icon' => $item->icon,
                'label' => $item->getTranslation('label', $locale, false) ?: $item->label,
                'value' => $item->displayValue((bool) $contactSettings?->privacy_hidden),
                'url' => $item->url,
            ])
            ->values()
            ->all();

        $siteSettings = SiteSetting::first();
        $cvTheme = $this->normalizeCvTheme($siteSettings?->preferred_cv_theme);
        $view = $cvTheme === 'classic' ? 'livewire.web.cv-classic' : 'livewire.web.cv';

        return view($view, [
            'cvData' => $cvData,
            'classicData' => $this->classicData($cvData, $locale),
        ])
            ->layout('components.layouts.web', [
                'title' => $record->full_name.' - CV',
            ]);
    }

    private function classicData(array $cvData, string $locale): array
    {
        return [
            'profile_summary' => $this->classicProfileSummary($cvData, $locale),
            'projects' => $this->classicProjects($locale),
            'technology_catalog' => $this->technologyCatalog($locale),
        ];
    }

    private function classicProfileSummary(array $cvData, string $locale): string
    {
        $summary = trim((string) data_get($cvData, 'classic_profile_summary', ''));

        if ($summary !== '') {
            return $summary;
        }

        $text = str_replace(['<br>', '<br/>', '<br />'], "\n", (string) data_get($cvData, 'about_content', ''));
        $text = trim(strip_tags($text));

        if ($text !== '') {
            return preg_replace('/\s+/', ' ', $text) ?: $text;
        }

        return $locale === 'en'
            ? 'Full-stack web developer focused on business software, admin panels, integrations, reporting workflows, and end-to-end product delivery.'
            : 'İş yazılımları, yönetim panelleri, entegrasyonlar, raporlama akışları ve uçtan uca ürün teslimatı odağında çalışan full-stack web geliştirici.';
    }

    private function classicProjects(string $locale)
    {
        return PortfolioProject::query()
            ->where('is_published', true)
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('project_date')
            ->limit(8)
            ->get()
            ->map(function (PortfolioProject $project) use ($locale): array {
                return [
                    'title' => $project->getTranslation('title', $locale, false) ?: $project->title,
                    'type' => $project->getTranslation('project_type', $locale, false) ?: $project->project_type,
                    'summary' => $project->getTranslation('short_description', $locale, false) ?: $project->short_description,
                    'role' => $project->getTranslation('role', $locale, false) ?: $project->role,
                    'date' => $project->project_date?->format('Y'),
                    'technologies' => $project->technologies ?? [],
                    'metrics' => $project->getTranslation('metrics', $locale, false) ?: [],
                    'url' => ReferenceUrl::route('portfolio.show', $project),
                ];
            })
            ->all();
    }

    private function technologyCatalog(string $locale): array
    {
        return PortfolioTechnology::query()
            ->active()
            ->ordered()
            ->get()
            ->mapWithKeys(fn (PortfolioTechnology $technology): array => [
                $technology->slug => [
                    'name' => $technology->name,
                    'category' => $technology->category,
                ],
            ])
            ->all();
    }

    private function normalizeCvTheme(?string $theme): string
    {
        return in_array($theme, self::CV_THEMES, true)
            ? (string) $theme
            : 'modern';
    }
}
