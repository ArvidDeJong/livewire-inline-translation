# API Reference

Complete API documentation for the Livewire Inline Translation package.

## Translation Model

**Namespace**: `ArvidDeJong\LivewireInlineTranslation\Models\Translation`

### Properties

#### `$fillable`

```php
protected $fillable = [
    'locale',
    'group',
    'key',
    'value',
];
```

Mass-assignable attributes.

### Static Methods

#### `getTranslation()`

Retrieve a translation from the database.

```php
public static function getTranslation(
    string $locale, 
    string $group, 
    string $key
): ?string
```

**Parameters**:
- `$locale` (string) - Language code (e.g., 'en', 'nl')
- `$group` (string) - File name (e.g., 'website', 'messages')
- `$key` (string) - Translation key

**Returns**: `?string` - Translation value or `null` if not found

**Example**:
```php
use ArvidDeJong\LivewireInlineTranslation\Models\Translation;

$value = Translation::getTranslation('en', 'website', 'welcome');
// Returns: "Welcome to our platform!" or null
```

#### `setTranslation()`

Save or update a translation in the database.

```php
public static function setTranslation(
    string $locale, 
    string $group, 
    string $key, 
    string $value
): self
```

**Parameters**:
- `$locale` (string) - Language code
- `$group` (string) - File name
- `$key` (string) - Translation key
- `$value` (string) - Translation value

**Returns**: `Translation` - The created or updated model instance

**Example**:
```php
Translation::setTranslation(
    'en', 
    'website', 
    'welcome', 
    'Welcome to our amazing platform!'
);
```

**Note**: Uses `updateOrCreate()` internally, so it will:
- Update existing translation if found
- Create new translation if not found

## InlineTranslation Component

**Namespace**: `ArvidDeJong\LivewireInlineTranslation\InlineTranslation`

### Properties

#### `$translationKey`

```php
public string $translationKey = '';
```

The translation key in `group.key` format.

**Example**: `'website.welcome'`

#### `$translationValue`

```php
public string $translationValue = '';
```

The current translation value (from database or language file).

#### `$showModal`

```php
public bool $showModal = false;
```

Controls modal visibility.

### Methods

#### `mount()`

Initializes the component.

```php
public function mount(string $translationKey): void
```

**Parameters**:
- `$translationKey` (string) - Translation key in `group.key` format

**Called**: Automatically by Livewire when component is created

**Example**:
```blade
<livewire:inline-translation translationKey="website.welcome" />
```

#### `openModal()`

Opens the edit modal.

```php
public function openModal(): void
```

**Called**: When user clicks the translation text (if authorized)

**Side Effects**:
- Refreshes `$translationValue` from database
- Sets `$showModal = true`
- Triggers view re-render

#### `closeModal()`

Closes the edit modal.

```php
public function closeModal(): void
```

**Called**: 
- When user clicks "Cancel"
- When user clicks outside modal
- When user clicks close button

**Side Effects**:
- Sets `$showModal = false`
- Triggers view re-render

#### `save()`

Saves the edited translation to database.

```php
public function save(): void
```

**Called**: When user clicks "Save" in modal

**Process**:
1. Parses `$translationKey` into group and key
2. Gets current locale
3. Saves `$translationValue` to database
4. Closes modal

**Side Effects**:
- Creates/updates database record
- Sets `$showModal = false`
- Updates displayed text

#### `getTranslation()` (protected)

Retrieves the current translation value.

```php
protected function getTranslation(): string
```

**Returns**: `string` - Translation value

**Priority**:
1. Database translation (if exists)
2. Language file translation (fallback)
3. Translation key (if nothing found)

**Example Flow**:
```php
// translationKey = "website.welcome"
// locale = "en"

// 1. Check database
$db = Translation::getTranslation('en', 'website', 'welcome');
if ($db !== null) return $db;

// 2. Check language file
return __('website.welcome');
```

#### `render()`

Renders the component view.

```php
public function render()
```

**Returns**: `\Illuminate\View\View`

**Variables Passed to View**:
- `$isAuthorized` (bool) - Whether user can edit

