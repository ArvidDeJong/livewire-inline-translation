---
title: "Usage"
nav_order: 3
description: "One complete example of an editable translation in Blade, then the HTML editor, several locales, reading the stored value elsewhere and where not to use it."
---

# Usage

## A complete example: an editable heading and intro

Three files. After this, `/about` shows a heading and an intro that an editor can change on the page.

`lang/en/website.php`, the texts as they are before anyone edits them:

```php
<?php

return [
    'about_title' => 'About us',
    'about_intro' => 'We build websites.<br>Since 2010.',
];
```

`routes/web.php`:

```php
use Illuminate\Support\Facades\Route;

Route::view('/about', 'about');
```

`resources/views/about.blade.php`:

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>About us</title>
</head>
<body>
    <h1><livewire:inline-translation translation-key="website.about_title" /></h1>

    <div><livewire:inline-translation translation-key="website.about_intro" :html="true" /></div>

    <div id="inline-translation-modals"></div>
</body>
</html>
```

What happens when someone opens `/about`:

- Each tag looks up its key in the `translations` table for the current locale. There is no row yet, so it shows the text from `lang/en/website.php`.
- A visitor who is not logged in on the [configured guard](configuration.md) gets the text and nothing else.
- An editor sees a dashed blue underline under both texts. A click opens the modal: a text field for the heading, a small editor with bold, italic and a bullet list for the intro, because of `:html="true"`.
- "Save" stores the value in the `translations` table and closes the modal. From then on every visitor gets the stored text. The language file is not changed.

## The key has the form group.key

The part before the first dot is the group, which is the name of the language file. The rest is the key in that file.

| `translation-key` | Language file | Stored as |
| --- | --- | --- |
| `website.welcome` | `lang/en/website.php`, key `welcome` | group `website`, key `welcome` |
| `website.hero.title` | `lang/en/website.php`, key `hero.title` | group `website`, key `hero.title` |
| `welcome` | none | not stored; the component shows the word `welcome` and "Save" does nothing |

The key has to point at a text. A key that points at an array in the language file (`website.hero` in the second row) makes the page fail; see [Troubleshooting](troubleshooting.md#the-page-fails-with-return-value-must-be-of-type-string-array-returned).

JSON translation strings, the ones without a group such as `__('Welcome')`, are not supported.

## Plain text or the HTML editor

| | `translation-key="..."` | `translation-key="..." :html="true"` |
| --- | --- | --- |
| In the modal | a text field | an editor with bold, italic and a bullet list |
| Use it for | headings, short sentences | a few lines with line breaks or emphasis, such as opening hours |

`:html="true"` only changes the editor. In both modes the stored value is put on the page as HTML, without escaping. A `<` typed in the text field is HTML too, and an editor can store any markup, including script. Give the guard only to people you trust with that, or [strip tags before saving](configuration.md#limit-the-html-an-editor-may-store).

Two things to know about the editor:

- It sends its content to the server 500 ms after the last change. Wait a moment after typing before you click "Save"; a click inside that half second stores the text from before the last change.
- It removes `<div>` tags and turns each closing `</div>` into `<br>`, because that is what a browser inserts for a new line. Other tags, for example `<p>` from pasted text, are stored as they are.

## Several locales

A value is stored for `app()->getLocale()`. An editor who changes the text on the Dutch page changes the `nl` row; the English page keeps its own text.

Saving happens in a separate request that Livewire sends to its own update route. Middleware on your page route does not run for that request, unless you tell Livewire to keep it. If a middleware sets the locale (from a URL prefix or the session), register it as persistent middleware, or the edit is stored under the default locale.

`app/Providers/AppServiceProvider.php`:

```php
use App\Http\Middleware\SetLocale;
use Livewire\Livewire;

public function boot(): void
{
    Livewire::addPersistentMiddleware([
        SetLocale::class,
    ]);
}
```

`SetLocale` stands for your own middleware class. The section "Configuring persistent middleware" of the [Livewire security documentation](https://livewire.laravel.com/docs/security) explains the mechanism.

## Read the stored value somewhere else

Only the component reads the database. `__('website.welcome')`, `@lang` and `trans()` keep returning the language file, also after an edit. Where you need the edited text outside the component, in a mail, a meta tag or a controller, do the same lookup the component does:

```php
use Darvis\LivewireInlineTranslation\Models\Translation;

$title = Translation::getTranslation(app()->getLocale(), 'website', 'about_title')
    ?? __('website.about_title');
```

`getTranslation()` returns `null` when nobody has edited the text yet, so the `??` falls back to the language file. The value can contain HTML; escape it with `e($title)` where HTML is not allowed.

## Go back to the language file

A database row keeps winning, also after you change the language file. Delete the row to fall back:

```php
use Darvis\LivewireInlineTranslation\Models\Translation;

Translation::where('locale', 'en')
    ->where('group', 'website')
    ->where('key', 'about_title')
    ->delete();
```

Saving an empty text does not do this. It stores an empty value, the text disappears from the page, and there is nothing left to click. Delete the row in that case too.

## Where the component does not belong

The component renders a `<span>` element with Livewire's attributes around the text, and for an editor a click on it opens the modal.

| Do not use it | Why | Use instead |
| --- | --- | --- |
| inside `<a>`, `<button>` or `<label>` | the click opens the modal and also follows the link or submits the form | `__('website.contact')` |
| in an attribute (`alt`, `title`, `placeholder`), in `<title>` or a meta tag | a `<span>` is not valid there | the lookup from "Read the stored value somewhere else" |
| in a mail or a PDF | there is no Livewire there | the same lookup |

It fits headings, paragraphs and other text that stands on its own.

## Next

- [Configuration](configuration.md): the guard, the modal container and narrowing who may edit
- [Testing](testing.md): test a page that contains the component
