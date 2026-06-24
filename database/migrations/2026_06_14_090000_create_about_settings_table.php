<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_settings', function (Blueprint $table) {
            $table->id();
            $table->json('eyebrow')->nullable();
            $table->json('headline')->nullable();
            $table->json('intro')->nullable();
            $table->json('current_label')->nullable();
            $table->json('current_status')->nullable();
            $table->json('current_text')->nullable();
            $table->json('philosophy_title')->nullable();
            $table->json('philosophy_text')->nullable();
            $table->json('quote')->nullable();
            $table->json('quote_attribution')->nullable();
            $table->json('portfolio_cta')->nullable();
            $table->json('contact_cta')->nullable();
            $table->json('hero_panels')->nullable();
            $table->json('focus_cards')->nullable();
            $table->string('profile_image_path')->nullable();
            $table->boolean('profile_is_personal')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_settings');
    }
};
