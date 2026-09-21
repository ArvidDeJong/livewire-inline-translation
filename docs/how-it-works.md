---
title: "How it works"
nav_order: 5
description: "What the inline-translation component does on each request: the database first lookup, the guard check on every action, saving per locale and the modal."
---

# How it works

The package has four parts: the Livewire component `InlineTranslation`, the Eloquent model `Translation`, one Blade view and a service provider that ties them to Laravel.

## What the service provider registers

`InlineTranslationServiceProvider` is discovered by Laravel through `composer.json`. It:

1. registers the Livewire component under the name `inline-translation`;
2. loads the migration from the package, which is how your application gets the `translations` table with `php artisan migrate`;
3. loads the views under the namespace `inline-translation`;
4. offers the view and the config for publishing, with the tags `inline-translation-views` and `inline-translation-config`;
5. merges the package config under the key `inline-translation`.

There is no migration to publish, and the package has no routes, commands, events or middleware of its own.

## How a text is looked up

When the page renders the tag, Livewire calls `mount(string $translationKey, bool $html = false)`:

1. The key is split on the first dot into a group and a key. `website.hero.title` becomes group `website` and key `hero.title`. A key without a dot is not looked up; the component shows the key itself.
2. `Translation::getTranslation($locale, $group, $key)` reads the `value` column of the row for `app()->getLocale()`.
3. A row wins. Without a row the component returns `__($translationKey)`, the text from your language file. When the language file has no such line either, Laravel returns the key, so the key is what the page shows.

That is one database query for each component on the page, on every page view. Nothing is cached. `openModal()` runs the same lookup again, so the editor starts from the current value.

## Who gets the underline, and who may save

`isAuthorized()` answers `true` when the configured guard is defined under `auth.guards` and `Auth::guard($guard)->check()` passes. A guard that is not defined gives `false` instead of an exception, so a typo in the config costs the editing and not the page.

The answer is used in three places:

| Where | What happens when it is `false` |
| --- | --- |
| `render()` passes it to the view as `$isAuthorized` | the text is shown without the underline, the click handler and the modal |
| `openModal()` | the request is answered with HTTP 403 |
| `save()` | the request is answered with HTTP 403; nothing is written |

`openModal()` and `save()` do this through `authorizeEditing()`, which is `abort_unless($this->isAuthorized(), 403)`. The check on the actions matters because the component is on the page for every visitor, and a browser can call any public method of a Livewire component.

The properties `translationKey` and `html` are locked with Livewire's `#[Locked]` attribute: the browser cannot change which key a component edits. An attempt ends in Livewire's exception `Cannot update locked property: [translationKey]`.

The checks on the actions and the locked properties exist since version 1.3.1. Upgrade an older installation.

## What a visitor receives

Every visitor, logged in or not, gets the text inside a `<span>` that carries Livewire's attributes (such as `wire:id` and `wire:snapshot`). The snapshot contains the translation key. Only an authorised user also gets the inner `<span wire:click="openModal">` with the dashed underline.

The value is printed without escaping, in both modes. That is what makes the HTML editor useful, and it means the people who may edit can put any markup on the page, including script.

## How a save is stored

`save()` splits the key the same way and calls `Translation::setTranslation($locale, $group, $key, $value)` with the locale of the request in which the save happens. That is an `updateOrCreate()` on `locale`, `group` and `key`: the first save creates the row, a later save updates it. Then the modal closes and the component shows the new value.

A key without a dot is not saved. `save()` returns without writing, and the modal stays open.

Livewire sends the save to its own update route. Route middleware of your page does not run there unless it is persistent middleware; see [Several locales](usage.md#several-locales).

## The translations table

| Column | Type | Notes |
| --- | --- | --- |
| `id` | big integer | primary key |
| `locale` | string, 10 | indexed |
| `group` | string, 100 | indexed; the part of the key before the first dot |
| `key` | string, 255 | indexed; the rest of the key |
| `value` | text | not nullable |
| `created_at`, `updated_at` | timestamps | |

`locale`, `group` and `key` together are unique, so there is one row for each text in each locale.

## How the modal reaches your layout

The modal only exists in the HTML while `showModal` is true. It sits in a `<template x-teleport="#...">` element, with the id from `modal_container_id`. Alpine, which Livewire ships, moves the content of that template into the element with that id. A modal that stayed inside a heading or a card could be cut off by `overflow: hidden` or end up under other elements; an empty container at the end of the body avoids that.

Without an element with that id there is nothing to move the modal into: the modal does not appear, and the browser console shows Alpine's warning `Cannot find x-teleport element for selector: "#inline-translation-modals"`.

In HTML mode the editor is a `contenteditable` element. It sends its content to the component 500 ms after the last input, with `<div>` tags removed, `</div>` turned into `<br>`, a double `<br>` reduced to one and a `<br>` at the end dropped.

## Next

- [API reference](api-reference.md): every method and property
- [Testing](testing.md): test this behaviour in your own application
