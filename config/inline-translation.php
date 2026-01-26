<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Guard
    |--------------------------------------------------------------------------
    |
    | This option controls which authentication guard should be used to
    | determine if a user is authorized to edit translations inline.
    | Default is 'staff', but you can change it to 'web' or any other guard.
    |
    */

    'guard' => env('INLINE_TRANSLATION_GUARD', 'user'),

    /*
    |--------------------------------------------------------------------------
    | Modal Container ID
    |--------------------------------------------------------------------------
    |
    | The ID of the container element where modals will be teleported to.
    | Make sure this element exists in your layout.
    |
    */

    'modal_container_id' => 'inline-translation-modals',
];
