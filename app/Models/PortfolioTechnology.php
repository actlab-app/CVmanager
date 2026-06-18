<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class PortfolioTechnology extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::deleted(function (PortfolioTechnology $technology): void {
            $technology->deleteManagedLogo();
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function deleteManagedLogo(): void
    {
        if (! $this->logo_path || ! str_starts_with($this->logo_path, 'images/technologies/catalog/')) {
            return;
        }

        $path = public_path($this->logo_path);

        if (File::isFile($path)) {
            File::delete($path);
        }
    }
}
