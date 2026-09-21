# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.3.2] - 2026-09-21

### Fixed

Documentation only; nothing in the package changes. These claims in the docs were wrong:

- **Installation** told you to add a Composer path repository and require `@dev`, and named Laravel "11.x or 12.x". The package is on Packagist (`composer require darvis/livewire-inline-translation`) and supports Laravel 11, 12 and 13. The layout example loaded Alpine from a CDN; Livewire ships Alpine, and a second copy breaks it.
- **API reference and How it works** still showed `staff` as the default guard (`'guard' => 'staff'`, `INLINE_TRANSLATION_GUARD=staff`, "staff guard"). The default is `web`.
- `mount()` was documented as `mount(string $translationKey)`. It is `mount(string $translationKey, bool $html = false)`, and the public `$html` property and the `:html` attribute were missing from the API reference.
- `render()` was shown without its return type and with the guard check inside it. It returns `Illuminate\Contracts\View\View` and asks the protected `isAuthorized()`, which `openModal()` and `save()` ask as well, through `authorizeEditing()`.
- The service provider was said to publish migrations and to load them "for package development". There is no publish tag for the migration; loading it is how every application gets the `translations` table.
- "This query is cached by Eloquent": nothing is cached. It is one query per component per page view, and one more when the modal opens. The timing and size figures next to it were not measured and are gone.
- The modal snippet had `x-teleport="#inline-translation-modals"` written out. The view uses the `modal_container_id` setting.
- The HTML editor was said to turn `<div>` and `<p>` into `<br>`. It only removes `<div>` and turns `</div>` into `<br>`; `<p>` is stored as it is.
- "All configuration can be overridden via environment variables": only `guard` has one, `INLINE_TRANSLATION_GUARD`.
- The API reference suggested listening for a `component-updated` event. The package dispatches no events.
- README and home page: "nothing in the markup gives away that it is editable". Every visitor gets Livewire's wrapper element, whose snapshot names the component and the key; what a visitor does not get is the underline, the click handler and the modal.
- How it works linked to an `extending.md` that never existed.
- `SECURITY.md` said only the underline and the modal are behind the guard, and told you to "wrap" the component for permissions. Saving is behind the guard too, and the hook is `isAuthorized()`.

### Added

- A **Troubleshooting** page, from symptom to cause and fix, with the literal error messages: no underline, no modal (Alpine's `Cannot find x-teleport element` warning), a 403 on save, an edit stored under the wrong locale, `Return value must be of type string, array returned`, a missing or clashing `translations` table, `Unable to find component: [inline-translation]`.
- A **Testing** page with a complete Pest test for the editor flow and the refused visitor, another guard, another locale and a whole page.
- Installation has a "Check that it works" section; Usage has one complete example with file names, and sections on several locales (persistent middleware), reading the stored value outside the component and going back to the language file.
- The FAQ answers what the package is, which versions it supports, what it costs and whether it is safe.
- The docs guard test checks that every relative link resolves, that the home page links every page and that the stated requirements match `composer.json`.

## [1.3.1] - 2026-09-21

### Security

- **`save()` and `openModal()` now check the guard.** The guard was only asked when the component was drawn, to decide whether the underline is shown. The component is on the page for every visitor and the browser can call every public action of a Livewire component, so a visitor who was not logged in could store a value for any translation key, and a value is rendered as HTML. Both actions now answer 403 for a visitor who may not edit, and `translationKey` and `html` are `#[Locked]`, so the browser cannot point the component at another key. Upgrade, and check the `translations` table for rows you did not write:
  `select * from translations where value like '%<script%' or value like '%onerror=%' or value like '%javascript:%';`
- If you subclassed the component and added a public action of your own, call `$this->authorizeEditing()` at the top of it.

### Fixed

- The "Custom Authorization Logic" examples in the docs overrode `render()`, which is a fatal error (the return type is missing) and only ever hid the underline. They now override `isAuthorized()`, which the view, `openModal()` and `save()` all ask.
- The docs named `staff` and `user` as the default guard. It is `web`: out of the box everyone who is logged in may edit. The docs now say so and tell you to point the package at a guard for editors.

### Added

- A Laravel Boost skill, `livewire-inline-translation-development`, in `resources/boost/skills/`. It covers how a translation is looked up, rendered and saved, what each failure gives you, the pitfalls in a host app (where the guard is checked, the locale on a Livewire update, `__()` elsewhere not reading the database), narrowing editing to a permission, reading and writing the `translations` table yourself, the settings and how to test it.
- A social preview image for the documentation site, used for the Open Graph and Twitter card of every page.

## [1.3.0] - 2026-09-20

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

See [GitHub Issues](https://github.com/ArvidDeJong/livewire-inline-translation/issues) for planned features and improvements.
