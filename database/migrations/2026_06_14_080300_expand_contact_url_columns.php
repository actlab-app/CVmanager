<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_settings', function (Blueprint $table) {
            $table->text('map_url')->nullable()->change();
        });

        Schema::table('contact_items', function (Blueprint $table) {
            $table->text('url')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('contact_settings', function (Blueprint $table) {
            $table->string('map_url')->nullable()->change();
        });

        Schema::table('contact_items', function (Blueprint $table) {
            $table->string('url')->nullable()->change();
        });
    }
};
