# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- `InlineTranslationConfig` with named accessors is the one place that reads the package config, so a default is written down once.
- The package now carries the same tooling as the other darvis packages: Pint, Larastan level 8, the `lint`, `format` and `analyse` composer scripts, the shared CI matrix (PHP 8.2 to 8.4 against Laravel 11, 12 and 13, lowest and latest), Dependabot, issue and pull request templates, a `.gitattributes` that keeps `docs/` and `tests/` out of the dist archive, a `CODE_OF_CONDUCT.md`, and a Laravel Boost guideline in `resources/boost/`.
- A [documentation site](https://arviddejong.github.io/livewire-inline-translation/) on GitHub Pages, built from `docs/`, with an FAQ, a description per page, a sitemap, `llms.txt` and structured data.

### Fixed

- **`modal_container_id` did nothing.** The Blade view had `inline-translation-modals` hardcoded as the teleport target, while the config and the documentation said you could change it. The view now uses the configured id.
- **The default guard did not exist.** The config shipped `guard => 'user'`, which is not a guard a stock Laravel defines, so every page showing a translation threw `Auth guard [user] is not defined` until you published the config. The default is now `web`, the guard every Laravel application has. Point it at your own editor guard if you have one.
- A guard the application does not define is treated as "nobody may edit" instead of throwing, so a typo in the config costs the editing and not the page.
- The code fell back to `'staff'` while the config said `'user'`, the comment claimed `'staff'` and the test expected `'staff'`, so the suite was red. There is one default now, in `InlineTranslationConfig`.
- The component tests ran on Livewire 3 only: `Livewire::getClass()` no longer exists in Livewire 4, and `assertSee()` escapes its needle, so the assertion on `wire:click="openModal"` could never match. Both are fixed, and the suite now runs on Laravel 13 with Livewire 4.
- Publishing the migration created the `translations` table a second time. `database/migrations` held both a real migration, which the provider loads automatically, and a `.php.stub` behind the `inline-translation-migrations` publish tag. The stub and that tag are gone; `php artisan migrate` creates the table, as it already did.

### Changed

- `orchestra/testbench` accepts `^11.0` and `pestphp/pest` accepts `^4.0`, so the package can actually be tested on the Laravel 13 it claims to support. `phpunit/phpunit` is a dev dependency instead of an implied one, and `minimum-stability` is `stable`.

## [1.2.0] - 2026-03-23

### Added
- Added support for Laravel 13.x.

---

## [1.1.0] - 2026-01-27

### Added
- **HTML Editor Mode** - New `:html="true"` parameter for WYSIWYG editing
  - ContentEditable-based editor with no external dependencies
  - Toolbar with Bold, Italic, and Bullet List formatting
  - Debounced updates (500ms) to prevent cursor jumping
  - Auto-converts `<div>` and `<p>` tags to `<br>` for cleaner HTML
- Updated documentation with HTML mode examples and usage guide

---

## [1.0.0] - 2026-01-26

### Added

**Core Features**
- Inline translation editing component for Livewire applications
- Database storage with fallback to Laravel language files
- Two-tier translation priority system (database → language files)
- Configurable authentication guard system
- Alpine.js modal with Tailwind inline styles
- Automatic Livewire component registration via service provider

**Models & Database**
- Translation Model with static helper methods
  - `getTranslation()` - Retrieve translations from database
  - `setTranslation()` - Save/update translations using updateOrCreate
- Database migration for translations table
- Unique constraint on locale, group, and key
- Indexed columns for performance

**Configuration**
- Configurable authentication guard (default: staff)
- Configurable modal container ID
- Environment variable support
- Published config file

**Documentation**
- Comprehensive README with installation and usage
- Detailed installation guide
- Complete usage guide with examples
- Configuration documentation
- How It Works - Internal architecture guide
- Complete API reference
- Contributing guidelines
- Security policy
- MIT License

**Testing**
- Comprehensive Pest test suite
- Unit tests for Translation Model
- Feature tests for Livewire component
- Service provider tests
- PHPUnit configuration
- Test coverage support

**Developer Experience**
- PSR-4 autoloading
- Composer scripts for testing
- EditorConfig for consistent coding style
- Well-documented code with PHPDoc blocks

### Features

**User Features**
- Edit translations directly on website without CMS access
- Visual indicator (blue dashed underline) for editable text
- Click-to-edit modal interface
- Instant updates without page reload
- Support for HTML content in translations
- Multi-language support via Laravel's locale system

**Developer Features**
- Authorization via configurable guard (default: staff)
- Database priority over language files (non-destructive)
- No Flux dependencies - works with any Livewire setup
- Extendable component and model classes
- Publishable views for customization
- Compatible with Livewire 3.x and 4.x
- Compatible with Laravel 11.x and 12.x
- PHP 8.2+ support

**Technical Features**
- Alpine.js x-teleport for modal positioning
- Livewire reactive properties
- Eloquent ORM for database operations
- Unique constraint prevents duplicate translations
- Database indexes for fast lookups

### Security
- CSRF protection via Livewire
- SQL injection protection via Eloquent
- Configurable authorization system
- Guard-based access control

### Compatibility
- PHP: ^8.2
- Laravel: ^11.0 | ^12.0
- Livewire: ^3.0 | ^4.0
- Alpine.js: Any version

---

## Future Releases

See [GitHub Issues](https://github.com/darvis/livewire-inline-translation/issues) for planned features and improvements.
