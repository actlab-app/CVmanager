<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_settings', function (Blueprint $table) {
            $table->id();
            $table->json('title')->nullable();
            $table->json('intro')->nullable();
            $table->json('form_title')->nullable();
            $table->json('privacy_notice')->nullable();
            $table->json('success_message')->nullable();
            $table->json('location')->nullable();
            $table->text('map_url')->nullable();
            $table->boolean('privacy_hidden')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_settings');
    }
};
