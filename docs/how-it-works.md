# How It Works

This document explains the internal workings of the Livewire Inline Translation package. Understanding these concepts will help you use the package effectively and troubleshoot issues.

## Architecture Overview

The package consists of four main components:

1. **Translation Model** - Database storage and retrieval
2. **Livewire Component** - UI and user interaction
3. **Service Provider** - Laravel integration
4. **Blade View** - Visual presentation

```
┌─────────────────────────────────────────────────────────────┐
│                      User Interface                          │
│  ┌────────────────────────────────────────────────────────┐ │
│  │  Blade View (inline-translation.blade.php)             │ │
│  │  - Shows text with/without edit indicator              │ │
│  │  - Renders modal for editing                           │ │
│  └────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
                            ↕
┌─────────────────────────────────────────────────────────────┐
│                   Livewire Component                         │
│  ┌────────────────────────────────────────────────────────┐ │
│  │  InlineTranslation.php                                 │ │
│  │  - Handles user interactions                           │ │
│  │  - Manages modal state                                 │ │
│  │  - Coordinates between view and model                  │ │
│  └────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
                            ↕
┌─────────────────────────────────────────────────────────────┐
│                    Translation Model                         │
│  ┌────────────────────────────────────────────────────────┐ │
│  │  Translation.php                                       │ │
│  │  - Database operations                                 │ │
│  │  - getTranslation() - Retrieve                         │ │
│  │  - setTranslation() - Save/Update                      │ │
│  └────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
                            ↕
┌─────────────────────────────────────────────────────────────┐
│                      Database Layer                          │
│  ┌────────────────────────────────────────────────────────┐ │
│  │  translations table                                    │ │
│  │  - Stores custom translations                          │ │
│  │  - Indexed for fast lookups                            │ │
│  └────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

## Component Lifecycle

### 1. Component Initialization

When you use `<livewire:inline-translation translationKey="website.welcome" />`:

```php
public function mount(string $translationKey): void
{
    // Store the translation key
    $this->translationKey = $translationKey;
    
    // Get the current translation value
    $this->translationValue = $this->getTranslation();
}
```

**What happens**:
1. Livewire creates a component instance
2. The `translationKey` is passed to `mount()`
3. `getTranslation()` is called to fetch the current value
4. The component is ready to render

### 2. Translation Retrieval

The `getTranslation()` method implements the two-tier priority system:

```php
protected function getTranslation(): string
{
    // Parse the translation key
    $parts = explode('.', $this->translationKey, 2);
    
    if (count($parts) !== 2) {
        return $this->translationKey; // Invalid format
    }
    
    [$group, $key] = $parts;
    $locale = app()->getLocale();
    
    // PRIORITY 1: Check database for custom translation
    $customTranslation = Translation::getTranslation($locale, $group, $key);
    
    if ($customTranslation !== null) {
        return $customTranslation; // Found in database
    }
    
    // PRIORITY 2: Fallback to Laravel language files
    return __($this->translationKey);
}
```

**Flow**:
```
translationKey: "website.welcome"
         ↓
Split into: group="website", key="welcome"
         ↓
Get locale: "en"
         ↓
Query database:
  SELECT value FROM translations
  WHERE locale='en' AND group='website' AND key='welcome'
         ↓
Found? → Return database value
Not found? → Return __('website.welcome')
```

### 3. Rendering

The component renders differently based on authorization:

```blade
@if ($isAuthorized)
    <!-- Editable version with blue underline -->
    <span wire:click="openModal" style="...">
        {!! $translationValue !!}
    </span>
    
    @if ($showModal)
        <!-- Modal for editing -->
    @endif
@else
    <!-- Read-only version -->
    {!! $translationValue !!}
