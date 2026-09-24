# darvis/livewire-inline-translation

[![Latest version](https://img.shields.io/packagist/v/darvis/livewire-inline-translation.svg)](https://packagist.org/packages/darvis/livewire-inline-translation)
[![Tests](https://github.com/ArvidDeJong/livewire-inline-translation/actions/workflows/tests.yml/badge.svg)](https://github.com/ArvidDeJong/livewire-inline-translation/actions/workflows/tests.yml)
[![PHP version](https://img.shields.io/packagist/dependency-v/darvis/livewire-inline-translation/php.svg)](https://packagist.org/packages/darvis/livewire-inline-translation)
[![License](https://img.shields.io/packagist/l/darvis/livewire-inline-translation.svg)](LICENSE)

A Laravel package with one Livewire component that shows a translation and lets an authorised user **edit it on the page itself**: they click the text, a modal opens, and the new value is stored in the database for the current locale. Everyone else gets the text without the underline, the click handler or the modal. A client who wants the wording changed can do it themselves, without a CMS behind it.

## Features

- Click to edit, on the page, in a modal
- Stored in the `translations` table per locale, with a fallback to your language files, which are never written to
- One guard decides who may edit; it is checked when the text is drawn, when the modal opens and when a value is saved
- The translation key is locked, so the browser cannot point the component at another key
- Optional HTML mode with a small bold, italic and bullet list editor
- No CSS framework needed: inline styles, and Alpine from Livewire for the modal
- A Laravel Boost guideline and skill for AI assistants

## Requirements

PHP 8.2 or higher, Laravel 11, 12 or 13, Livewire 3 or 4, and a database.

## Installation

```bash
composer require darvis/livewire-inline-translation
php artisan migrate
```

The `translations` table comes with the migration; there is nothing to publish for it.

Put a container for the modal in your layout, once, before the closing body tag:

```blade
<div id="inline-translation-modals"></div>
```

## Who may edit

Whoever is logged in on the guard from `INLINE_TRANSLATION_GUARD`. The default is `web`, so out of the box **every logged in user may edit every translation**. When customers can log in too, point the package at a guard for editors:

```env
INLINE_TRANSLATION_GUARD=staff
```

The stored value is rendered as HTML without sanitising, so whoever may edit can put any markup on the page, including script. Give that guard only to people you trust that far. [Configuration](https://arviddejong.github.io/livewire-inline-translation/configuration.html) shows how to require a permission and how to strip tags.

## Quick start

`resources/views/welcome.blade.php`:

```blade
<h1><livewire:inline-translation translation-key="website.welcome" /></h1>

<livewire:inline-translation translation-key="website.intro" :html="true" />
```

`website.welcome` is looked up in the database first and falls back to `__('website.welcome')`, so the page reads the same as before until someone edits it. An editor sees a dashed underline, clicks the text, changes it in the modal and saves. `:html="true"` gives them the small editor instead of a text field.

## Documentation

[arviddejong.github.io/livewire-inline-translation](https://arviddejong.github.io/livewire-inline-translation/)

- [Installation](https://arviddejong.github.io/livewire-inline-translation/installation.html): every step, and a check that it works
- [Usage](https://arviddejong.github.io/livewire-inline-translation/usage.html): a complete example, the HTML editor, several locales
- [Configuration](https://arviddejong.github.io/livewire-inline-translation/configuration.html): the two settings and narrowing who may edit
- [How it works](https://arviddejong.github.io/livewire-inline-translation/how-it-works.html): the lookup order, the authorisation check and the modal
- [API reference](https://arviddejong.github.io/livewire-inline-translation/api-reference.html): the component, the model, the config and the table
- [Testing](https://arviddejong.github.io/livewire-inline-translation/testing.html): test an editable translation in your own application
- [Troubleshooting](https://arviddejong.github.io/livewire-inline-translation/troubleshooting.html): from the symptom to the fix
- [FAQ](https://arviddejong.github.io/livewire-inline-translation/faq.html): short answers

## Laravel Boost

The package ships [Laravel Boost](https://laravel.com/docs/boost) resources: a guideline
and a `livewire-inline-translation-development` skill. Run `php artisan boost:install`, or
`php artisan boost:update --discover` in a project that already uses Boost.

## Testing

```bash
composer test      # Pest
composer lint      # Pint, check only (composer format fixes)
composer analyse   # Larastan
```

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## Support the package

If darvis/livewire-inline-translation saves you time, a star on [GitHub](https://github.com/ArvidDeJong/livewire-inline-translation) or a favourite on [Packagist](https://packagist.org/packages/darvis/livewire-inline-translation) helps other developers find it.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md).

## Security

Report a vulnerability privately, as described in [SECURITY.md](SECURITY.md), not in a public issue.

## License

MIT. See [LICENSE](LICENSE).
