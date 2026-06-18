<?php

namespace App\Livewire\Admin;

use App\Models\ContactMessage;
use App\Models\ContactSetting;
use App\Models\PortfolioProject;
use App\Models\PortfolioTechnology;
use App\Models\SiteSetting;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
class Dashboard extends Component
{
    public bool $privacyHidden = false;
    public bool $noindex = false;
    public string $webThemeColor = 'green';

    public function mount(): void
    {
        $this->privacyHidden = (bool) ContactSetting::first()?->privacy_hidden;
        $siteSettings = SiteSetting::first();
        $this->noindex = (bool) $siteSettings?->noindex;
        $this->webThemeColor = $this->normalizeThemeColor($siteSettings?->web_theme_color);
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

    public function render(): View
    {
        return view('livewire.admin.dashboard', [
            'projectCount' => PortfolioProject::count(),
            'technologyCount' => PortfolioTechnology::count(),
            'unreadMessageCount' => ContactMessage::whereNull('read_at')->count(),
            'themeColors' => config('web-theme-colors'),
        ]);
    }

    private function normalizeThemeColor(?string $color): string
    {
        return array_key_exists((string) $color, config('web-theme-colors'))
            ? (string) $color
            : 'green';
    }
}
