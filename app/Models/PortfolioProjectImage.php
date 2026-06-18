<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\File;
use Spatie\Translatable\HasTranslations;

class PortfolioProjectImage extends Model
{
    use HasTranslations;

    public array $translatable = ['title', 'description'];

    protected $guarded = [];

    protected static function booted(): void
    {
        static::deleted(function (PortfolioProjectImage $image): void {
            $path = public_path($image->path);

            if (File::isFile($path)) {
                File::delete($path);
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(PortfolioProject::class, 'portfolio_project_id');
    }
}
