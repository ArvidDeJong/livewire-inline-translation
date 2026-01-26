<?php

use Darvis\LivewireInlineTranslation\InlineTranslation;
use Darvis\LivewireInlineTranslation\Models\Translation;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

beforeEach(function () {
    // Set up language file
    app('translator')->addLines([
        'website.welcome' => 'Welcome from language file',
    ], 'en');
});

it('can mount with a translation key', function () {
    Livewire::test(InlineTranslation::class, ['translationKey' => 'website.welcome'])
        ->assertSet('translationKey', 'website.welcome')
        ->assertSet('translationValue', 'Welcome from language file');
});

it('shows language file translation when no database translation exists', function () {
    Livewire::test(InlineTranslation::class, ['translationKey' => 'website.welcome'])
        ->assertSee('Welcome from language file');
});

it('shows database translation when it exists', function () {
    Translation::create([
        'locale' => 'en',
        'group' => 'website',
        'key' => 'welcome',
        'value' => 'Welcome from database',
    ]);

    Livewire::test(InlineTranslation::class, ['translationKey' => 'website.welcome'])
        ->assertSet('translationValue', 'Welcome from database')
        ->assertSee('Welcome from database');
});

it('prioritizes database translation over language file', function () {
    Translation::create([
        'locale' => 'en',
        'group' => 'website',
        'key' => 'welcome',
        'value' => 'Database wins',
    ]);

    Livewire::test(InlineTranslation::class, ['translationKey' => 'website.welcome'])
        ->assertSet('translationValue', 'Database wins')
        ->assertDontSee('Welcome from language file');
});

it('can open modal', function () {
    Livewire::test(InlineTranslation::class, ['translationKey' => 'website.welcome'])
        ->assertSet('showModal', false)
        ->call('openModal')
        ->assertSet('showModal', true);
});

it('can close modal', function () {
    Livewire::test(InlineTranslation::class, ['translationKey' => 'website.welcome'])
        ->call('openModal')
        ->assertSet('showModal', true)
        ->call('closeModal')
        ->assertSet('showModal', false);
});

it('can save translation to database', function () {
    Livewire::test(InlineTranslation::class, ['translationKey' => 'website.welcome'])
        ->set('translationValue', 'New translation value')
        ->call('save');

    $this->assertDatabaseHas('translations', [
        'locale' => 'en',
        'group' => 'website',
        'key' => 'welcome',
        'value' => 'New translation value',
    ]);
});

it('closes modal after saving', function () {
    Livewire::test(InlineTranslation::class, ['translationKey' => 'website.welcome'])
        ->call('openModal')
        ->set('translationValue', 'New value')
        ->call('save')
        ->assertSet('showModal', false);
});

it('updates existing translation when saving', function () {
    Translation::create([
        'locale' => 'en',
        'group' => 'website',
        'key' => 'welcome',
        'value' => 'Old value',
    ]);

    Livewire::test(InlineTranslation::class, ['translationKey' => 'website.welcome'])
        ->set('translationValue', 'Updated value')
        ->call('save');

    expect(Translation::count())->toBe(1);
    expect(Translation::first()->value)->toBe('Updated value');
});

it('handles invalid translation key format gracefully', function () {
    Livewire::test(InlineTranslation::class, ['translationKey' => 'invalid'])
        ->assertSet('translationValue', 'invalid');
});

it('does not save with invalid translation key format', function () {
    Livewire::test(InlineTranslation::class, ['translationKey' => 'invalid'])
        ->set('translationValue', 'Some value')
        ->call('save');

    expect(Translation::count())->toBe(0);
});

it('shows editable version for authorized users', function () {
    // Create a mock user
    $user = new class {
        public $id = 1;
    };

    // Mock the staff guard
    Auth::shouldReceive('guard')
        ->with('staff')
        ->andReturnSelf();

    Auth::shouldReceive('check')
        ->andReturn(true);

    Livewire::test(InlineTranslation::class, ['translationKey' => 'website.welcome'])
        ->assertSee('wire:click="openModal"');
});

it('shows read-only version for unauthorized users', function () {
    Auth::shouldReceive('guard')
        ->with('staff')
        ->andReturnSelf();

    Auth::shouldReceive('check')
        ->andReturn(false);

    Livewire::test(InlineTranslation::class, ['translationKey' => 'website.welcome'])
        ->assertDontSee('wire:click="openModal"');
});

it('refreshes translation value when opening modal', function () {
    $component = Livewire::test(InlineTranslation::class, ['translationKey' => 'website.welcome'])
        ->assertSet('translationValue', 'Welcome from language file');

    // Create database translation
    Translation::create([
        'locale' => 'en',
        'group' => 'website',
        'key' => 'welcome',
        'value' => 'New database value',
    ]);

    // Open modal should refresh the value
    $component->call('openModal')
        ->assertSet('translationValue', 'New database value');
});
