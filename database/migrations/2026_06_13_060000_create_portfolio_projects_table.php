<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_projects', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('title');
            $table->json('short_description')->nullable();
            $table->json('detailed_description')->nullable();
            $table->json('project_type')->nullable();
            $table->json('role')->nullable();
            $table->json('duration')->nullable();
            $table->json('platform')->nullable();
            $table->string('status')->default('draft');
            $table->date('project_date')->nullable();
            $table->string('live_url')->nullable();
            $table->string('repository_url')->nullable();
            $table->json('technologies')->nullable();
            $table->json('features')->nullable();
            $table->json('technical_decisions')->nullable();
            $table->json('metrics')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_projects');
    }
};
