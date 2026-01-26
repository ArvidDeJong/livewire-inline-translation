<?php

use Darvis\LivewireInlineTranslation\InlineTranslation;
use Livewire\Livewire;

it('registers the livewire component', function () {
    expect(Livewire::getClass('inline-translation'))
        ->toBe(InlineTranslation::class);
});

it('loads the package views', function () {
    expect(view()->exists('inline-translation::inline-translation'))
        ->toBeTrue();
});

it('merges package config', function () {
    expect(config('inline-translation.guard'))
        ->toBe('staff')
        ->and(config('inline-translation.modal_container_id'))
        ->toBe('inline-translation-modals');
});

it('can override config with environment variable', function () {
    config(['inline-translation.guard' => 'web']);

    expect(config('inline-translation.guard'))
        ->toBe('web');
});
