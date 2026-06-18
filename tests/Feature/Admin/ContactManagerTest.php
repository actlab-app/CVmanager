<?php

use App\Livewire\Admin\ContactManager;
use App\Livewire\Admin\Dashboard;
use App\Models\ContactItem;
use App\Models\ContactMessage;
use App\Models\ContactSetting;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('requires authentication for contact management', function () {
    auth()->logout();

    $this->get(route('contact-manager'))->assertRedirect('/admin');
});

it('renders the dedicated contact icon catalog', function () {
    Livewire::test(ContactManager::class)
        ->call('addItem')
        ->assertSee('WhatsApp')
        ->assertSee('GitHub')
        ->assertSee('LinkedIn')
        ->assertSee(asset('images/contact-icons/whatsapp.svg'))
        ->assertSee(asset('images/contact-icons/linkedin.svg'));
});

it('keeps moved contact rows and language labels isolated', function () {
    $component = Livewire::test(ContactManager::class)
        ->call('addItem')
        ->call('addItem')
        ->call('addItem');

    [$phoneKey, $emailKey, $githubKey] = $component->get('itemOrder');

    $component
        ->set("items.{$phoneKey}.label.tr", 'Telefon')
        ->set("items.{$phoneKey}.label.en", 'Phone')
        ->set("items.{$phoneKey}.value", '111')
        ->set("items.{$emailKey}.label.tr", 'E-posta')
        ->set("items.{$emailKey}.label.en", 'Email')
        ->set("items.{$emailKey}.value", '222')
        ->set("items.{$githubKey}.label.tr", 'GitHub')
        ->set("items.{$githubKey}.label.en", 'GitHub')
        ->set("items.{$githubKey}.value", '333');

    $component
        ->assertSeeHtml('wire:key="contact-item-tr-'.$phoneKey.'"')
        ->assertSeeHtml('wire:model="items.'.$phoneKey.'.value"')
        ->call('moveItem', $githubKey, -1)
        ->call('moveItem', $githubKey, -1)
        ->assertSet('itemOrder.0', $githubKey)
        ->set("items.{$githubKey}.value", 'github.com/example')
        ->set("items.{$githubKey}.label.tr", 'GitHub TR')
        ->set("items.{$githubKey}.label.en", 'GitHub EN')
        ->assertSet("items.{$githubKey}.value", 'github.com/example')
        ->assertSet("items.{$phoneKey}.value", '111')
        ->assertSet("items.{$emailKey}.value", '222')
        ->assertSet("items.{$githubKey}.label.tr", 'GitHub TR')
        ->assertSet("items.{$githubKey}.label.en", 'GitHub EN')
        ->assertSet("items.{$phoneKey}.label.tr", 'Telefon')
        ->assertSet("items.{$phoneKey}.label.en", 'Phone')
        ->call('switchLang', 'en')
        ->assertSeeHtml('wire:key="contact-item-en-'.$githubKey.'"');
});

it('saves multilingual contact settings and channels', function () {
    $component = Livewire::test(ContactManager::class)
        ->set('translations.tr.title', 'İletişime Geçin')
        ->set('translations.en.title', 'Get in Touch')
        ->set('translations.tr.location', 'İzmir, Türkiye')
        ->set('translations.en.location', 'Izmir, Turkey')
        ->set('privacy_hidden', true)
        ->call('addItem');

    $itemKey = $component->get('itemOrder.0');

    $component
        ->set("items.{$itemKey}.label.tr", 'Telefon')
        ->set("items.{$itemKey}.label.en", 'Phone')
        ->set("items.{$itemKey}.value", '+90 555 123 45 67')
        ->set("items.{$itemKey}.url", 'tel:+905551234567')
        ->set("items.{$itemKey}.icon", 'phone')
        ->set("items.{$itemKey}.is_private", true)
        ->call('save')
        ->assertHasNoErrors();

    $settings = ContactSetting::firstOrFail();
    $item = ContactItem::firstOrFail();

    expect($settings->getTranslation('title', 'en'))->toBe('Get in Touch')
        ->and($settings->privacy_hidden)->toBeTrue()
        ->and($item->getTranslation('label', 'tr'))->toBe('Telefon')
        ->and($item->is_private)->toBeTrue();
});

it('rejects icons outside the contact catalog', function () {
    $component = Livewire::test(ContactManager::class)
        ->set('translations.tr.title', 'İletişim')
        ->set('translations.en.title', 'Contact')
        ->call('addItem');

    $itemKey = $component->get('itemOrder.0');

    $component
        ->set("items.{$itemKey}.label.tr", 'Özel Kanal')
        ->set("items.{$itemKey}.label.en", 'Custom Channel')
        ->set("items.{$itemKey}.value", 'example')
        ->set("items.{$itemKey}.icon", 'invalid-brand')
        ->call('save')
        ->assertHasErrors(["items.{$itemKey}.icon"]);
});

it('toggles contact privacy from the dashboard', function () {
    ContactSetting::create([
        'title' => ['tr' => 'İletişim', 'en' => 'Contact'],
        'privacy_hidden' => false,
    ]);

    Livewire::test(Dashboard::class)
        ->set('privacyHidden', true);

    expect(ContactSetting::firstOrFail()->privacy_hidden)->toBeTrue();
});

it('marks and deletes contact messages', function () {
    $message = ContactMessage::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'subject' => 'Test Subject',
        'message' => 'This is a test contact message.',
    ]);

    Livewire::test(ContactManager::class)
        ->call('markAsRead', $message->id);

    expect($message->fresh()->read_at)->not->toBeNull();

    Livewire::test(ContactManager::class)
        ->call('deleteMessage', $message->id);

    expect(ContactMessage::find($message->id))->toBeNull();
});
