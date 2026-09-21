---
title: "Configuration"
nav_order: 4
description: "The two settings of darvis/livewire-inline-translation, how to choose the guard that may edit, and how to narrow editing to a permission or limit the HTML."
---

# Configuration

## The two settings

| Config key | Env variable | Default | What it does |
| --- | --- | --- | --- |
| `inline-translation.guard` | `INLINE_TRANSLATION_GUARD` | `web` | The authentication guard that decides who may edit. |
| `inline-translation.modal_container_id` | none | `inline-translation-modals` | The `id` of the element in your layout that the modal is moved into. |

An empty value, or a value that is not a string, falls back to the default.

Set the guard in `.env`. For the container id you publish the config file first:

```bash
php artisan vendor:publish --tag=inline-translation-config
```

`config/inline-translation.php`:

```php
<?php

return [
    'guard' => env('INLINE_TRANSLATION_GUARD', 'web'),

    'modal_container_id' => 'site-modals',
];
```

The element in your layout has to carry the same id: `<div id="site-modals"></div>`.

When your configuration is cached, run `php artisan config:clear` (or `php artisan config:cache` again) after every change to `.env` or the config file.

## Choose the guard

A guard is how Laravel knows who is logged in. Every guard of your application is defined under `guards` in `config/auth.php`; the [Laravel authentication documentation](https://laravel.com/docs/authentication#adding-custom-guards) explains how to add one.

The package asks one question: is someone logged in on the configured guard? If so, that person may edit every translation on the site.

| Your situation | Setting |
| --- | --- |
| Only staff can log in to the site | keep `web` |
| Customers log in on `web`, staff on a guard of their own | `INLINE_TRANSLATION_GUARD=staff`, with the name of that guard |
| Staff and customers share one guard | keep that guard and [let only some users edit](#let-only-some-users-edit) |

A guard name that is not defined in `config/auth.php` does not raise an error. Nobody can edit, and the pages keep showing their text.

## Let only some users edit

The component decides in one protected method, `isAuthorized()`. The view asks it to decide whether to draw the underline, and `openModal()` and `save()` ask it before they do anything; they answer with a 403 response when it returns `false`. Extend the component and override that method.

`app/Livewire/EditorsInlineTranslation.php`:

```php
<?php

namespace App\Livewire;

use Darvis\LivewireInlineTranslation\InlineTranslation;

class EditorsInlineTranslation extends InlineTranslation
{
    protected function isAuthorized(): bool
    {
        return parent::isAuthorized()
            && auth()->user()?->can('edit-translations') === true;
    }
}
```

`parent::isAuthorized()` keeps the guard check. `can('edit-translations')` asks a [gate or policy](https://laravel.com/docs/authorization#gates) that you define yourself. `auth()->user()` is the user of the default guard; use `Auth::guard('staff')->user()` when your editors are on another guard.

Register the class under the name of the package component, so that every `<livewire:inline-translation>` tag uses it.

`app/Providers/AppServiceProvider.php`:

```php
use App\Livewire\EditorsInlineTranslation;
use Livewire\Livewire;

public function boot(): void
{
    Livewire::component('inline-translation', EditorsInlineTranslation::class);
}
```

Two rules for a subclass:

- Do not override `render()` to change who may edit. That only hides the underline, and an override without the return type `Illuminate\Contracts\View\View` is a fatal error.
- The browser can call every public method of a Livewire component. A public action you add yourself has to start with `$this->authorizeEditing();`.

## Limit the HTML an editor may store

The package stores what the editor sends and renders it as HTML. To allow only a few tags, clean the value in the same subclass before it is saved:

```php
public function save(): void
{
    $this->translationValue = strip_tags(
        $this->translationValue,
        '<b><i><strong><em><ul><li><br>'
    );

    parent::save();
}
```

`parent::save()` does the authorisation check and writes the row. `strip_tags()` removes tags but leaves attributes on the tags you allow, so for untrusted editors use an HTML sanitiser package instead.

## Change the look or the language of the modal

The modal uses inline styles and fixed English labels ("Edit Translation", "Key", "Translation", "Cancel", "Save"). Publish the view to change them:

```bash
php artisan vendor:publish --tag=inline-translation-views
```

Edit `resources/views/vendor/inline-translation/inline-translation.blade.php`. Keep the `wire:click` and `wire:model` attributes and the `x-teleport` line as they are. After a package upgrade, compare your copy with the package view; a published view is not updated for you.

## Next

- [How it works](how-it-works.md): what the component does on each request
- [Troubleshooting](troubleshooting.md): when the underline or the modal does not show
