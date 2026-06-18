<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class AboutSetting extends Model
{
    use HasTranslations;

    public array $translatable = [
        'eyebrow',
        'headline',
        'intro',
        'current_label',
        'current_status',
        'current_text',
        'philosophy_title',
        'philosophy_text',
        'principles_title',
        'quote',
        'quote_attribution',
        'portfolio_cta',
        'contact_cta',
        'hero_panels',
        'focus_cards',
        'principles',
    ];

    protected $guarded = [];
}
