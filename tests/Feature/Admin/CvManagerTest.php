<?php

use App\Livewire\Admin\CvManager;
use App\Models\CvRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('renders Turkish labels and the save action without mojibake', function () {
    Livewire::test(CvManager::class)
        ->assertSee('CV Yönetimi')
        ->assertSee('Özgeçmiş bilgilerini buradan düzenleyebilirsiniz.')
        ->assertSee('Klasik CV Bilgileri')
        ->assertSee('Kaydet')
        ->assertDontSee('CV YÃ¶netimi')
        ->assertDontSee('TÃ¼rkÃ§e')
        ->assertDontSee('AÃ§Ä±klama');
});

it('adds repeater items with section-specific default icons', function () {
    $component = Livewire::test(CvManager::class)
        ->call('addItem', 'quick_infos')
        ->call('addItem', 'educations')
        ->call('addItem', 'experiences')
        ->call('addItem', 'skills')
        ->call('addItem', 'project_types');

    foreach ([
        'quick_infos' => 'info',
        'educations' => 'graduation-cap',
        'experiences' => 'briefcase-business',
        'skills' => 'code-xml',
        'project_types' => 'folder-kanban',
    ] as $field => $icon) {
        $rowKey = $component->get("repeaterOrder.{$field}.0");
        $component->assertSet("{$field}.tr.{$rowKey}.icon", $icon);
    }
});

it('persists classic CV summary and detailed experience text', function () {
    $component = Livewire::test(CvManager::class)
        ->set('full_name', 'Test User')
        ->set('translations.tr.classic_profile_summary', 'ATS uyumlu profesyonel ozet.')
        ->set('translations.en.classic_profile_summary', 'ATS friendly professional summary.')
        ->call('addItem', 'experiences');

    $rowKey = $component->get('repeaterOrder.experiences.0');

    $component
        ->set("experiences.tr.{$rowKey}.company", 'Calisto Digital')
        ->set("experiences.tr.{$rowKey}.description", 'Full-stack developer')
        ->set("experiences.tr.{$rowKey}.detailed_description", '100+ proje teslimatinda aktif rol.')
        ->set("experiences.en.{$rowKey}.company", 'Calisto Digital')
        ->set("experiences.en.{$rowKey}.description", 'Full-stack developer')
        ->set("experiences.en.{$rowKey}.detailed_description", 'Active role in 100+ project deliveries.')
        ->call('save');

    $record = CvRecord::firstOrFail();

    expect($record->getTranslation('classic_profile_summary', 'tr'))->toBe('ATS uyumlu profesyonel ozet.')
        ->and($record->getTranslation('experiences', 'tr')[0]['detailed_description'])->toBe('100+ proje teslimatinda aktif rol.');
});

