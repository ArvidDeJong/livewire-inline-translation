---
name: livewire-inline-translation-development
description: Work with darvis/livewire-inline-translation. Use it to make a translation editable on the page, decide who may edit it, narrow editing to a permission, read the stored value outside the component, keep the locale right on a Livewire update, and test all of it.
---

# darvis/livewire-inline-translation development

## When to use this skill

Use this skill when a page in an application that has `darvis/livewire-inline-translation` installed shows a text through `<livewire:inline-translation>`, when you decide who may edit such a text, when an edited text does not show up (or shows up in the wrong language), when you read or write the `translations` table, or when you write tests around an editable translation.

## How a translation is rendered and saved

1. The service provider registers the Livewire component `inline-translation` (`Darvis\LivewireInlineTranslation\InlineTranslation`), loads the migration for the `translations` table and the view namespace `inline-translation`.
2. `mount(string $translationKey, bool $html = false)` splits the key on the **first** dot: `website.hero.title` becomes group `website` and key `hero.title`.
3. The value is looked up with `Translation::getTranslation($locale, $group, $key)` for `app()->getLocale()`. A row wins; without a row the component falls back to `__($translationKey)`. That is one query per component on the page, without a cache.
4. `render()` passes `isAuthorized` to the view: true when the configured guard is defined in `auth.guards` and `Auth::guard($guard)->check()` passes. An authorised user gets a dashed underline with `wire:click="openModal"`; everyone else gets the value inside a plain `<span>`.
5. `openModal()` reads the value again and sets `showModal`. The modal is teleported with Alpine into `#<modal_container_id>`.
6. `save()` writes `translationValue` with `Translation::setTranslation()` (an `updateOrCreate` on `locale`, `group`, `key`) for the locale of **that** request, and closes the modal.

Nothing in the package throws on purpose, logs or dispatches an event. This is what each situation gives you:

| Situation | What you get | Exception | Log line |
| --- | --- | --- | --- |
| Key without a dot (`welcome`) | the key itself is rendered; `save()` returns without writing and leaves the modal open | none | none |
| No row and no language line | `__()` returns the key, so the key is rendered | none | none |
| The key points at an array in the language file (`website.hero`) | the page fails | `TypeError`: `getTranslation(): Return value must be of type string, array returned` | none |
| Guard from the config is not defined in `auth.guards` | the text renders, nobody gets the edit affordance | none | none |
| `guard` or `modal_container_id` is empty or not a string | the defaults `web` and `inline-translation-modals` | none | none |
| `php artisan migrate` has not run | every page with the component fails | `Illuminate\Database\QueryException` (no `translations` table) | none |
| The host app already has a `translations` table | `php artisan migrate` fails on `Schema::create('translations')` | `QueryException` (table already exists) | none |
| An empty value is saved | a row with `''`; it wins over the language file, so the text disappears and there is nothing left to click | none | none |
| No element with the container id in the layout | `showModal` is true, but the modal has nowhere to go and stays invisible | none | none |

## Making a text editable

```blade
{{-- layout, once, just before the closing body tag --}}
<div id="inline-translation-modals"></div>

{{-- plain text: a textarea in the modal --}}
<h1><livewire:inline-translation translation-key="website.welcome" /></h1>

{{-- the small editor with bold, italic and a bullet list --}}
<livewire:inline-translation translation-key="website.intro" :html="true" />
```

The table comes with `php artisan migrate`; there is no migration to publish. The publish tags are `inline-translation-config` and `inline-translation-views`.

## Pitfalls

- **The guard is checked in `openModal()` and `save()`, not only in the view.** Both answer 403 for a visitor who may not edit, and `translationKey` and `html` are `#[Locked]`, so the browser cannot point the component at another key. This is so since 1.3.1; on an older version the actions were open to every visitor, so upgrade before anything else. When you add a public action in a subclass, call `$this->authorizeEditing()` first: the browser can call every public method of a Livewire component.
- The guard is the only check the package makes. Everyone who is logged in on it may edit every key. Narrow that with a subclass, see below.
- `html` only switches the editor. Both modes render the value unescaped, so a `<` typed in the textarea is HTML too. An editor can therefore put any markup on the page; give the guard only to people you trust with that, or strip tags in a subclass.
- The component renders a `<span>` with Livewire attributes. It cannot be used inside an attribute, `<title>`, a meta tag or a mail subject, and for an authorised user the click opens the modal, so keep it out of `<a>`, `<button>` and `<label>`.
- Only the component reads the database. `__('website.welcome')`, `@lang` and `trans()` elsewhere (mails, meta tags, other pages) keep returning the language file. Read the stored value yourself where you need it, see below.
- The locale is `app()->getLocale()` at the time of the request, and `save()` runs on Livewire's update request. A locale that is set by route middleware (a URL prefix, the session) is not set there unless that middleware is persistent, so the edit lands under the default locale and the text re-renders in the default language. Register it in a service provider: `Livewire::addPersistentMiddleware([\App\Http\Middleware\SetLocale::class]);`.
- The HTML editor pushes its content to `translationValue` 500 ms after the last input. A click on Save inside that window stores the previous value.
- The editor strips `<div>` tags and turns `</div>` into `<br>`. Other tags, such as `<p>` from pasted text, are stored as they are.
- JSON translation strings (keys without a dot) are not supported. Column sizes: `locale` 10, `group` 100, `key` 255 characters.
- A database row keeps winning after the language file changes. Delete the row to fall back to the file.
- The labels in the modal (`Edit Translation`, `Key`, `Translation`, `Cancel`, `Save`) are fixed English text in the view. Publish the views to change them.
- Livewire ships Alpine. Do not load a second Alpine for the modal.

