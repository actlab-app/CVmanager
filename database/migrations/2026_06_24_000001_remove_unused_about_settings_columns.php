<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('about_settings')) {
            return;
        }

        Schema::table('about_settings', function (Blueprint $table): void {
            foreach (['principles_title', 'principles', 'hero_image_path'] as $column) {
                if (Schema::hasColumn('about_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('about_settings')) {
            return;
        }

        Schema::table('about_settings', function (Blueprint $table): void {
            if (! Schema::hasColumn('about_settings', 'principles_title')) {
                $table->json('principles_title')->nullable()->after('philosophy_text');
            }

            if (! Schema::hasColumn('about_settings', 'principles')) {
                $table->json('principles')->nullable()->after('focus_cards');
            }

            if (! Schema::hasColumn('about_settings', 'hero_image_path')) {
                $table->string('hero_image_path')->nullable()->after('principles');
            }
        });
    }
};
