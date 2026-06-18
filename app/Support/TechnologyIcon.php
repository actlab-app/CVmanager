<?php

namespace App\Support;

use Flux\Flux;

class TechnologyIcon
{
    public static function resolve(?string $icon): string
    {
        $icon = match ($icon) {
            'image', 'images' => 'photo',
            'component' => 'cube',
            default => $icon ?: 'code-bracket',
        };

        return Flux::componentExists('icon.'.$icon) ? $icon : 'code-bracket';
    }
}
