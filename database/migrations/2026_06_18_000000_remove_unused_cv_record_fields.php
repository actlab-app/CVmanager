<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['short_summary', 'contact_note'] as $column) {
            if (Schema::hasColumn('cv_records', $column)) {
                Schema::table('cv_records', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }

    public function down(): void
    {
        Schema::table('cv_records', function (Blueprint $table) {
            if (! Schema::hasColumn('cv_records', 'short_summary')) {
                $table->json('short_summary')->nullable()->after('job_title');
            }

            if (! Schema::hasColumn('cv_records', 'contact_note')) {
                $table->json('contact_note')->nullable()->after('about_content');
            }
        });
    }
};
