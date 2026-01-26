<?php

namespace Darvis\LivewireInlineTranslation;

use Darvis\LivewireInlineTranslation\Models\Translation;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class InlineTranslation extends Component
{
    public string $translationKey = '';

    public string $translationValue = '';

    public bool $showModal = false;

    public function mount(string $translationKey): void
    {
        $this->translationKey = $translationKey;
        $this->translationValue = $this->getTranslation();
    }

    public function openModal(): void
    {
        $this->translationValue = $this->getTranslation();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function save(): void
    {
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

    public function render()
    {
        $guardName = config('inline-translation.guard', 'staff');
        $isAuthorized = Auth::guard($guardName)->check();

        return view('inline-translation::inline-translation', [
            'isAuthorized' => $isAuthorized,
        ]);
    }
}
