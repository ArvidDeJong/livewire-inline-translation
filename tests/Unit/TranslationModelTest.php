<?php

use Darvis\LivewireInlineTranslation\Models\Translation;

it('can create a translation', function () {
    $translation = Translation::create([
        'locale' => 'en',
        'group' => 'website',
        'key' => 'welcome',
        'value' => 'Welcome to our platform!',
    ]);

    expect($translation)->toBeInstanceOf(Translation::class)
        ->and($translation->locale)->toBe('en')
        ->and($translation->group)->toBe('website')
        ->and($translation->key)->toBe('welcome')
        ->and($translation->value)->toBe('Welcome to our platform!');
});

it('can retrieve a translation using getTranslation', function () {
    Translation::create([
        'locale' => 'en',
        'group' => 'website',
        'key' => 'welcome',
        'value' => 'Welcome!',
    ]);

    $value = Translation::getTranslation('en', 'website', 'welcome');

    expect($value)->toBe('Welcome!');
});

it('returns null when translation does not exist', function () {
    $value = Translation::getTranslation('en', 'website', 'nonexistent');

    expect($value)->toBeNull();
});

it('can save a translation using setTranslation', function () {
    $translation = Translation::setTranslation('en', 'website', 'welcome', 'Hello World!');

    expect($translation)->toBeInstanceOf(Translation::class)
        ->and($translation->value)->toBe('Hello World!');

    $this->assertDatabaseHas('translations', [
        'locale' => 'en',
        'group' => 'website',
        'key' => 'welcome',
        'value' => 'Hello World!',
    ]);
});

it('updates existing translation when using setTranslation', function () {
    Translation::create([
        'locale' => 'en',
        'group' => 'website',
        'key' => 'welcome',
        'value' => 'Old value',
    ]);

    Translation::setTranslation('en', 'website', 'welcome', 'New value');

    expect(Translation::count())->toBe(1);

    $translation = Translation::first();
    expect($translation->value)->toBe('New value');
});

it('enforces unique constraint on locale, group, and key', function () {
    Translation::create([
        'locale' => 'en',
        'group' => 'website',
        'key' => 'welcome',
        'value' => 'First',
    ]);

    Translation::create([
        'locale' => 'en',
        'group' => 'website',
        'key' => 'welcome',
        'value' => 'Second',
    ]);
})->throws(\Illuminate\Database\QueryException::class);

it('allows same key for different locales', function () {
    Translation::create([
        'locale' => 'en',
        'group' => 'website',
        'key' => 'welcome',
        'value' => 'Welcome!',
    ]);

    Translation::create([
        'locale' => 'nl',
        'group' => 'website',
        'key' => 'welcome',
        'value' => 'Welkom!',
    ]);

    expect(Translation::count())->toBe(2);
});

it('allows same key for different groups', function () {
    Translation::create([
        'locale' => 'en',
        'group' => 'website',
        'key' => 'title',
        'value' => 'Website Title',
    ]);

    Translation::create([
        'locale' => 'en',
        'group' => 'app',
        'key' => 'title',
        'value' => 'App Title',
    ]);

    expect(Translation::count())->toBe(2);
});
