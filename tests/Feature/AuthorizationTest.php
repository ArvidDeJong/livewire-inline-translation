<?php

use Darvis\LivewireInlineTranslation\InlineTranslation;
use Darvis\LivewireInlineTranslation\Models\Translation;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

/**
 * The component is on the page for every visitor, and a visitor can call any public action of a
 * Livewire component. So the guard has to be checked where the writing happens, not only where
 * the underline is drawn.
 */
beforeEach(function () {
    app('translator')->addLines(['website.welcome' => 'Welcome'], 'en');
});

it('refuses to save for a visitor who is not logged in', function () {
    Livewire::test(InlineTranslation::class, ['translationKey' => 'website.welcome'])
        ->set('translationValue', '<script>alert(1)</script>')
        ->call('save')
        ->assertForbidden();

    expect(Translation::count())->toBe(0);
});

it('refuses to open the modal for a visitor who is not logged in', function () {
    Livewire::test(InlineTranslation::class, ['translationKey' => 'website.welcome'])
        ->call('openModal')
        ->assertForbidden();
});

it('refuses to save when the configured guard does not exist', function () {
    config(['inline-translation.guard' => 'typo']);

    Livewire::test(InlineTranslation::class, ['translationKey' => 'website.welcome'])
        ->set('translationValue', 'New value')
        ->call('save')
        ->assertForbidden();

    expect(Translation::count())->toBe(0);
});

it('saves for someone logged in on the configured guard', function () {
    Auth::shouldReceive('guard')->with('web')->andReturnSelf();
    Auth::shouldReceive('check')->andReturn(true);

    Livewire::test(InlineTranslation::class, ['translationKey' => 'website.welcome'])
        ->set('translationValue', 'New value')
        ->call('save')
        ->assertOk();

    expect(Translation::first()->value)->toBe('New value');
});

it('does not let the browser change which key is edited', function () {
    Livewire::test(InlineTranslation::class, ['translationKey' => 'website.welcome'])
        ->set('translationKey', 'auth.failed');
})->throws(Exception::class, 'Cannot update locked property: [translationKey]');

it('does not let the browser switch the component to html mode', function () {
    Livewire::test(InlineTranslation::class, ['translationKey' => 'website.welcome'])
        ->set('html', true);
})->throws(Exception::class, 'Cannot update locked property: [html]');
