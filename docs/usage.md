# Usage Guide

This guide explains how to use the Livewire Inline Translation package in your application.

## Basic Usage

The simplest way to use the package is with the Livewire component:

```blade
<livewire:inline-translation translationKey="website.welcome" />
```

### Translation Key Format

The `translationKey` must follow this format: `{group}.{key}`

- **group**: The language file name (without `.php`)
- **key**: The translation key within that file

**Examples**:
- `website.welcome` → `lang/en/website.php` → `['welcome' => '...']`
- `messages.hello` → `lang/en/messages.php` → `['hello' => '...']`
- `auth.failed` → `lang/en/auth.php` → `['failed' => '...']`

## How It Works

### For Visitors (Not Logged In)

When a visitor views the page, they see the translated text normally:

```blade
<livewire:inline-translation translationKey="website.welcome" />
<!-- Renders: Welcome to our website! -->
```

### For Authorized Users (Logged In)

When an authorized user (e.g., staff member) views the page:

1. **Visual Indicator**: Text appears with a blue dashed underline
2. **Click to Edit**: Clicking the text opens an edit modal
3. **Edit & Save**: User can edit the translation and save
4. **Instant Update**: The text updates immediately without page reload
5. **Database Storage**: Custom translation is saved to database

## Real-World Examples

### Example 1: Homepage Hero Text

```blade
<header class="hero">
    <h1>
        <livewire:inline-translation translationKey="website.hero_title" />
    </h1>
    <p>
        <livewire:inline-translation translationKey="website.hero_subtitle" />
    </p>
</header>
```

**Language file** (`lang/en/website.php`):
```php
return [
    'hero_title' => 'Welcome to Our Platform',
    'hero_subtitle' => 'The best solution for your business',
];
```

### Example 2: Button Text

```blade
<button type="submit">
    <livewire:inline-translation translationKey="website.submit_button" />
</button>
```

### Example 3: Rich Content with HTML

The component supports HTML in translations:

```blade
<div class="content">
    <livewire:inline-translation translationKey="website.rich_content" />
</div>
```

**Language file**:
```php
return [
    'rich_content' => 'This is <strong>bold</strong> and <em>italic</em> text with a <a href="#">link</a>.',
];
```

### Example 4: Multiple Languages

The package respects Laravel's locale:

```blade
<!-- Dutch version (nl) -->
<livewire:inline-translation translationKey="website.welcome" />
<!-- Renders: Welkom op onze website! -->

<!-- English version (en) -->
<livewire:inline-translation translationKey="website.welcome" />
<!-- Renders: Welcome to our website! -->
```

## Translation Priority

The package uses a two-tier system:

1. **Database** (Highest Priority)
   - Custom translations saved via inline editing
   - Stored in `translations` table
   - Can override any language file

2. **Language Files** (Fallback)
   - Laravel's default `lang/{locale}/{file}.php` files
   - Used when no database translation exists

**Example Flow**:
```
User requests: website.welcome
↓
1. Check database for custom translation
   - Found? → Return custom translation ✓
   - Not found? → Continue to step 2
↓
2. Check language file
   - Found? → Return file translation ✓
   - Not found? → Return translation key
```

## Important Limitations

### ❌ Do NOT Use Inside Clickable Elements

**Wrong**:
```blade
<a href="/contact">
    <livewire:inline-translation translationKey="website.contact" />
</a>

<button onclick="doSomething()">
    <livewire:inline-translation translationKey="website.button" />
</button>
```

**Why**: The component creates a clickable `<span>` for authorized users, which conflicts with parent clickable elements.

**Correct**:
```blade
<a href="/contact">
    {{ __('website.contact') }}
</a>

<button onclick="doSomething()">
    {{ __('website.button') }}
</button>
```

### ✅ Best Practices

**Use for**:
- Headings and titles
- Paragraphs and descriptions
- Static content blocks
- Hero sections
- Feature descriptions

**Don't use for**:
- Navigation links
- Button text inside `<a>` or `<button>`
- Form labels inside `<label>`
- Alt text for images

## Authorization

### Default Guard

By default, only users authenticated via the `staff` guard can edit translations:

```php
// This user CAN edit
Auth::guard('staff')->login($staffUser);

// This user CANNOT edit
Auth::guard('web')->login($regularUser);
```

### Changing the Guard

To allow regular users to edit:

```env
INLINE_TRANSLATION_GUARD=web
```

Or in `config/inline-translation.php`:
```php
return [
    'guard' => 'web',
];
```

### Custom Authorization Logic

If you need more complex authorization, you can extend the component:

```php
namespace App\Livewire;

use ArvidDeJong\LivewireInlineTranslation\InlineTranslation as BaseInlineTranslation;

class InlineTranslation extends BaseInlineTranslation
{
    public function render()
    {
        // Custom authorization logic
        $isAuthorized = auth()->check() && auth()->user()->can('edit-translations');

        return view('inline-translation::inline-translation', [
            'isAuthorized' => $isAuthorized,
        ]);
    }
}
```

## Workflow Example

Let's walk through a complete workflow:

### 1. Developer Creates Language File

```php
// lang/en/website.php
return [
    'welcome' => 'Welcome to our website!',
];
```

### 2. Developer Adds Component to View

```blade
<h1>
    <livewire:inline-translation translationKey="website.welcome" />
</h1>
```

### 3. Staff Member Edits Translation

1. Staff logs in via staff guard
2. Visits the page
3. Sees "Welcome to our website!" with blue underline
4. Clicks the text
5. Modal opens showing:
   - Key: `website.welcome`
   - Current value: `Welcome to our website!`
6. Changes to: `Welcome to our amazing platform!`
7. Clicks "Save"
8. Modal closes
9. Text updates immediately

### 4. Database Record Created

```sql
INSERT INTO translations (locale, group, key, value)
VALUES ('en', 'website', 'welcome', 'Welcome to our amazing platform!');
```

### 5. All Visitors See Updated Text

From now on, everyone sees: `Welcome to our amazing platform!`

The original language file remains unchanged, making it easy to revert if needed.

## Tips & Tricks

### Tip 1: Use Descriptive Keys

**Good**:
```php
'hero_title' => '...',
'hero_subtitle' => '...',
'cta_button' => '...',
```

**Bad**:
```php
'text1' => '...',
'text2' => '...',
'btn' => '...',
```

### Tip 2: Group Related Translations

```php
// lang/en/homepage.php
return [
    'hero_title' => '...',
    'hero_subtitle' => '...',
    'feature_1_title' => '...',
    'feature_1_description' => '...',
];
```

### Tip 3: Preview Before Publishing

Test translations in a staging environment before deploying to production.

### Tip 4: Export Database Translations

You can export custom translations to share across environments:

```php
$translations = \ArvidDeJong\LivewireInlineTranslation\Models\Translation::all();
```

## Next Steps

- [Configuration](configuration.md) - Learn about all configuration options
- [How It Works](how-it-works.md) - Understand the internals
- [API Reference](api-reference.md) - Detailed API documentation
