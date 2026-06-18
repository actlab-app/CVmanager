<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('about_settings') || Schema::hasColumn('about_settings', 'profile_is_personal')) {
            return;
        }

        Schema::table('about_settings', function (Blueprint $table) {
            $table->boolean('profile_is_personal')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('about_settings', 'profile_is_personal')) {
            return;
        }

        Schema::table('about_settings', function (Blueprint $table) {
            $table->dropColumn('profile_is_personal');
        });
    }
};
