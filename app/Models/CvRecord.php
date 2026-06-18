<?php

namespace App\Models;

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
        'quick_infos',
        'educations',
        'experiences',
        'skills',
        'project_types',
    ];
}