@endif
```

**Authorization Check**:
```php
public function render()
{
    $guardName = config('inline-translation.guard', 'staff');
    $isAuthorized = Auth::guard($guardName)->check();
    
    return view('inline-translation::inline-translation', [
        'isAuthorized' => $isAuthorized,
    ]);
}
```

### 4. User Interaction

When an authorized user clicks the text:

```php
public function openModal(): void
{
    // Refresh the translation value (in case it changed)
    $this->translationValue = $this->getTranslation();
    
    // Show the modal
    $this->showModal = true;
}
```

Livewire automatically:
1. Updates the component state
2. Re-renders the view
3. Shows the modal via Alpine.js

### 5. Saving Changes

When the user saves:

```php
public function save(): void
{
    // Parse the translation key
    $parts = explode('.', $this->translationKey, 2);
    
    if (count($parts) !== 2) {
        return; // Invalid format
    }
    
    [$group, $key] = $parts;
    $locale = app()->getLocale();
    
    // Save to database
    Translation::setTranslation($locale, $group, $key, $this->translationValue);
    
    // Close the modal
    $this->showModal = false;
}
```

**Database Operation**:
```php
public static function setTranslation(string $locale, string $group, string $key, string $value): self
{
    return self::updateOrCreate(
        [
            'locale' => $locale,
            'group' => $group,
            'key' => $key,
        ],
        [
            'value' => $value,
        ]
    );
}
```

This uses Laravel's `updateOrCreate()`:
- **If record exists**: Updates the `value` field
- **If record doesn't exist**: Creates a new record

## Database Schema

The `translations` table structure:

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

**Indexes**:
- **Unique constraint**: Prevents duplicate translations
- **Individual indexes**: Fast lookups by locale, group, or key
- **Composite unique**: Ensures one translation per locale/group/key combination

**Example Records**:
```
| id | locale | group   | key     | value                    | created_at | updated_at |
|----|--------|---------|---------|--------------------------|------------|------------|
| 1  | en     | website | welcome | Welcome to our platform! | ...        | ...        |
| 2  | nl     | website | welcome | Welkom op ons platform!  | ...        | ...        |
| 3  | en     | website | hero    | Your success starts here | ...        | ...        |
```

## Modal System

The package uses Alpine.js `x-teleport` to move the modal to a specific container:

```blade
@if ($showModal)
    <template x-teleport="#inline-translation-modals">
        <div class="modal-backdrop">
            <div class="modal-content">
                <!-- Modal content -->
            </div>
        </div>
    </template>
@endif
```

**Why Teleport?**

1. **Z-index Issues**: Prevents stacking context problems
2. **Positioning**: Ensures modal appears above all content
3. **Accessibility**: Keeps modals at the top level of DOM
4. **Consistency**: All modals appear in the same container

**Without Teleport**:
```html
<div class="page-content" style="position: relative; z-index: 1;">
    <div class="modal" style="z-index: 999;">
        <!-- Modal might be hidden behind other elements -->
    </div>
</div>
```

**With Teleport**:
```html
<body>
    <div class="page-content" style="position: relative; z-index: 1;">
        <!-- Component here -->
    </div>
    
    <div id="inline-translation-modals">
        <div class="modal" style="z-index: 9999;">
            <!-- Modal always on top -->
        </div>
    </div>
</body>
```

## Service Provider

The service provider handles Laravel integration:

```php
public function boot(): void
{
    // Register Livewire component
    Livewire::component('inline-translation', InlineTranslation::class);
    
    // Load migrations (for package development)
    $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    
    // Load views with namespace
    $this->loadViewsFrom(__DIR__.'/../resources/views', 'inline-translation');
    
    // Publish assets
    $this->publishes([...]);
}
```

**Auto-Discovery**:

Laravel automatically discovers the service provider via `composer.json`:

```json
"extra": {
    "laravel": {
        "providers": [
            "Darvis\\LivewireInlineTranslation\\InlineTranslationServiceProvider"
        ]
    }
}
```

No manual registration needed!

## Performance Considerations

### Database Queries

Each component instance makes **one query** during mount:

```php
// This query is cached by Eloquent
Translation::where('locale', $locale)
    ->where('group', $group)
    ->where('key', $key)
    ->value('value');
```

**Optimization Tips**:

1. **Use Caching**: Cache translations for production
2. **Eager Loading**: Not applicable (single record lookup)
3. **Indexes**: Already optimized with database indexes

### Livewire Overhead

Each component adds minimal overhead:
- **Initial render**: ~1-2ms
- **Re-render**: ~0.5-1ms
- **Network**: ~1KB per component

**Best Practice**: Use for content that changes, not for static UI elements.

## Security

### XSS Protection

The component uses `{!! !!}` to render HTML:

```blade
{!! $translationValue !!}
```

**Why?** Translations may contain legitimate HTML (bold, links, etc.)

**Risk**: Malicious HTML could be injected

**Mitigation**:
1. Only authorized users can edit (staff guard)
2. Trusted users should validate content
3. Consider adding HTML sanitization for extra security

### SQL Injection

Protected by Eloquent's parameter binding:

```php
// Safe - uses parameter binding
Translation::where('locale', $locale)
    ->where('group', $group)
    ->where('key', $key);
```

### CSRF Protection

Livewire handles CSRF automatically - no additional protection needed.

## Debugging

### Enable Livewire Debugging

```blade
@livewireScripts(['debug' => true])
```

### Check Component State

In browser console:
```javascript
// Find component
Livewire.find('component-id')

// Check properties
Livewire.find('component-id').get('translationKey')
Livewire.find('component-id').get('translationValue')
```

### Database Queries

Enable query logging:
```php
DB::enableQueryLog();
// ... use component ...
dd(DB::getQueryLog());
```

## Next Steps

- [API Reference](api-reference.md) - Detailed API documentation
- [Extending](extending.md) - Customize the package
- [Contributing](../CONTRIBUTING.md) - Contribute to the package
