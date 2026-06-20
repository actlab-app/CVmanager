<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reference_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reference_token_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->text('landing_url');
            $table->text('referrer')->nullable();
            $table->char('ip_hash', 64)->nullable();
            $table->char('user_agent_hash', 64)->nullable();
            $table->timestamp('visited_at');
            $table->timestamps();

            $table->index(['reference_token_id', 'visited_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reference_visits');
    }
};
