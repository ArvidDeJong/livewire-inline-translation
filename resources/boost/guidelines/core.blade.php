## darvis/livewire-inline-translation

Lets an authorised user edit a translation on the page itself: they click the text, a modal opens, and the new value is stored in the `translations` table. Everyone else just sees the text.

- Render it as the Livewire component `inline-translation`: `<livewire:inline-translation translation-key="website.welcome" />`, or `:html="true"` for the small WYSIWYG editor. The key must be `group.key`; a key without a dot is rendered as-is and never saved.
- The lookup order is database first, language file second. `Translation::getTranslation($locale, $group, $key)` returns null when there is no database row, and the component then falls back to `__($key)`. Saving writes a row for the current `app()->getLocale()`, so a translation is per locale.
- Who may edit comes from one guard check, `Auth::guard($guard)->check()`, with `$guard` from the config (default `web`). A guard the application does not define is treated as "nobody may edit" rather than throwing, so a typo costs the editing and not the page. There is no per user or per key permission; if you need one, wrap the component in your own and decide there.
- Config lives under the key `inline-translation` (file `config/inline-translation.php`). Inside the package every setting comes from `Darvis\LivewireInlineTranslation\Support\InlineTranslationConfig`, which holds the defaults; nothing else reads the config, and a test fails on a direct read.
- The modal is teleported with Alpine into the element named by `modal_container_id`. That element has to exist in the layout, outside anything with its own stacking context, or the modal is clipped. Alpine must be loaded; Livewire ships it.
- The stored value is rendered with `{!! !!}`, on purpose, because the HTML mode exists. Whoever may edit a translation may therefore put script on the page. Treat the guard as that level of trust, or sanitise in your own `save()`.
- The `translations` table comes along with `php artisan migrate`; there is nothing to publish. `locale`, `group` and `key` together are unique, so `setTranslation()` updates rather than duplicates.
- In a test, set the guard with `config(['inline-translation.guard' => '...'])` and assert on the rendered output; `Livewire::test()` renders the component, so `assertSee('wire:click="openModal"')` tells you whether the edit affordance is there.

@verbatim
<code-snippet name="Read a translation the same way the component does" lang="php">
use Darvis\LivewireInlineTranslation\Models\Translation;

$value = Translation::getTranslation(app()->getLocale(), 'website', 'welcome') ?? __('website.welcome');
</code-snippet>
@endverbatim
