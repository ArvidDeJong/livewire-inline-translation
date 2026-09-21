---
title: "Testing"
nav_order: 7
description: "Test an editable translation in your own Laravel application with Livewire::test(): the editor flow, the refused visitor, another guard and another locale."
---

# Testing

The package calls nothing outside your application. A test needs a database with the `translations` table and nothing else. The `RefreshDatabase` trait runs the package migration together with your own.

The examples use [Pest](https://pestphp.com); in a PHPUnit class the same calls work inside a test method.

## A complete test

`tests/Feature/InlineTranslationTest.php`:

```php
<?php

use App\Models\User;
use Darvis\LivewireInlineTranslation\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('lets an editor change the welcome text', function () {
    $this->actingAs(User::factory()->create(), 'web');

    Livewire::test('inline-translation', ['translationKey' => 'website.welcome'])
        ->assertSeeHtml('wire:click="openModal"')
        ->call('openModal')
        ->set('translationValue', 'Hello there')
        ->call('save')
        ->assertSet('showModal', false)
        ->assertSee('Hello there');

    expect(Translation::getTranslation('en', 'website', 'welcome'))->toBe('Hello there');
});

it('refuses a save from a visitor who is not logged in', function () {
    Livewire::test('inline-translation', ['translationKey' => 'website.welcome'])
        ->assertDontSeeHtml('wire:click="openModal"')
        ->set('translationValue', '<script>alert(1)</script>')
        ->call('save')
        ->assertForbidden();

    expect(Translation::count())->toBe(0);
});
```

The first test logs someone in on the `web` guard, checks that the underline with its click handler is there, saves a text and reads it back from the table. The second test does the same without logging in: the underline is missing and `save` is answered with 403.

Keep the second test. It fails on a package version before 1.3.1, and it fails when a subclass of yours adds a public action without `$this->authorizeEditing()`.

## Things that go wrong in a test

- **`Cannot update locked property: [translationKey]`.** The key and `html` are locked. Pass them as mount parameters, the second argument of `Livewire::test()`, not with `->set()`.
- **`assertSee('wire:click="openModal"')` never matches.** `assertSee()` escapes its argument. Use `assertSeeHtml()`, or pass `false` as the second argument.
- **Your subclass is not tested.** When you registered your own class as `inline-translation`, test by that name, as above. `Livewire::test(InlineTranslation::class)` tests the package class and skips your `isAuthorized()`.
- **The text is the key.** The test application has no language line for the key. Add one in the test: `app('translator')->addLines(['website.welcome' => 'Welcome'], 'en');`.

## Another guard

Set the guard in the test and log in on it. The guard has to exist under `auth.guards`, or nobody may edit.

```php
it('shows the underline to staff only', function () {
    config(['inline-translation.guard' => 'staff']);

    $this->actingAs(User::factory()->create(), 'web');

    Livewire::test('inline-translation', ['translationKey' => 'website.welcome'])
        ->assertDontSeeHtml('wire:click="openModal"');
});
```

This example assumes `config/auth.php` of your application defines a `staff` guard.

## Another locale

```php
it('stores the text for the current locale', function () {
    $this->actingAs(User::factory()->create(), 'web');
    app()->setLocale('nl');

    Livewire::test('inline-translation', ['translationKey' => 'website.welcome'])
        ->set('translationValue', 'Welkom')
        ->call('save');

    expect(Translation::getTranslation('nl', 'website', 'welcome'))->toBe('Welkom')
        ->and(Translation::getTranslation('en', 'website', 'welcome'))->toBeNull();
});
```

## Test a whole page

A normal HTTP test works too. It shows that the tag is in the right view and that a visitor gets the stored text:

```php
it('shows the edited text on the about page', function () {
    Translation::setTranslation('en', 'website', 'about_title', 'Who we are');

    $this->get('/about')->assertOk()->assertSee('Who we are');
});
```

`setTranslation()` writes the row directly, without an authorisation check, which makes it the way to prepare data in a test or a seeder.
