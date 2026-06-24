<?php

namespace App\Livewire\Web;

use App\Models\AboutSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use Livewire\Component;

class About extends Component
{
    private const LANGUAGES = ['tr', 'en'];

    public function setLocale(string $language): void
    {
        if (! in_array($language, self::LANGUAGES, true)) {
            return;
        }

        session()->put('locale', $language);
        App::setLocale($language);
        $this->redirect(request()->header('Referer'), navigate: true);
    }

    public function render(): View
    {
        $language = App::getLocale();
        $setting = AboutSetting::first();
        $about = config("about.translations.{$language}");

        if ($setting) {
            foreach (array_keys($about) as $field) {
                $about[$field] = $setting->getTranslation($field, $language, false) ?: $about[$field];
            }
        }

        foreach (['hero_panels', 'focus_cards'] as $field) {
            $about[$field] = $setting?->getTranslation($field, $language, false)
                ?: config("about.{$field}.{$language}", []);
        }

        $about['hero_panels'] = $this->normalizeHeroPanels(
            $about['hero_panels'],
            config('about.hero_image_path'),
            $language,
        );

        $contactSettings = \App\Models\ContactSetting::first();

        return view('livewire.web.about', [
            'about' => $about,
            'heroImagePath' => config('about.hero_image_path'),
            'profileImagePath' => $setting?->profile_image_path ?: config('about.profile_image_path'),
            'profileIsPersonal' => (bool) $setting?->profile_is_personal,
            'privacyHidden' => (bool) $contactSettings?->privacy_hidden,
            'privacyNotice' => $contactSettings?->privacy_notice,
        ])
            ->layout('components.layouts.web', [
                'title' => $about['headline'].' - CV Manager',
            ]);
    }

    private function normalizeHeroPanels(mixed $items, ?string $fallbackImagePath, string $language): array
    {
        $items = is_array($items) ? array_values($items) : [];
        $fallbackItems = config("about.hero_panels.{$language}", []);
        $panels = [];

        for ($index = 0; $index < 3; $index++) {
            $item = is_array($items[$index] ?? null) ? $items[$index] : [];
            $fallback = is_array($fallbackItems[$index] ?? null) ? $fallbackItems[$index] : [];

            $panels[] = [
                'image_path' => $item['image_path'] ?? $fallback['image_path'] ?? $fallbackImagePath,
                'title' => $item['title'] ?? $fallback['title'] ?? '',
                'description' => $item['description'] ?? $fallback['description'] ?? '',
            ];
        }

        return $panels;
    }
}
