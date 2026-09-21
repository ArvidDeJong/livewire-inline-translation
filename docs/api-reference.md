---
title: "API reference"
nav_order: 6
description: "Every public and protected member of the InlineTranslation component and the Translation model, plus the tag attributes, config accessors and publish tags."
---

# API reference

## The Blade tag

```blade
<livewire:inline-translation translation-key="website.welcome" />
<livewire:inline-translation translation-key="website.intro" :html="true" />
```

| Attribute | Type | Default | Meaning |
| --- | --- | --- | --- |
| `translation-key` | string, required | | The key in the form `group.key`. `translationKey` is accepted as well. |
| `:html` | bool | `false` | `true` shows the editor with bold, italic and a bullet list instead of a text field. |

The component name is `inline-translation`. It is registered by the service provider; you do not register it yourself.

## InlineTranslation

`Darvis\LivewireInlineTranslation\InlineTranslation`, a `Livewire\Component`.

### Properties

| Property | Type | Default | Notes |
| --- | --- | --- | --- |
| `$translationKey` | `string` | `''` | `#[Locked]`: set by the tag, the browser cannot change it. |
| `$translationValue` | `string` | `''` | The current text; bound to the field in the modal. |
| `$showModal` | `bool` | `false` | Whether the modal is rendered. |
| `$html` | `bool` | `false` | `#[Locked]`. |

### Public methods

| Method | What it does |
| --- | --- |
| `mount(string $translationKey, bool $html = false): void` | Stores both arguments and loads the current text into `$translationValue`. |
| `openModal(): void` | Checks the authorisation (403 when it fails), reloads the text and sets `$showModal` to `true`. |
| `closeModal(): void` | Sets `$showModal` to `false`. It changes nothing else, so it has no authorisation check. |
| `save(): void` | Checks the authorisation (403 when it fails), stores `$translationValue` for the current locale and closes the modal. A key without a dot is not stored and leaves the modal open. |
| `render(): Illuminate\Contracts\View\View` | Renders the view `inline-translation::inline-translation` with one variable, `$isAuthorized` (bool). |

### Protected methods, for a subclass

| Method | What it does |
| --- | --- |
| `isAuthorized(): bool` | `true` when the configured guard is defined in `auth.guards` and someone is logged in on it. Override this to narrow who may edit; see [Configuration](configuration.md#let-only-some-users-edit). |
| `authorizeEditing(): void` | `abort_unless($this->isAuthorized(), 403)`. Call it first in every public action you add. |
| `getTranslation(): string` | The database value for the current locale, otherwise `__($this->translationKey)`. For a key without a dot it returns the key. |

## Translation

`Darvis\LivewireInlineTranslation\Models\Translation`, an Eloquent model on the `translations` table. Fillable: `locale`, `group`, `key`, `value`.

```php
use Darvis\LivewireInlineTranslation\Models\Translation;

Translation::getTranslation('en', 'website', 'welcome');          // ?string
Translation::setTranslation('en', 'website', 'welcome', 'Hello'); // Translation
```

| Method | Returns | What it does |
| --- | --- | --- |
| `getTranslation(string $locale, string $group, string $key): ?string` | the `value`, or `null` when there is no row | One query on `locale`, `group` and `key`. |
| `setTranslation(string $locale, string $group, string $key, string $value): self` | the model | `updateOrCreate()`: creates the row or updates its `value`. |

Both are static. Neither checks who is calling; they are meant for your own code, such as a seeder or an import.

## InlineTranslationConfig

`Darvis\LivewireInlineTranslation\Support\InlineTranslationConfig` is the one class in the package that reads the config.

| Method | Returns |
| --- | --- |
| `InlineTranslationConfig::guard(): string` | `inline-translation.guard`, or `web` when it is empty or not a string |
| `InlineTranslationConfig::modalContainerId(): string` | `inline-translation.modal_container_id`, or `inline-translation-modals` when it is empty or not a string |

Use it in your layout when you changed the container id and do not want to repeat it:

{% raw %}
```blade
@use('Darvis\LivewireInlineTranslation\Support\InlineTranslationConfig')

<div id="{{ InlineTranslationConfig::modalContainerId() }}"></div>
```
{% endraw %}

## Config keys and publish tags

| Config key | Env variable | Default |
| --- | --- | --- |
| `inline-translation.guard` | `INLINE_TRANSLATION_GUARD` | `web` |
| `inline-translation.modal_container_id` | none | `inline-translation-modals` |

| Publish tag | Publishes |
| --- | --- |
| `inline-translation-config` | `config/inline-translation.php` |
| `inline-translation-views` | `resources/views/vendor/inline-translation/` |

There is no publish tag for the migration. The package loads it, and `php artisan migrate` runs it.

## Events, routes and commands

The package dispatches no events, registers no routes and has no Artisan commands.

## The table

See [The translations table](how-it-works.md#the-translations-table).