**Authorization Logic**:
```php
$guardName = config('inline-translation.guard', 'staff');
$isAuthorized = Auth::guard($guardName)->check();
```

## Service Provider

**Namespace**: `ArvidDeJong\LivewireInlineTranslation\InlineTranslationServiceProvider`

### Methods

#### `boot()`

Bootstraps the package.

```php
public function boot(): void
```

**Actions**:
1. Registers Livewire component
2. Loads migrations
3. Loads views
4. Publishes config
5. Publishes views
6. Publishes migrations

#### `register()`

Registers package services.

```php
public function register(): void
```

**Actions**:
- Merges package config with app config

## Blade Directives

### Component Usage

```blade
<livewire:inline-translation translationKey="group.key" />
```

**Attributes**:
- `translationKey` (required) - Translation key in `group.key` format

**Output (Visitor)**:
```html
<span>Welcome to our platform!</span>
```

**Output (Authorized)**:
```html
<span wire:click="openModal" style="cursor: pointer; border-bottom: 1px dashed #3b82f6;">
    Welcome to our platform!
</span>
```

## Database Schema

### `translations` Table

```sql
CREATE TABLE translations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    locale VARCHAR(10) NOT NULL,
    group VARCHAR(100) NOT NULL,
    key VARCHAR(255) NOT NULL,
    value TEXT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    UNIQUE KEY unique_translation (locale, group, key),
    INDEX idx_locale (locale),
    INDEX idx_group (group),
    INDEX idx_key (key)
);
```

**Columns**:
- `id` - Primary key
- `locale` - Language code (max 10 chars)
- `group` - File name (max 100 chars)
- `key` - Translation key (max 255 chars)
- `value` - Translation value (text, unlimited)
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp

**Indexes**:
- Unique constraint on `(locale, group, key)`
- Individual indexes on `locale`, `group`, `key`

## Configuration

### Config Keys

```php
// config/inline-translation.php

return [
    // Authentication guard name
    'guard' => 'staff',
    
    // Modal container element ID
    'modal_container_id' => 'inline-translation-modals',
];
```

### Environment Variables

```env
INLINE_TRANSLATION_GUARD=staff
```

## Events

The package doesn't emit custom events, but you can listen to Livewire events:

```javascript
// Listen for component updates
Livewire.on('component-updated', (component) => {
    console.log('Component updated:', component);
});
```

## Extending the Package

### Custom Component

```php
namespace App\Livewire;

use ArvidDeJong\LivewireInlineTranslation\InlineTranslation as BaseInlineTranslation;

class CustomInlineTranslation extends BaseInlineTranslation
{
    // Override methods as needed
    
    public function save(): void
    {
        // Add logging
        logger('Translation saved', [
            'key' => $this->translationKey,
            'value' => $this->translationValue,
        ]);
        
        // Call parent
        parent::save();
    }
}
```

### Custom View

Publish and modify the view:

```bash
php artisan vendor:publish --tag=inline-translation-views
```

Then edit `resources/views/vendor/inline-translation/inline-translation.blade.php`

### Custom Model

Extend the Translation model:

```php
namespace App\Models;

use ArvidDeJong\LivewireInlineTranslation\Models\Translation as BaseTranslation;

class Translation extends BaseTranslation
{
    // Add custom methods
    
    public static function getByGroup(string $locale, string $group): Collection
    {
        return self::where('locale', $locale)
            ->where('group', $group)
            ->get();
    }
}
```

## Type Hints

For better IDE support:

```php
use ArvidDeJong\LivewireInlineTranslation\Models\Translation;
use ArvidDeJong\LivewireInlineTranslation\InlineTranslation;

/** @var Translation $translation */
$translation = Translation::find(1);

/** @var InlineTranslation $component */
$component = Livewire::test(InlineTranslation::class);
```

## Next Steps

- [Usage Guide](usage.md) - Learn how to use the package
- [How It Works](how-it-works.md) - Understand the internals
- [Contributing](../CONTRIBUTING.md) - Contribute to the package
