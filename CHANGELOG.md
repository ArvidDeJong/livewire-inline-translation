# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
