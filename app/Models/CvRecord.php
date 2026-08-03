<?php

namespace App\Models;

use App\Support\ReferenceUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CvRecord extends Model
{
    use HasFactory, HasTranslations;

    protected $guarded = [];

    public $translatable = [
        'job_title',
        'about_content',
        'classic_profile_summary',
        'quick_infos',
        'educations',
        'experiences',
        'skills',
        'project_types',
    ];

    public function resolveQrUrl(?string $token = null): ?string
    {
        $token = $this->qr_token ?: $token;

        if ($this->qr_page) {
            $pathMap = [
                'about' => 'about',
                'cv' => 'cv',
                'portfolio' => 'portfolio',
                'portfolio.index' => 'portfolio',
                'contact' => 'contact',
            ];

            $path = $pathMap[$this->qr_page] ?? ltrim((string) $this->qr_page, '/');
            $baseUrl = rtrim(config('app.url', url('/')), '/').'/'.ltrim($path, '/');

            return $token
                ? ReferenceUrl::appendToken($baseUrl, $token)
                : $baseUrl;
        }

        if ($this->qr_url) {
            return $token
                ? ReferenceUrl::appendToken($this->qr_url, $token)
                : $this->qr_url;
        }

        return null;
    }
}

