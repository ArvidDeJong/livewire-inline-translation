---
title: "Home"
nav_order: 1
description: "darvis/livewire-inline-translation lets a logged in editor click a text on a Laravel Livewire page, change it in a modal and store it in the database."
permalink: /
---

# Livewire Inline Translation

`darvis/livewire-inline-translation` is a Laravel package with one Livewire component that shows a translation and lets an authorised user edit it on the page itself. They click the text, a modal opens, and the new value is stored in the `translations` table for the current locale.

It is for a site where a client or an editor wants to change wording without a CMS and without asking a developer. Your language files stay the source for every text nobody has edited.

## Install it in three lines

```bash
composer require darvis/livewire-inline-translation
php artisan migrate
```

```blade
<div id="inline-translation-modals"></div>
```

The last line goes in your layout, once, before the closing body tag. [Installation](installation.md) has every step and a check that it works.

## Requirements

PHP 8.2 or higher, Laravel 11, 12 or 13, and Livewire 3 or 4. Livewire brings Alpine.js, which the modal uses. A database, because the edited values are stored there.

## Your first editable translation

`resources/views/welcome.blade.php`:

```blade
<h1><livewire:inline-translation translation-key="website.welcome" /></h1>
```

`website.welcome` is looked up in the database first and falls back to `__('website.welcome')`, so the page reads the same as before until someone edits it. Someone who is logged in on the configured guard sees a dashed blue underline and can click the text. Everyone else gets the text without the underline, the click handler or the modal.

## Where the text lives

| Situation | Where the text comes from |
| --- | --- |
| Not edited yet | your language file, through `__('website.welcome')` |
| Edited once | a row in the `translations` table, one per locale |
| Language file changed afterwards | the database row still wins; delete the row to fall back to the file |

## What it does not do

- No per user or per key permissions. There is one guard check; you narrow it by [overriding `isAuthorized()`](configuration.md#let-only-some-users-edit).
- No revision history, no approval flow and no translation memory.
- No sanitising. The stored value is rendered as HTML, so whoever may edit can put any markup on the page.
- It does not change `__()`. Only the component reads the database; see [Usage](usage.md#read-the-stored-value-somewhere-else).
- No JSON translation strings: a key needs the form `group.key`.

## Read on

- [Installation](installation.md): from `composer require` to a text you can click, with a check that it works
- [Usage](usage.md): one complete example, the HTML editor, several locales and where the component does not belong
- [Configuration](configuration.md): the two settings, choosing a guard and narrowing who may edit
- [How it works](how-it-works.md): the lookup order, the authorisation check, saving per locale and the modal
- [API reference](api-reference.md): the component, the model, the table, the config keys and the publish tags
- [Testing](testing.md): test a page with an editable translation in your own application
- [Troubleshooting](troubleshooting.md): from the symptom to the cause and the fix
- [FAQ](faq.md): short answers to the questions people ask first
