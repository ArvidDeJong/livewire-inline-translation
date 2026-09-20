<?php

namespace Darvis\LivewireInlineTranslation;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class InlineTranslationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('inline-translation', InlineTranslation::class);

        // The translations table comes along with php artisan migrate; there is
        // nothing to publish. Publishing it as well would create the table twice.
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'inline-translation');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/inline-translation'),
        ], 'inline-translation-views');

        $this->publishes([
            __DIR__.'/../config/inline-translation.php' => config_path('inline-translation.php'),
        ], 'inline-translation-config');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/inline-translation.php',
            'inline-translation'
        );
    }
}
