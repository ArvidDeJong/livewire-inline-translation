---
title: "Troubleshooting"
nav_order: 8
description: "Symptom, cause and fix for darvis/livewire-inline-translation: no underline, no modal, a 403 on save, the wrong locale and translations table errors."
---

# Troubleshooting

The package itself throws no exceptions and writes no log lines. The error messages on this page come from PHP, Laravel, Livewire or Alpine, and are quoted as they appear.

## The text has no underline although I am logged in

The package does not consider you an editor. Check in this order:

1. **You are logged in on another guard.** Run `php artisan tinker` and look at the setting:

   ```php
   Darvis\LivewireInlineTranslation\Support\InlineTranslationConfig::guard();
   ```

   The default is `web`. You have to be logged in on exactly that guard.
2. **The guard name is not defined.** The name has to be a key under `guards` in `config/auth.php`. An unknown name does not give an error; it means nobody may edit.
3. **The config is cached.** After changing `.env` or `config/inline-translation.php`, run `php artisan config:clear`.
4. **A subclass says no.** When you overrode `isAuthorized()`, that method decides. See [Configuration](configuration.md#let-only-some-users-edit).

## I click the text and no modal appears

The modal is moved into the element with the id from `modal_container_id`, `inline-translation-modals` by default. Open the browser console. This warning means that the element is missing:

```text
Alpine Warning: Cannot find x-teleport element for selector: "#inline-translation-modals"
```

- Add `<div id="inline-translation-modals"></div>` to the layout of this page, before the closing body tag.
- When you changed `modal_container_id`, the element has to carry the new id, and a cached config has to be cleared.
- When you published the view before version 1.3.0, your copy still has the id `inline-translation-modals` written into it. Compare `resources/views/vendor/inline-translation/inline-translation.blade.php` with the view in the package.

## The modal is cut off or sits behind other elements

The container sits inside an element with `overflow: hidden`, a `transform` or its own `z-index`. Move the container so that it is a direct child of the body, at the end.

## Opening the modal or saving gives a 403

`openModal()` and `save()` answer with HTTP 403 when the visitor may not edit. For someone who saw the underline this means the login ended in the meantime: the session expired, or they logged out in another tab. Log in again and reload the page.

In a test, a 403 means the test did not log in on the configured guard; see [Testing](testing.md).

## The edit is saved, but the page shows another language or the old text

The value was stored under another locale than the page uses. Saving runs in Livewire's own update request, and the middleware that sets your locale did not run there, so the edit went to the default locale. Look in the table:

```php
Darvis\LivewireInlineTranslation\Models\Translation::where('key', 'welcome')->get(['locale', 'group', 'key', 'value']);
```

Register your locale middleware as persistent middleware, as shown under [Several locales](usage.md#several-locales), and delete the row that ended up under the wrong locale.

## The HTML editor saved the text without my last change

The editor sends its content to the server 500 ms after the last input. A click on "Save" within that half second stores the previous content. Wait a moment after typing, then save.

## The text disappeared after saving an empty value

An empty value is stored as an empty text, and a stored text wins over the language file. There is nothing left to click. Delete the row; see [Go back to the language file](usage.md#go-back-to-the-language-file).

## I changed the language file and the page still shows the old text

Someone edited this text before, so there is a row in the `translations` table, and the row wins. Delete the row.

## `__()` still returns the old text after an edit

That is how the package works: only the component reads the database. See [Read the stored value somewhere else](usage.md#read-the-stored-value-somewhere-else).

## The page shows the key, such as website.welcome

There is no row and no language line for this key in the current locale, so Laravel returns the key. Add the line to `lang/<locale>/website.php`. A key without a dot, such as `welcome`, is always shown as it is, and cannot be saved.

## The page fails with "Return value must be of type string, array returned"

```text
Darvis\LivewireInlineTranslation\InlineTranslation::getTranslation(): Return value must be of type string, array returned
```

The key points at an array in the language file instead of a text. With `'hero' => ['title' => '...']` in `lang/en/website.php`, use `website.hero.title`, not `website.hero`.

## The page fails with an error about the translations table

SQLite and MySQL word it differently:

```text
SQLSTATE[HY000]: General error: 1 no such table: translations
SQLSTATE[42S02]: Base table or view not found: 1146 Table 'your_database.translations' doesn't exist
```

The migration has not run on this database. Run `php artisan migrate`, also on the server after a deploy.

## `php artisan migrate` says the table already exists

```text
SQLSTATE[42S01]: Base table or view already exists: 1050 Table 'translations' already exists
```

Your application, or another package, already has a table called `translations`. The table name of this package is fixed; it cannot be configured. The two cannot share one database unless the other table gets another name.

## "Unable to find component: [inline-translation]"

Livewire does not know the component, which means the service provider of the package is not loaded.

- Run `composer install`, then `php artisan package:discover`.
- Check that `composer.json` of your application does not list `darvis/livewire-inline-translation` under `extra.laravel.dont-discover`.
- Run `php artisan optimize:clear` to drop cached package and config files.

## "Auth guard [user] is not defined."

You are on version 1.2.0 or older, where the default guard was one that a standard Laravel application does not have. Upgrade to the latest version; since 1.3.0 the default is `web` and an unknown guard no longer throws.

## "Cannot update locked property: [translationKey]"

Code, a test or a browser tried to change `translationKey` or `html` after the component was mounted. Both are locked since 1.3.1. Set them on the tag, or as mount parameters in a test.

## My custom component fails with "must be compatible with ... render(): Illuminate\Contracts\View\View"

Your subclass overrides `render()` without the return type. Older versions of these docs showed that. Remove the override and put your rule in `isAuthorized()`; see [Configuration](configuration.md#let-only-some-users-edit).

## Still stuck

Open an [issue](https://github.com/ArvidDeJong/livewire-inline-translation/issues/new/choose) with the Blade tag, the translation key, your `guard` setting and the versions of Laravel and Livewire. For a security problem, follow the [security policy](https://github.com/ArvidDeJong/livewire-inline-translation/blob/main/SECURITY.md) instead.
