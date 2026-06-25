<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cv_records', function (Blueprint $table) {
            $table->json('classic_profile_summary')->nullable()->after('about_content');
        });
    }

    public function down(): void
    {
        Schema::table('cv_records', function (Blueprint $table) {
            $table->dropColumn('classic_profile_summary');
        });
    }
};
