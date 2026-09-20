<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Guard
    |--------------------------------------------------------------------------
    |
    | The authentication guard that decides who may edit a translation inline.
    | A visitor who is not logged in on this guard sees the translation but
    | gets no edit affordance and cannot open the modal. 'web' is the guard
    | every Laravel application has; point this at your own editor guard if
    | you have one. A guard this application does not define is treated as
    | "nobody may edit", so a typo here costs you the editing, not the page.
    |
    */

    'guard' => env('INLINE_TRANSLATION_GUARD', 'web'),

    /*
    |--------------------------------------------------------------------------
    | Modal Container ID
    |--------------------------------------------------------------------------
    |
    | The id of the element the edit modal is teleported into. Make sure an
    | element with this id exists in your layout, outside any element with
    | its own stacking context, or the modal will be clipped.
    |
    */

    'modal_container_id' => 'inline-translation-modals',
];
