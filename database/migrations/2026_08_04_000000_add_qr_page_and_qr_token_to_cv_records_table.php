<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cv_records', function (Blueprint $table) {
            $table->string('qr_page')->nullable()->after('qr_url');
            $table->string('qr_token')->nullable()->after('qr_page');
        });
    }

    public function down(): void
    {
        Schema::table('cv_records', function (Blueprint $table) {
            $table->dropColumn(['qr_page', 'qr_token']);
        });
    }
};
