<?php

namespace Darvis\LivewireInlineTranslation\Models;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    protected $fillable = [
        'locale',
        'group',
        'key',
        'value',
    ];

    public static function getTranslation(string $locale, string $group, string $key): ?string
    {
        return self::where('locale', $locale)
            ->where('group', $group)
            ->where('key', $key)
            ->value('value');
    }

    public static function setTranslation(string $locale, string $group, string $key, string $value): self
    {
        return self::updateOrCreate(
            [
                'locale' => $locale,
                'group' => $group,
                'key' => $key,
            ],
            [
                'value' => $value,
            ]
        );
    }
}
