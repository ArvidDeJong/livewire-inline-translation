<?php

declare(strict_types=1);

use Darvis\LivewireInlineTranslation\Support\InlineTranslationConfig;

/**
 * InlineTranslationConfig is the one place that reads the package config. These tests guard the
 * two things that go wrong once a default is written down twice: an accessor that disagrees with
 * the config file, and a caller that reaches past the accessor and keeps its own stale fallback.
 */
function packageRoot(string $path = ''): string
{
    return dirname(__DIR__, 2).($path === '' ? '' : '/'.$path);
}

it('returns the values the config file ships', function () {
    $config = require packageRoot('config/inline-translation.php');

    expect(InlineTranslationConfig::guard())->toBe($config['guard'])
        ->and(InlineTranslationConfig::modalContainerId())->toBe($config['modal_container_id']);
});

it('follows a changed setting', function () {
    config([
        'inline-translation.guard' => 'staff',
        'inline-translation.modal_container_id' => 'my-modals',
    ]);

    expect(InlineTranslationConfig::guard())->toBe('staff')
        ->and(InlineTranslationConfig::modalContainerId())->toBe('my-modals');
});

it('falls back to the shipped default when a setting is emptied', function () {
    config([
        'inline-translation.guard' => '',
        'inline-translation.modal_container_id' => null,
    ]);

    expect(InlineTranslationConfig::guard())->toBe('web')
        ->and(InlineTranslationConfig::modalContainerId())->toBe('inline-translation-modals');
});

it('is the only place in the package that reads the config', function () {
    $offenders = [];

    foreach (['src', 'resources', 'routes'] as $directory) {
        $path = packageRoot($directory);

        if (! is_dir($path)) {
            continue;
        }

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $relative = str_replace(packageRoot('').'/', '', $file->getPathname());

            if (str_contains($relative, 'InlineTranslationConfig.php')) {
                continue;
            }

            if (preg_match("/config\(['\"]inline-translation\./", (string) file_get_contents($file->getPathname()))) {
                $offenders[] = $relative;
            }
        }
    }

    expect($offenders)->toBe([], 'these read the config directly instead of through InlineTranslationConfig');
});

it('keeps the config keys in alphabetical order', function () {
    preg_match_all(
        "/^    '([a-z_0-9]+)' =>/m",
        (string) file_get_contents(packageRoot('config/inline-translation.php')),
        $matches
    );

    $keys = $matches[1];
    $sorted = $keys;
    sort($sorted);

    expect($keys)->toBe($sorted);
});
