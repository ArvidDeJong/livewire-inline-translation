---
title: "Installation"
nav_order: 2
description: "Install darvis/livewire-inline-translation step by step: composer, the migration, the modal container in your layout, the guard, and a check that it works."
---

# Installation

## Requirements

| What | Version |
| --- | --- |
| PHP | 8.2 or higher |
| Laravel | 11, 12 or 13 |
| Livewire | 3 or 4 |
| Database | any database Laravel supports; the edited texts are stored there |

Livewire brings Alpine.js, which the modal uses. Do not load a second copy of Alpine.

## Step 1: require the package

```bash
composer require darvis/livewire-inline-translation
```

Laravel discovers the service provider by itself. It registers the Livewire component `inline-translation`, the views and the migration.

## Step 2: create the table

```bash
php artisan migrate
```

This creates the `translations` table. The migration comes with the package; there is nothing to publish for it.

If your application already has a table called `translations`, this step fails. See [Troubleshooting](troubleshooting.md#php-artisan-migrate-says-the-table-already-exists).

## Step 3: add the modal container to your layout

The edit modal is moved ("teleported") by Alpine into one element in your layout, so that it is not cut off by the element the text sits in. Add that element once, before the closing body tag.

`resources/views/components/layouts/app.blade.php` (or the layout your pages use):

```blade
<body>
    ...

    <div id="inline-translation-modals"></div>
</body>
```

Livewire injects its own scripts and styles into every page that contains a Livewire component. Only when you turned that off (`inject_assets` in `config/livewire.php`) do you add `@livewireStyles` and `@livewireScripts` to the layout yourself, as the [Livewire installation guide](https://livewire.laravel.com/docs/installation) describes.

## Step 4: decide who may edit

A guard is the part of Laravel that decides who is logged in; the guards of your application are listed under `guards` in `config/auth.php`. The package lets everyone edit who is logged in on one guard.

The default is `web`, the guard every Laravel application has. **With that default every logged in user of your site may edit every translation.** That is fine when only staff can log in. When customers can log in too, point the package at a guard that only your editors use.

`.env`:

```env
INLINE_TRANSLATION_GUARD=staff
```

The value is the name of a guard from `config/auth.php`. A name that is not defined there means nobody may edit; the pages keep working. Run `php artisan config:clear` after changing `.env` when your configuration is cached.

[Configuration](configuration.md) explains how to narrow editing further, for example to users with a permission.

## Step 5: make one text editable

`lang/en/website.php`:

```php
<?php

return [
    'welcome' => 'Welcome to our website',
];
```

`resources/views/welcome.blade.php`:

```blade
<h1><livewire:inline-translation translation-key="website.welcome" /></h1>
```

## Check that it works

1. Run `php artisan migrate:status`. The list has to contain this line:

   ```text
   2026_01_26_000000_create_translations_table ................ [1] Ran
   ```

   The number between the brackets can differ.

2. Open the page while you are logged out. You see "Welcome to our website" as ordinary text.
3. Log in on the guard from step 4 and open the page again. The text has a dashed blue underline.
4. Click the text. A modal with the title "Edit Translation" opens, with the key `website.welcome` and a text field. Change the text and click "Save". The modal closes and the page shows the new text.
5. Run `php artisan tinker` and ask for the stored value:

   ```php
   Darvis\LivewireInlineTranslation\Models\Translation::getTranslation('en', 'website', 'welcome');
   ```

   It returns the text you saved. Use your own locale instead of `en` when `app()->getLocale()` is something else.

No underline in step 3, or no modal in step 4? [Troubleshooting](troubleshooting.md) starts with exactly those two.

## Optional: publish the config or the view

```bash
php artisan vendor:publish --tag=inline-translation-config
php artisan vendor:publish --tag=inline-translation-views
```

The first command creates `config/inline-translation.php`; you only need it to change the id of the modal container, because the guard has an env variable. The second copies the view to `resources/views/vendor/inline-translation/inline-translation.blade.php`, where you can restyle the modal or translate its labels. A published view does not follow later package updates, so compare it with the package view after an upgrade.

## Laravel Boost

The package ships [Laravel Boost](https://laravel.com/docs/boost) resources: a guideline and a `livewire-inline-translation-development` skill, so an AI assistant in your project knows how the component looks up, renders and saves a translation. Run `php artisan boost:install`, or `php artisan boost:update --discover` in a project that already uses Boost.

## Next

- [Usage](usage.md): a complete example, the HTML editor and several locales
- [Configuration](configuration.md): the two settings and narrowing who may edit
