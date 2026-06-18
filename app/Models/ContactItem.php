<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class ContactItem extends Model
{
    use HasTranslations;

    public array $translatable = ['label'];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_private' => 'boolean',
            'is_active' => 'boolean',
            'show_in_cv' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function displayValue(bool $privacyHidden): string
    {
        if (! $privacyHidden || ! $this->is_private) {
            return $this->value;
        }

        if (filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
            [$user, $domain] = explode('@', $this->value, 2);
            [$host, $extension] = array_pad(explode('.', $domain, 2), 2, '');

            return Str::substr($user, 0, 2).'***@'.Str::substr($host, 0, 1).'***'
                .($extension ? '.'.$extension : '');
        }

        $length = Str::length($this->value);

        if ($length <= 4) {
            return '••••';
        }

        return Str::substr($this->value, 0, 2).'••••'.Str::substr($this->value, -2);
    }
}
