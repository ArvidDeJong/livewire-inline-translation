<?php

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

it('registers the livewire component under its name', function () {
    // Livewire 3 and 4 disagree about how to look a component class up by name,
    // so ask the only question that matters: does rendering it by name work?
    Livewire::test('inline-translation', ['translationKey' => 'website.welcome'])
        ->assertOk()
        ->assertSet('translationKey', 'website.welcome');
});

it('loads the package views', function () {
    expect(view()->exists('inline-translation::inline-translation'))
        ->toBeTrue();
});

it('merges package config, so a host app needs no published config', function () {
    expect(config('inline-translation.guard'))
        ->toBe('web')
        ->and(config('inline-translation.modal_container_id'))
        ->toBe('inline-translation-modals');
});

it('publishes the views and the config, but not a migration', function () {
    $groups = ServiceProvider::$publishGroups;

    expect($groups)->toHaveKeys(['inline-translation-views', 'inline-translation-config'])
        ->and($groups)->not->toHaveKey('inline-translation-migrations');
});

it('registers the migration path, so migrate creates the table without publishing', function () {
    $paths = array_map(fn (string $path): string => (string) realpath($path), app('migrator')->paths());

    expect($paths)->toContain((string) realpath(__DIR__.'/../../database/migrations'))
        ->and(glob(__DIR__.'/../../database/migrations/*'))->toHaveCount(1);
});
