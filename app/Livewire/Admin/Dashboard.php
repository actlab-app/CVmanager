<?php

namespace App\Livewire\Admin;

use App\Models\ContactMessage;
use App\Models\ContactSetting;
use App\Models\PortfolioProject;
use App\Models\PortfolioTechnology;
use App\Models\ReferenceToken;
use App\Models\SiteSetting;
use Carbon\CarbonImmutable;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
class Dashboard extends Component
{
    private const REFERENCE_TOKEN_CHART_LIMIT = 10;

    public bool $privacyHidden = false;

    public bool $noindex = false;

    public bool $blockVisitorsWithoutReferenceToken = false;

    public string $webThemeColor = 'green';

    public string $preferredCvTheme = 'modern';

    public ?string $referenceTokenChartDateFrom = null;

    public ?string $referenceTokenChartDateTo = null;

    public function mount(): void
    {
        $this->privacyHidden = (bool) ContactSetting::first()?->privacy_hidden;
        $siteSettings = SiteSetting::first();
        $this->noindex = (bool) $siteSettings?->noindex;
        $this->blockVisitorsWithoutReferenceToken = (bool) $siteSettings?->block_visitors_without_reference_token;
        $this->webThemeColor = $this->normalizeThemeColor($siteSettings?->web_theme_color);
        $this->preferredCvTheme = $this->normalizeCvTheme($siteSettings?->preferred_cv_theme);
    }

    public function updatedPrivacyHidden(bool $hidden): void
    {
        ContactSetting::firstOrCreate()->update(['privacy_hidden' => $hidden]);

        Flux::toast(
            $hidden ? 'Kişisel iletişim verileri gizlendi.' : 'Kişisel iletişim verileri görünür durumda.',
            variant: 'success',
        );
    }

    public function updatedNoindex(bool $value): void
    {
        SiteSetting::firstOrCreate()->update(['noindex' => $value]);

        Flux::toast(
            $value ? 'Arama motoru indekslemesi engellendi.' : 'Arama motoru indekslemesine izin verildi.',
            variant: 'success',
        );
    }

    public function updatedBlockVisitorsWithoutReferenceToken(bool $value): void
    {
        SiteSetting::firstOrCreate()->update(['block_visitors_without_reference_token' => $value]);

        Flux::toast(
            $value ? 'Tokensiz public ziyaretler engellenecek.' : 'Tokensiz public ziyaretlere izin verildi.',
            variant: 'success',
        );
    }

    public function selectWebThemeColor(string $color): void
    {
        $color = $this->normalizeThemeColor($color);
        $this->webThemeColor = $color;

        SiteSetting::firstOrCreate()->update(['web_theme_color' => $color]);

        Flux::toast(
            'Web tema rengi güncellendi.',
            variant: 'success',
        );
    }

    public function selectPreferredCvTheme(string $theme): void
    {
        $theme = $this->normalizeCvTheme($theme);
        $this->preferredCvTheme = $theme;

        SiteSetting::firstOrCreate()->update(['preferred_cv_theme' => $theme]);

        Flux::toast(
            'Tercih edilen CV teması güncellendi.',
            variant: 'success',
        );
    }

    public function resetReferenceTokenChartDates(): void
    {
        $this->reset(['referenceTokenChartDateFrom', 'referenceTokenChartDateTo']);
    }

    public function render(): View
    {
        return view('livewire.admin.dashboard', [
            'projectCount' => PortfolioProject::count(),
            'technologyCount' => PortfolioTechnology::count(),
            'unreadMessageCount' => ContactMessage::whereNull('read_at')->count(),
            'themeColors' => config('web-theme-colors'),
            'cvThemes' => [
                'modern' => [
                    'label' => 'Modern CV',
                    'description' => 'Mevcut görsel, kartlı ve portfolyo odaklı CV görünümü.',
                    'icon' => 'sparkles',
                ],
                'classic' => [
                    'label' => 'Klasik CV',
                    'description' => 'ATS uyumlu, taranabilir ve seçilmiş proje başarılarını öne çıkaran görünüm.',
                    'icon' => 'file-text',
                ],
            ],
            'referenceTokenChart' => $this->referenceTokenChart(),
        ]);
    }

    private function referenceTokenChart(): array
    {
        $tokens = ReferenceToken::query()
            ->withCount([
                'visits as chart_visits_count' => fn (Builder $query) => $this->applyReferenceVisitDateRange($query),
            ])
            ->whereHas('visits', fn (Builder $query) => $this->applyReferenceVisitDateRange($query))
            ->orderByDesc('chart_visits_count')
            ->orderBy('name')
            ->limit(self::REFERENCE_TOKEN_CHART_LIMIT)
            ->get(['id', 'name', 'token']);

        $maxVisits = max(1, (int) $tokens->max('chart_visits_count'));

        return $tokens
            ->map(fn (ReferenceToken $referenceToken): array => [
                'name' => $referenceToken->name,
                'token' => $referenceToken->token,
                'visits_count' => (int) $referenceToken->chart_visits_count,
                'percentage' => round(((int) $referenceToken->chart_visits_count / $maxVisits) * 100, 2),
            ])
            ->all();
    }

    private function applyReferenceVisitDateRange(Builder $query): Builder
    {
        [$dateFrom, $dateTo] = $this->referenceTokenChartDateRange();

        return $query
            ->when($dateFrom, fn (Builder $query) => $query->where('visited_at', '>=', $dateFrom))
            ->when($dateTo, fn (Builder $query) => $query->where('visited_at', '<=', $dateTo));
    }

    private function referenceTokenChartDateRange(): array
    {
        $dateFrom = $this->parseDate($this->referenceTokenChartDateFrom)?->startOfDay();
        $dateTo = $this->parseDate($this->referenceTokenChartDateTo)?->endOfDay();

        if ($dateFrom && $dateTo && $dateFrom->greaterThan($dateTo)) {
            return [$dateTo->startOfDay(), $dateFrom->endOfDay()];
        }

        return [$dateFrom, $dateTo];
    }

    private function parseDate(?string $date): ?CarbonImmutable
    {
        if (! $date) {
            return null;
        }

        try {
            return CarbonImmutable::createFromFormat('Y-m-d', $date);
        } catch (\Throwable) {
            return null;
        }
    }

    private function normalizeThemeColor(?string $color): string
    {
        return array_key_exists((string) $color, config('web-theme-colors'))
            ? (string) $color
            : 'green';
    }

    private function normalizeCvTheme(?string $theme): string
    {
        return in_array($theme, ['modern', 'classic'], true)
            ? (string) $theme
            : 'modern';
    }
}
