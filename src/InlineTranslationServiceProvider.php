<?php

namespace Darvis\LivewireInlineTranslation;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class InlineTranslationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Register Livewire component
        Livewire::component('inline-translation', InlineTranslation::class);

        // Publish migrations
        $this->publishes([
            __DIR__.'/../database/migrations/create_translations_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_translations_table.php'),
        ], 'inline-translation-migrations');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'inline-translation');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/inline-translation'),
        ], 'inline-translation-views');

        // Publish config
        $this->publishes([
            __DIR__.'/../config/inline-translation.php' => config_path('inline-translation.php'),
        ], 'inline-translation-config');
    }

    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../config/inline-translation.php',
            'inline-translation'
        );
    }
}