## Narrowing who may edit, and what they may store

`isAuthorized()` is protected and is what `openModal()`, `save()` and the view all ask. Override that method, not `render()`: `render()` declares `Illuminate\Contracts\View\View` as its return type, and an override without that return type is a fatal error.

```php
namespace App\Livewire;

use Darvis\LivewireInlineTranslation\InlineTranslation;

class GuardedInlineTranslation extends InlineTranslation
{
    public function save(): void
    {
        $this->translationValue = strip_tags($this->translationValue, '<b><i><strong><em><ul><li><br>');

        parent::save();
    }

    protected function isAuthorized(): bool
    {
        return parent::isAuthorized() && auth()->user()?->can('edit-translations') === true;
    }
}
```

`parent::save()` does the 403 check, so stripping the tags first is harmless for a visitor who is refused anyway.

Register it under the same name in `AppServiceProvider::boot()`, so every existing tag uses it. The package provider is discovered before the application's providers, so this registration is the later one:

```php
use App\Livewire\GuardedInlineTranslation;
use Livewire\Livewire;

Livewire::component('inline-translation', GuardedInlineTranslation::class);
```

`auth()->user()` is the default guard; use `Auth::guard(...)` when the editors are on another one.

## Reading and writing a value yourself

```php
use Darvis\LivewireInlineTranslation\Models\Translation;

// The same lookup the component does.
$value = Translation::getTranslation(app()->getLocale(), 'website', 'welcome') ?? __('website.welcome');

// Seed or import a value; updates the row when it exists.
Translation::setTranslation('nl', 'website', 'welcome', 'Welkom');

// Back to the language file.
Translation::where('locale', 'nl')->where('group', 'website')->where('key', 'welcome')->delete();
```

`getTranslation()` returns `null` when there is no row, `setTranslation()` returns the model. Escape the value with `e()` or the double curly braces when it goes somewhere that must not contain HTML.

## Settings

Two settings, under the config key `inline-translation`: `guard` (env `INLINE_TRANSLATION_GUARD`, default `web`) and `modal_container_id` (default `inline-translation-modals`, no env variable). Read them through `Darvis\LivewireInlineTranslation\Support\InlineTranslationConfig`: `guard()` and `modalContainerId()`, which also hold the defaults.

```blade
@use('Darvis\LivewireInlineTranslation\Support\InlineTranslationConfig')

<div id="{{ InlineTranslationConfig::modalContainerId() }}"></div>
```

## Testing

There is no external API; tests need a database with the `translations` table (`RefreshDatabase` runs the package migration) and nothing else.

```php
use App\Models\User;
use Darvis\LivewireInlineTranslation\Models\Translation;
use Livewire\Livewire;

it('lets an editor change the welcome text', function () {
    $this->actingAs(User::factory()->create(), 'web');

    Livewire::test('inline-translation', ['translationKey' => 'website.welcome'])
        ->assertSeeHtml('wire:click="openModal"')
        ->call('openModal')
        ->set('translationValue', 'Hello there')
        ->call('save')
        ->assertSet('showModal', false);

    expect(Translation::getTranslation('en', 'website', 'welcome'))->toBe('Hello there');
});

it('refuses a save from a visitor', function () {
    Livewire::test('inline-translation', ['translationKey' => 'website.welcome'])
        ->assertDontSeeHtml('wire:click="openModal"')
        ->set('translationValue', '<script>alert(1)</script>')
        ->call('save')
        ->assertForbidden();

    expect(Translation::count())->toBe(0);
});
```

- Keep the second test in the host app: it fails on a package version before 1.3.1, and it catches a subclass that adds an action without `authorizeEditing()`.
- `->set('translationKey', ...)` throws `Cannot update locked property`; pass the key as a mount parameter.
- When you registered a subclass, test by the name `inline-translation`, not by the package class, or the test bypasses your subclass.
- `assertSee()` escapes its needle. Use `assertSeeHtml()` or `assertSee('wire:click="openModal"', false)` for markup.
- Another guard: `config(['inline-translation.guard' => 'staff'])` together with `actingAs($user, 'staff')`. The guard has to exist in `auth.guards`, or nobody may edit.
- Another locale: call `app()->setLocale('nl')` before `Livewire::test()` and assert on the row for `nl`.
- Give the language line a value with `app('translator')->addLines(['website.welcome' => 'Welcome'], 'en')` when the test app has no language file for it.
