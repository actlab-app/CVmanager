<?php

use App\Livewire\Admin\CvManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

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
        ->assertSeeHtml('wire:confirm="Bu öğeyi silmek istediğinize emin misiniz?"')
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
    $record = new \App\Models\CvRecord(['full_name' => 'Test User']);
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

    $record = \App\Models\CvRecord::firstOrFail();

    expect($record->getTranslation('skills', 'tr')[0]['category'])->toBe('Frontend')
        ->and($record->getTranslation('skills', 'tr')[0])->not->toHaveKey('_key');
});
