# Configuration

This document covers all configuration options available in the Livewire Inline Translation package.

## Configuration File

After publishing the config file:

```bash
php artisan vendor:publish --tag=inline-translation-config
```

You'll find it at `config/inline-translation.php`:

```php
<?php

return [
    'guard' => env('INLINE_TRANSLATION_GUARD', 'staff'),
    'modal_container_id' => 'inline-translation-modals',
];
```

## Available Options

### Authentication Guard

**Key**: `guard`  
**Type**: `string`  
**Default**: `'staff'`  
**Environment Variable**: `INLINE_TRANSLATION_GUARD`

Controls which authentication guard determines if a user can edit translations.

**Examples**:

```php
// Use staff guard (default)
'guard' => 'staff',

// Use web guard (regular users)
'guard' => 'web',

// Use custom guard
'guard' => 'admin',
```

**Via Environment**:

```env
# .env
INLINE_TRANSLATION_GUARD=web
```

**Use Cases**:

- **staff**: Only staff members can edit (recommended for production)
- **web**: All authenticated users can edit (use with caution)
- **admin**: Only administrators can edit
- **custom**: Your custom guard

### Modal Container ID

**Key**: `modal_container_id`  
**Type**: `string`  
**Default**: `'inline-translation-modals'`

The ID of the HTML element where modals will be teleported.

**Example**:

```php
'modal_container_id' => 'my-modals-container',
```

**Layout Update Required**:

```blade
<div id="my-modals-container"></div>
```

**When to Change**:

- You already have a container with this ID
- You want to group modals differently
- You have multiple modal systems

## Environment Variables

All configuration can be overridden via environment variables:

```env
# .env

# Authentication guard
INLINE_TRANSLATION_GUARD=staff

# You can add more as needed
```

**Priority**:

1. Environment variable (highest)
2. Published config file
3. Package default (lowest)

## Runtime Configuration

You can also change configuration at runtime:

```php
// In a service provider or middleware
config(['inline-translation.guard' => 'web']);
```

**Note**: This only affects the current request.

## Multiple Guards Example

If you need different guards for different scenarios:

```php
// app/Providers/AppServiceProvider.php

public function boot()
{
    // Allow web users in development
    if (app()->environment('local')) {
        config(['inline-translation.guard' => 'web']);
    }
    
    // Only staff in production
    if (app()->environment('production')) {
        config(['inline-translation.guard' => 'staff']);
    }
}
```

## Custom Authorization Logic

For more complex scenarios, extend the component:

```php
// app/Livewire/CustomInlineTranslation.php

namespace App\Livewire;

use ArvidDeJong\LivewireInlineTranslation\InlineTranslation as BaseInlineTranslation;
use Illuminate\Support\Facades\Auth;

class CustomInlineTranslation extends BaseInlineTranslation
{
    public function render()
    {
        // Custom logic: User must be staff AND have permission
        $isAuthorized = Auth::guard('staff')->check() 
            && Auth::guard('staff')->user()->can('edit-translations');

        return view('inline-translation::inline-translation', [
            'isAuthorized' => $isAuthorized,
        ]);
    }
}
```

Then register your custom component:

```php
// app/Providers/AppServiceProvider.php

use Livewire\Livewire;
use App\Livewire\CustomInlineTranslation;

public function boot()
{
    // Override the default component
    Livewire::component('inline-translation', CustomInlineTranslation::class);
}
```

## Best Practices

### Production Settings

```env
# .env (production)
INLINE_TRANSLATION_GUARD=staff
APP_ENV=production
APP_DEBUG=false
```

**Why**:
- Only trusted staff can edit
- Prevents accidental edits by regular users
- Maintains content quality

### Development Settings

```env
# .env (local)
INLINE_TRANSLATION_GUARD=web
APP_ENV=local
APP_DEBUG=true
```

**Why**:
- Easier testing without staff login
- Faster development workflow
- Can test with different user types

### Staging Settings

```env
# .env (staging)
INLINE_TRANSLATION_GUARD=staff
APP_ENV=staging
APP_DEBUG=false
```

**Why**:
- Mirrors production setup
- Allows content team to test
- Catches issues before production

## Security Considerations

### Guard Selection

**High Security** (Recommended):
```php
'guard' => 'staff', // Only staff members
```

**Medium Security**:
```php
'guard' => 'web', // All authenticated users
```

**Low Security** (Not Recommended):
```php
// Don't do this - anyone can edit!
// The package doesn't support this, but be aware
```

### Additional Security Layers

Consider adding:

1. **Permissions**: Use Laravel's authorization
2. **Audit Logging**: Track who changes what
3. **Approval Workflow**: Require approval before publishing
4. **Content Validation**: Sanitize HTML input

**Example with Permissions**:

```php
// Create a permission
// php artisan permission:create edit-translations

// In your custom component
public function render()
{
    $isAuthorized = Auth::guard('staff')->check() 
        && Auth::guard('staff')->user()->hasPermissionTo('edit-translations');

    return view('inline-translation::inline-translation', [
        'isAuthorized' => $isAuthorized,
    ]);
}
```

## Troubleshooting

### Config Not Loading

**Problem**: Changes to config file aren't taking effect

**Solution**:
```bash
php artisan config:clear
php artisan config:cache
```

### Wrong Guard Being Used

**Problem**: Using wrong authentication guard

**Check**:
```php
// In tinker or controller
config('inline-translation.guard'); // Should show your guard

Auth::guard('staff')->check(); // Should return true/false
```

**Fix**:
1. Check `.env` file for `INLINE_TRANSLATION_GUARD`
2. Clear config cache
3. Verify guard exists in `config/auth.php`

### Modal Container Not Found

**Problem**: Modal doesn't appear

**Check**:
1. Container exists in layout: `<div id="inline-translation-modals"></div>`
2. Container ID matches config
3. Alpine.js is loaded
4. Check browser console for errors

**Fix**:
```blade
<!-- Make sure this exists in your layout -->
<div id="{{ config('inline-translation.modal_container_id') }}"></div>
```

## Next Steps

- [Usage Guide](usage.md) - Learn how to use the package
- [How It Works](how-it-works.md) - Understand the internals
- [API Reference](api-reference.md) - Detailed API documentation
