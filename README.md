# darvis/livewire-inline-translation

[![Latest version](https://img.shields.io/packagist/v/darvis/livewire-inline-translation.svg)](https://packagist.org/packages/darvis/livewire-inline-translation)
[![Tests](https://github.com/ArvidDeJong/livewire-inline-translation/actions/workflows/tests.yml/badge.svg)](https://github.com/ArvidDeJong/livewire-inline-translation/actions/workflows/tests.yml)
[![Total downloads](https://img.shields.io/packagist/dt/darvis/livewire-inline-translation.svg)](https://packagist.org/packages/darvis/livewire-inline-translation)
[![PHP version](https://img.shields.io/packagist/dependency-v/darvis/livewire-inline-translation/php.svg)](https://packagist.org/packages/darvis/livewire-inline-translation)
[![License](https://img.shields.io/packagist/l/darvis/livewire-inline-translation.svg)](LICENSE)

Lets an authorised user **edit a translation on the page itself**: they click the text, a modal opens, and the new value is stored in the database. Everyone else sees ordinary text, with nothing in the markup that gives away it is editable.

A client who wants the wording changed normally sends you an email. This lets them do it themselves, without a CMS behind it.

## Features

- ✏️ Click to edit, in the page, in a modal
- 🗄️ Stored in the `translations` table per locale, with a fallback to your language files, which stay untouched
- 🔐 One guard check decides who may edit; a visitor without it sees plain text
- 📝 Optional HTML mode with a small bold, italic and list editor
- 🧩 No CSS framework needed: inline styles, Alpine for the modal, nothing from a CDN
- 🤖 Laravel Boost guideline and `livewire-inline-translation-development` skill included

## Requirements

PHP 8.2+, Laravel 11, 12 or 13, and Livewire 3 or 4.

## Installation

```bash
composer require darvis/livewire-inline-translation
php artisan migrate
```

The `translations` table comes along with the migration; there is nothing to publish for it.

Put a container for the modal in your layout, once, just before the closing body tag:

```blade
<div id="inline-translation-modals"></div>
```

## Quick start

Replace the call where the text should become editable:

```blade
<h1><livewire:inline-translation translation-key="website.welcome" /></h1>
```

`website.welcome` is looked up in the database first and falls back to `__('website.welcome')`, so the page reads the same as before until someone edits it.

For rich text:

```blade
<livewire:inline-translation translation-key="website.intro" :html="true" />
```

## Who may edit

Whoever is logged in on the guard from `config('inline-translation.guard')`, `web` by default:

```bash
php artisan vendor:publish --tag=inline-translation-config
```

```php
// config/inline-translation.php
'guard' => 'staff',
```

The check is a plain `Auth::guard($guard)->check()`, all or nothing. The stored value is rendered as HTML, so whoever may edit may also put script on the page: give that guard only to people you trust that far.

## Documentation

[arviddejong.github.io/livewire-inline-translation](https://arviddejong.github.io/livewire-inline-translation/): installation, usage, configuration, how it works and the API reference.

## Laravel Boost

The package ships [Laravel Boost](https://laravel.com/docs/boost) resources: a guideline
and a `livewire-inline-translation-development` skill. Run `php artisan boost:install`, or
`php artisan boost:update --discover` in a project that already uses Boost.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md). Security issues go through [SECURITY.md](SECURITY.md), not a public issue.

## License

MIT. See [LICENSE](LICENSE).
