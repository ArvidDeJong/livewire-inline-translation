---
title: Home
nav_order: 1
description: "Edit translations inline on the page in a Laravel Livewire app: an authorised user clicks the text, a modal opens and the new value is stored in the database."
permalink: /
---

# Livewire Inline Translation

A client who wants the wording on a page changed normally sends you an email. This package lets them do it themselves, in the page, without a CMS behind it.

Render a translation as a component instead of with `__()`. Someone logged in on the guard you configure sees a dashed underline under the text, clicks it, edits it in a modal and saves. Everyone else sees ordinary text, with no markup that gives away that it is editable.

```bash
composer require darvis/livewire-inline-translation
php artisan migrate
```

Requires PHP 8.2+, Laravel 11, 12 or 13, and Livewire 3 or 4.

## Your first editable translation

Put a container for the modal in your layout, once:

```blade
<body>
    ...
    <div id="inline-translation-modals"></div>
    @livewireScripts
</body>
```

Then replace the call where the text should become editable:

```blade
<h1><livewire:inline-translation translation-key="website.welcome" /></h1>
```

That is all. `website.welcome` is looked up in the database first and falls back to your `lang/en/website.php`, so the page reads the same as before until someone edits it.

## Where the text lives

| | |
|---|---|
| Not edited yet | your language file, through `__('website.welcome')` |
| Edited once | a row in the `translations` table, per locale |
| Language file changed afterwards | the database row wins; delete it to fall back again |

Your language files stay untouched, so they remain the source for everything you have not handed over.

## What it does not do

It is deliberately small. There is one guard check and no per user or per key permissions, no revision history, no approval flow and no translation memory. The value is rendered as HTML, which is what makes the editor mode useful and also means the guard is the whole security boundary. [How it works](how-it-works.md) says exactly where the edges are.

## Read on

- [Installation](installation.md): composer, the migration, the modal container and the guard
- [Usage](usage.md): the component in Blade, the HTML editor mode and where not to use it
- [Configuration](configuration.md): the two settings and what they change
- [How it works](how-it-works.md): the lookup order, saving per locale and the teleport
- [API reference](api-reference.md): every public method, the table and the config keys
