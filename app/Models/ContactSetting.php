<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ContactSetting extends Model
{
    use HasTranslations;

    public array $translatable = [
        'title',
        'intro',
        'form_title',
        'privacy_notice',
        'success_message',
        'location',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'privacy_hidden' => 'boolean',
        ];
    }
}