it('loads legacy scalar classic CV summary without copying it into every language', function () {
    DB::table('cv_records')->insert([
        'full_name' => 'Test User',
        'classic_profile_summary' => json_encode('Eski tek dilli ozet.'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    Livewire::test(CvManager::class)
        ->assertSet('translations.tr.classic_profile_summary', 'Eski tek dilli ozet.')
        ->assertSet('translations.en.classic_profile_summary', '')
        ->set('translations.en.classic_profile_summary', 'English summary.')
        ->call('save');

    $record = CvRecord::firstOrFail();

    expect($record->getTranslation('classic_profile_summary', 'tr'))->toBe('Eski tek dilli ozet.')
        ->and($record->getTranslation('classic_profile_summary', 'en'))->toBe('English summary.');
});

it('keeps icons synchronized between languages', function () {
    $component = Livewire::test(CvManager::class)
        ->call('addItem', 'skills');
    $rowKey = $component->get('repeaterOrder.skills.0');

    $component
        ->set("skills.tr.{$rowKey}.icon", 'database')
        ->assertSet("skills.tr.{$rowKey}.icon", 'database')
        ->assertSet("skills.en.{$rowKey}.icon", 'database')
        ->set("skills.en.{$rowKey}.icon", 'cloud')
        ->assertSet("skills.tr.{$rowKey}.icon", 'cloud')
        ->assertSet("skills.en.{$rowKey}.icon", 'cloud');
});

it('keeps moved repeater rows isolated across fields and languages', function () {
    $component = Livewire::test(CvManager::class)
        ->call('addItem', 'skills')
        ->call('addItem', 'skills');
    [$backendKey, $frontendKey] = $component->get('repeaterOrder.skills');

    $component
        ->set("skills.tr.{$backendKey}.category", 'Backend')
        ->set("skills.en.{$backendKey}.category", 'Backend EN')
        ->set("skills.tr.{$backendKey}.details", 'PHP')
        ->set("skills.en.{$backendKey}.details", 'PHP EN')
        ->set("skills.tr.{$frontendKey}.category", 'Frontend')
        ->set("skills.en.{$frontendKey}.category", 'Frontend EN')
        ->set("skills.tr.{$frontendKey}.details", 'Alpine')
        ->set("skills.en.{$frontendKey}.details", 'Alpine EN')
        ->call('moveItemUp', 'skills', $frontendKey)
        ->assertSet('repeaterOrder.skills.0', $frontendKey)
        ->set("skills.tr.{$frontendKey}.details", 'Alpine Updated')
        ->set("skills.en.{$frontendKey}.details", 'Alpine Updated EN')
        ->assertSet("skills.tr.{$backendKey}.details", 'PHP')
        ->assertSet("skills.en.{$backendKey}.details", 'PHP EN')
        ->assertSet("skills.tr.{$frontendKey}.category", 'Frontend')
        ->assertSet("skills.en.{$frontendKey}.category", 'Frontend EN');
});

it('renders icon picker triggers with stable model bindings and client-side previews', function () {
    $component = Livewire::test(CvManager::class)
        ->call('addItem', 'quick_infos');
    $rowKey = $component->get('repeaterOrder.quick_infos.0');

    $component
        ->assertSeeHtml('wire:model.live="quick_infos.tr.'.$rowKey.'.icon"')
        ->assertSeeHtml('wire:model="quick_infos.tr.'.$rowKey.'.title"')
        ->assertSeeHtml('wire:confirm=')
        ->assertSeeHtml('open-lucide-icon-picker')
        ->assertDontSeeHtml('<ui-selected');
});

it('ignores unsupported languages and repeater fields', function () {
    Livewire::test(CvManager::class)
        ->call('switchLang', 'de')
        ->call('addItem', 'unknown')
        ->call('removeItem', 'unknown', 0)
        ->call('moveItemDown', 'unknown', 0)
        ->assertSet('activeLang', 'tr')
        ->assertSet('quick_infos.tr', []);
});

it('normalizes incomplete repeater data when loading a record', function () {
    $record = new CvRecord(['full_name' => 'Test User']);
    $record->setTranslation('quick_infos', 'tr', [['title' => 'Konum']]);
    $record->setTranslation('quick_infos', 'en', [['title' => 'Location']]);
    $record->save();

    $component = Livewire::test(CvManager::class);
    $rowKey = $component->get('repeaterOrder.quick_infos.0');

    $component
        ->assertSet("quick_infos.tr.{$rowKey}.icon", 'info')
        ->assertSet("quick_infos.tr.{$rowKey}.value", '')
        ->assertSet("quick_infos.en.{$rowKey}.icon", 'info');
});

it('persists repeater rows in their visible order without internal keys', function () {
    $component = Livewire::test(CvManager::class)
        ->set('full_name', 'Test User')
        ->call('addItem', 'skills')
        ->call('addItem', 'skills');
    [$backendKey, $frontendKey] = $component->get('repeaterOrder.skills');

    $component
        ->set("skills.tr.{$backendKey}.category", 'Backend')
        ->set("skills.en.{$backendKey}.category", 'Backend')
        ->set("skills.tr.{$frontendKey}.category", 'Frontend')
        ->set("skills.en.{$frontendKey}.category", 'Frontend')
        ->call('moveItemUp', 'skills', $frontendKey)
        ->call('save');

    $record = CvRecord::firstOrFail();

    expect($record->getTranslation('skills', 'tr')[0]['category'])->toBe('Frontend')
        ->and($record->getTranslation('skills', 'tr')[0])->not->toHaveKey('_key');
});
