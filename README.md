# Livewire Inline Translation

[![Latest Version](https://img.shields.io/badge/version-1.0.0-blue.svg)](https://github.com/darvis/livewire-inline-translation)
[![PHP Version](https://img.shields.io/badge/php-%5E8.2-8892BF.svg)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/laravel-11.x%20%7C%2012.x-FF2D20.svg)](https://laravel.com)
[![Livewire Version](https://img.shields.io/badge/livewire-3.x%20%7C%204.x-FB70A9.svg)](https://livewire.laravel.com)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

Inline translation editing for Livewire applications with database storage. This package allows authorized users (e.g., staff) to edit translations directly on the website without accessing the CMS.

![Demo](https://via.placeholder.com/800x400?text=Demo+Screenshot)

## ✨ Features

- ✅ **Inline Editing** - Edit translations directly on your website
- ✅ **HTML Editor** - Optional ContentEditable WYSIWYG editor for rich text content
- ✅ **Database Storage** - Custom translations stored in database with fallback to Laravel language files
- ✅ **Flexible Authorization** - Configurable guard system (staff, web, custom)
- ✅ **Modern UI** - Alpine.js modal with Tailwind inline styles
- ✅ **Framework Agnostic** - No Flux dependencies, works with any Livewire setup
- ✅ **No External Dependencies** - Built-in ContentEditable editor, no CDN required
- ✅ **Auto-Discovery** - Automatic Livewire component registration
- ✅ **Well Tested** - Comprehensive Pest test suite
- ✅ **Fully Documented** - Extensive documentation for developers

## 📋 Requirements

- **PHP**: 8.2 or higher
- **Laravel**: 11.x or 12.x
- **Livewire**: 3.x or 4.x
- **Alpine.js**: Any version (for modal functionality)

## 📚 Documentation

- [Installation Guide](docs/installation.md) - Step-by-step installation instructions
- [Usage Guide](docs/usage.md) - Learn how to use the package
- [Configuration](docs/configuration.md) - All configuration options
- [How It Works](docs/how-it-works.md) - Understanding the internals
- [API Reference](docs/api-reference.md) - Complete API documentation
- [Contributing](CONTRIBUTING.md) - How to contribute to this package

## 🚀 Quick Start

### Installation

### 1. Install via Composer

```bash
composer require darvis/livewire-inline-translation
```

### 2. Publish and Run Migrations

```bash
php artisan vendor:publish --tag=inline-translation-migrations
php artisan migrate
```

### 3. Publish Config (Optional)

```bash
php artisan vendor:publish --tag=inline-translation-config
```

This will create `config/inline-translation.php` where you can configure:
- Authentication guard (default: `staff`)
- Modal container ID

### 4. Publish Views (Optional)

If you want to customize the view:

```bash
php artisan vendor:publish --tag=inline-translation-views
```

### 5. Add Modal Container to Layout

Add this container to your layout file (e.g., `resources/views/components/layouts/website.blade.php`):

```blade
<!DOCTYPE html>
<html>
<head>
    <!-- Your head content -->
    @livewireStyles
</head>
<body>
    <!-- Your body content -->
    {{ $slot }}

    <!-- Add this container for inline translation modals -->
    <div id="inline-translation-modals"></div>

    @livewireScripts
</body>
</html>
```

## Usage

### Basic Usage

Use the component in your Blade templates:

```blade
<livewire:inline-translation translationKey="website.welcome" />
```

### Translation Key Format

The translation key should follow the format: `{group}.{key}`

- `group`: The language file name (e.g., `website`, `messages`)
- `key`: The translation key within that file

Examples:
- `website.welcome` → `lang/en/website.php` → `['welcome' => '...']`
- `messages.hello` → `lang/en/messages.php` → `['hello' => '...']`

### How It Works

1. **For Visitors**: Shows the translated text normally
2. **For Authorized Users**: 
   - Shows text with blue dashed underline
   - Click to open edit modal
   - Edit and save translation
   - Changes are stored in database
   - No page reload needed

### Translation Priority

1. **Database** - Custom translations from `translations` table (highest priority)
2. **Language Files** - Laravel's default `lang/{locale}/{file}.php` files (fallback)

This means you can override any Laravel translation by editing it inline, and the original files remain untouched.

## Configuration

### Change Authentication Guard

In `config/inline-translation.php`:

```php
return [
    'guard' => 'web', // Change from 'staff' to 'web' or any other guard
];
```

Or via environment variable:

```env
INLINE_TRANSLATION_GUARD=web
```

### Customize Modal Container

In `config/inline-translation.php`:

```php
return [
    'modal_container_id' => 'my-custom-container',
];
```

Then update your layout:

```blade
<div id="my-custom-container"></div>
```

## Database Structure

The package creates a `translations` table:

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| locale | string(10) | Language code (e.g., 'en', 'nl') |
| group | string(100) | File name (e.g., 'website', 'messages') |
| key | string(255) | Translation key |
| value | text | Translation value |
| created_at | timestamp | Created timestamp |
| updated_at | timestamp | Updated timestamp |

Unique constraint on: `locale`, `group`, `key`

## Examples

### Example 1: Welcome Message

Language file `lang/en/website.php`:
```php
return [
    'welcome' => 'Welcome to our website!',
];
```

Blade template:
```blade
<h1><livewire:inline-translation translationKey="website.welcome" /></h1>
```

### Example 2: Multiple Translations

```blade
<div>
    <h1><livewire:inline-translation translationKey="website.title" /></h1>
    <p><livewire:inline-translation translationKey="website.description" /></p>
    <button><livewire:inline-translation translationKey="website.cta_button" /></button>
</div>
```

### Example 3: With HTML Content

The component supports HTML in translations:

```blade
<div>
    <livewire:inline-translation translationKey="website.rich_content" />
</div>
```

Language file:
```php
return [
    'rich_content' => 'This is <strong>bold</strong> and <em>italic</em> text.',
];
```

## Important Notes

### Do NOT Use Inside Links or Buttons

❌ **Wrong:**
```blade
<a href="/contact">
    <livewire:inline-translation translationKey="website.contact" />
</a>
```

✅ **Correct:**
```blade
<a href="/contact">{{ __('website.contact') }}</a>
```

The component generates a clickable span for authorized users, which conflicts with parent clickable elements.

### Alpine.js Required

The modal uses Alpine.js `x-teleport` directive. Make sure Alpine.js is loaded in your layout:

```blade
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

## Troubleshooting

### Modal Not Showing

1. Check if `#inline-translation-modals` container exists in your layout
2. Verify Alpine.js is loaded
3. Check browser console for JavaScript errors

### Translations Not Saving

1. Verify database migration ran successfully
2. Check if user is authenticated with correct guard
3. Verify translation key format is correct (`group.key`)

### Authorization Not Working

1. Check `config/inline-translation.php` guard setting
2. Verify user is logged in with correct guard
3. Clear config cache: `php artisan config:clear`

## 🧪 Testing

The package includes a comprehensive test suite using Pest:

```bash
# Run all tests
composer test

# Run with coverage
composer test-coverage

# Run specific test file
vendor/bin/pest tests/Unit/TranslationModelTest.php
```

## 🤝 Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

### Development Setup

```bash
# Clone the repository
git clone https://github.com/darvis/livewire-inline-translation.git
cd livewire-inline-translation

# Install dependencies
composer install

# Run tests
composer test
```

## 📝 Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for recent changes.

## 🔒 Security

If you discover any security-related issues, please email info@arvid.nl instead of using the issue tracker.

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## 👨‍💻 Author

**Arvid de Jong**
- Email: info@arvid.nl
- GitHub: [@darvis](https://github.com/darvis)

## 🙏 Credits

- Built with [Laravel](https://laravel.com)
- Powered by [Livewire](https://livewire.laravel.com)
- UI interactions with [Alpine.js](https://alpinejs.dev)

## ⭐ Support

If you find this package helpful, please consider giving it a star on GitHub!
