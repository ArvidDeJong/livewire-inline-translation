<?php

declare(strict_types=1);

namespace Darvis\LivewireInlineTranslation\Support;

/**
 * The one place that reads the package config. Callers ask this class, so a default is written
 * once and a caller cannot quietly disagree with the config file about what it is.
 */
final class InlineTranslationConfig
{
    /**
     * The authentication guard that decides who may edit a translation.
     */
    public static function guard(): string
    {
        $guard = config('inline-translation.guard', 'web');

        return is_string($guard) && $guard !== '' ? $guard : 'web';
    }

    /**
     * The id of the element the edit modal is teleported into.
     */
    public static function modalContainerId(): string
    {
        $id = config('inline-translation.modal_container_id', 'inline-translation-modals');

        return is_string($id) && $id !== '' ? $id : 'inline-translation-modals';
    }
}
