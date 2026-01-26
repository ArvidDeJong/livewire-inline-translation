# Installation Guide

This guide will walk you through installing the Livewire Inline Translation package step by step.

## Requirements

Before installing, make sure your system meets these requirements:

- **PHP**: 8.2 or higher
- **Laravel**: 11.x or 12.x
- **Livewire**: 3.x or 4.x
- **Alpine.js**: Any version (for modal functionality)

## Step 1: Add Package Repository

Since this is a local package, you need to add it to your project's `composer.json` file.

Open your project's `composer.json` and add the package repository:

```json
{
    "repositories": {
        "inline-translation": {
            "type": "path",
            "url": "../Packages/livewire-inline-translation"
        }
    }
}
```

**Note**: Adjust the `url` path based on where you've placed the package relative to your project.

## Step 2: Require the Package

Add the package to your `require` section:

```json
{
    "require": {
        "darvis/livewire-inline-translation": "@dev"
    }
}
```

Then run:

```bash
composer update darvis/livewire-inline-translation
```

This will create a symlink from your `vendor` directory to the package directory.

## Step 3: Publish Configuration (Optional)

If you want to customize the package configuration, publish the config file:

```bash
php artisan vendor:publish --tag=inline-translation-config
```

This creates `config/inline-translation.php` where you can configure:
- Authentication guard (default: `staff`)
- Modal container ID

## Step 4: Run Migrations

The package needs a database table to store custom translations. Run the migrations:

```bash
php artisan migrate
```

This creates the `translations` table with the following structure:
- `id` - Primary key
- `locale` - Language code (e.g., 'en', 'nl')
- `group` - File name (e.g., 'website', 'messages')
- `key` - Translation key
- `value` - Translation value
- `timestamps` - Created/updated timestamps

## Step 5: Add Modal Container to Layout

The package uses Alpine.js to teleport modals to a specific container. Add this container to your main layout file.

**Example**: `resources/views/components/layouts/website.blade.php`

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Livewire Styles -->
    @livewireStyles
</head>
<body>
    <!-- Your page content -->
    {{ $slot }}

    <!-- IMPORTANT: Add this container for inline translation modals -->
    <div id="inline-translation-modals"></div>

    <!-- Livewire Scripts -->
    @livewireScripts
</body>
</html>
```

**Important**: 
- The container ID must match the `modal_container_id` in your config
- Place it before the closing `</body>` tag
- Make sure Alpine.js is loaded

## Step 6: Verify Installation

To verify the package is installed correctly:

```bash
# Check if the package is listed
composer show darvis/livewire-inline-translation

# Check if the service provider is discovered
php artisan package:discover

# Verify the migration ran
php artisan migrate:status
```

## Configuration Options

### Change Authentication Guard

By default, the package uses the `staff` guard. To change this:

**Option 1**: Environment variable (recommended)

```env
INLINE_TRANSLATION_GUARD=web
```

**Option 2**: Config file

```php
// config/inline-translation.php
return [
    'guard' => 'web',
];
```

### Custom Modal Container

If you need a different container ID:

```php
// config/inline-translation.php
return [
    'modal_container_id' => 'my-custom-modals',
];
```

Then update your layout:

```blade
<div id="my-custom-modals"></div>
```

## Troubleshooting

### Package Not Found

If Composer can't find the package:

1. Check the `url` path in your `repositories` section
2. Make sure the package directory exists
3. Run `composer clear-cache`
4. Try `composer update` again

### Migration Already Exists

If you get an error about the migration already existing:

```bash
# Check migration status
php artisan migrate:status

# If the table exists, you're good to go
# If not, run the migration
php artisan migrate
```

### Modal Not Showing

If the modal doesn't appear when clicking translations:

1. Check if the modal container exists in your layout
2. Verify Alpine.js is loaded
3. Check browser console for JavaScript errors
4. Make sure the container ID matches your config

### Authorization Not Working

If translations aren't editable even when logged in:

1. Verify you're using the correct guard
2. Check if the user is authenticated: `Auth::guard('staff')->check()`
3. Clear config cache: `php artisan config:clear`

## Next Steps

Now that the package is installed, learn how to use it:

- [Usage Guide](usage.md) - Learn how to use the component
- [Configuration](configuration.md) - Detailed configuration options
- [How It Works](how-it-works.md) - Understand the internals
