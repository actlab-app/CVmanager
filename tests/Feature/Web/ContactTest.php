<?php

use App\Livewire\Web\Contact;
use App\Models\ContactItem;
use App\Models\ContactMessage;
use App\Models\ContactSetting;
use Livewire\Livewire;

beforeEach(function () {
    $settings = new ContactSetting([
        'privacy_hidden' => true,
    ]);
    $settings->setTranslations('title', ['tr' => 'İletişime Geçin', 'en' => 'Get in Touch']);
    $settings->setTranslations('intro', ['tr' => 'Projeler için ulaşın.', 'en' => 'Contact me for projects.']);
    $settings->setTranslations('form_title', ['tr' => 'Mesaj Gönderin', 'en' => 'Send a Message']);
    $settings->setTranslations('privacy_notice', [
        'tr' => 'Bazı kişisel iletişim verileri gizlenmiştir.',
        'en' => 'Some personal contact details are hidden.',
    ]);
    $settings->setTranslations('success_message', [
        'tr' => 'Mesajınız alındı.',
        'en' => 'Your message has been received.',
    ]);
    $settings->setTranslations('location', ['tr' => 'İzmir, Türkiye', 'en' => 'Izmir, Turkey']);
    $settings->save();

    $phone = new ContactItem([
        'value' => '+90 555 123 45 67',
        'url' => 'tel:+905551234567',
        'icon' => 'phone',
        'is_private' => true,
        'is_active' => true,
        'sort_order' => 0,
    ]);
    $phone->setTranslations('label', ['tr' => 'Telefon', 'en' => 'Phone']);
    $phone->save();

    $github = new ContactItem([
        'value' => 'github.com/example',
        'url' => 'https://github.com/example',
        'icon' => 'github',
        'is_private' => false,
        'is_active' => true,
        'sort_order' => 1,
    ]);
    $github->setTranslations('label', ['tr' => 'GitHub', 'en' => 'GitHub']);
    $github->save();
});

it('renders contact details with private values masked', function () {
    $this->withSession(['locale' => 'tr'])
        ->get(route('contact'))
        ->assertOk()
        ->assertSee('İletişime Geçin')
        ->assertSee('+9••••67')
        ->assertDontSee('tel:+905551234567')
        ->assertSee('https://github.com/example')
        ->assertSee(asset('images/contact-icons/github.svg'))
        ->assertSee('Bazı kişisel iletişim verileri gizlenmiştir.');
});

it('stores submitted contact messages', function () {
    Livewire::test(Contact::class)
        ->set('name', 'Ahmet Test')
        ->set('email', 'ahmet@example.com')
        ->set('subject', 'Proje Hakkında')
        ->set('message', 'Yeni bir proje hakkında görüşmek istiyorum.')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('sent', true);

    expect(ContactMessage::count())->toBe(1)
        ->and(ContactMessage::first()->subject)->toBe('Proje Hakkında');
});

it('silently rejects honeypot submissions', function () {
    Livewire::test(Contact::class)
        ->set('name', 'Spam User')
        ->set('email', 'spam@example.com')
        ->set('subject', 'Spam Subject')
        ->set('message', 'This message should not be stored.')
        ->set('website', 'https://spam.example.com')
        ->call('submit')
        ->assertHasNoErrors();

    expect(ContactMessage::count())->toBe(0);
});
