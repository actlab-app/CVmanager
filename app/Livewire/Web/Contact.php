<?php

namespace App\Livewire\Web;

use App\Models\ContactItem;
use App\Models\ContactMessage;
use App\Models\ContactSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class Contact extends Component
{
    private const LANGUAGES = ['tr', 'en'];

    public string $name = '';

    public string $email = '';

    public string $subject = '';

    public string $message = '';

    public string $website = '';

    public bool $sent = false;

    public function setLocale(string $language): void
    {
        if (! in_array($language, self::LANGUAGES, true)) {
            return;
        }

        session()->put('locale', $language);
        App::setLocale($language);
        $this->redirect(request()->header('Referer'), navigate: true);
    }

    public function submit(): void
    {
        $this->sent = false;
        $this->validate();

        if ($this->website !== '') {
            $this->resetForm();

            return;
        }

        $key = 'contact-form:'.sha1(request()->ip().'|'.strtolower($this->email));

        $accepted = RateLimiter::attempt($key, 3, function (): void {
            ContactMessage::create([
                'name' => $this->name,
                'email' => $this->email,
                'subject' => $this->subject,
                'message' => $this->message,
            ]);
        }, 600);

        if (! $accepted) {
            $this->addError('message', app()->getLocale() === 'tr'
                ? 'Çok kısa sürede fazla mesaj gönderdiniz. Lütfen daha sonra tekrar deneyin.'
                : 'Too many messages were sent in a short time. Please try again later.');

            return;
        }

        $this->resetForm();
        $this->sent = true;
    }

    public function render(): View
    {
        $settings = ContactSetting::first();

        return view('livewire.web.contact', [
            'settings' => $settings,
            'contactItems' => ContactItem::query()->active()->ordered()->get(),
        ])->layout('components.layouts.web', [
            'title' => ($settings?->title ?: __('İletişim')).' - CV Manager',
        ]);
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:180'],
            'subject' => ['required', 'string', 'min:3', 'max:180'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'website' => ['nullable', 'string', 'max:255'],
        ];
    }

    private function resetForm(): void
    {
        $this->reset(['name', 'email', 'subject', 'message', 'website']);
        $this->resetValidation();
    }
}
