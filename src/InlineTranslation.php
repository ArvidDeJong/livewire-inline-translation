<?php

namespace Darvis\LivewireInlineTranslation;

use Darvis\LivewireInlineTranslation\Models\Translation;
use Darvis\LivewireInlineTranslation\Support\InlineTranslationConfig;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

class InlineTranslation extends Component
{
    /**
     * Locked: the page decides which key this component edits, the browser may not change it.
     */
    #[Locked]
    public string $translationKey = '';

    public string $translationValue = '';

    public bool $showModal = false;

    #[Locked]
    public bool $html = false;

    public function mount(string $translationKey, bool $html = false): void
    {
        $this->translationKey = $translationKey;
        $this->html = $html;
        $this->translationValue = $this->getTranslation();
    }

    public function openModal(): void
    {
        $this->authorizeEditing();

        $this->translationValue = $this->getTranslation();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function save(): void
    {
        $this->authorizeEditing();

        $parts = explode('.', $this->translationKey, 2);

        if (count($parts) !== 2) {
            return;
        }

        [$group, $key] = $parts;
        $locale = app()->getLocale();

        Translation::setTranslation($locale, $group, $key, $this->translationValue);

        $this->showModal = false;
    }

    protected function getTranslation(): string
    {
        $parts = explode('.', $this->translationKey, 2);

        if (count($parts) !== 2) {
            return $this->translationKey;
        }

        [$group, $key] = $parts;
        $locale = app()->getLocale();

        $customTranslation = Translation::getTranslation($locale, $group, $key);

        if ($customTranslation !== null) {
            return $customTranslation;
        }

        return __($this->translationKey);
    }

    public function render(): View
    {
        /** @var view-string $view */
        $view = 'inline-translation::inline-translation';

        return view($view, [
            'isAuthorized' => $this->isAuthorized(),
        ]);
    }

    /**
     * Stop an action for a visitor who may not edit.
     *
     * The component is on the page for every visitor, and the browser can call any public action,
     * so hiding the underline in the view is not a check. Every action that reads for editing or
     * writes goes through here.
     */
    protected function authorizeEditing(): void
    {
        abort_unless($this->isAuthorized(), 403);
    }

    /**
     * Whether the visitor may edit this translation.
     *
     * A guard the application does not define would throw on every render of every page that
     * shows a translation, so a typo in the config costs the editing, not the site.
     */
    protected function isAuthorized(): bool
    {
        $guard = InlineTranslationConfig::guard();

        if (! is_array(config('auth.guards.'.$guard))) {
            return false;
        }

        return Auth::guard($guard)->check();
    }
}
