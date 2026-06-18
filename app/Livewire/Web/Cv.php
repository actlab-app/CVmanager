<?php

namespace App\Livewire\Web;

use App\Models\ContactItem;
use App\Models\ContactSetting;
use App\Models\CvRecord;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use Livewire\Component;

class Cv extends Component
{
    private const LANGUAGES = ['tr', 'en'];

    private const TEXT_FIELDS = [
        'job_title',
        'about_content',
    ];

    private const REPEATER_FIELDS = [
        'quick_infos',
        'educations',
        'experiences',
        'skills',
        'project_types',
    ];

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
        $this->redirect(route('cv', ['locale' => $lang]));
    }

    public function render(): View
    {
        $record = CvRecord::firstOrFail();
        $locale = App::getLocale();

        $cvData = [
            'full_name' => $record->full_name,
            'qr_url' => $record->qr_url,
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

        return view('livewire.web.cv', [
            'cvData' => $cvData,
        ])
            ->layout('components.layouts.web', [
                'title' => $record->full_name.' - CV',
            ]);
    }
}
