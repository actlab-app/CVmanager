<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class PortfolioProject extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = [
        'title',
        'short_description',
        'detailed_description',
        'project_type',
        'role',
        'duration',
        'platform',
        'features',
        'technical_decisions',
        'metrics',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'technologies' => 'array',
            'project_date' => 'date',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (PortfolioProject $project): void {
            $project->images()->get()->each->delete();
        });
    }

    public function images(): HasMany
    {
        return $this->hasMany(PortfolioProjectImage::class)->orderBy('sort_order');
    }
}
