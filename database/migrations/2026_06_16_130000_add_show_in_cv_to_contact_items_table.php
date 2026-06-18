<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_items', function (Blueprint $table) {
            $table->boolean('show_in_cv')->default(false)->after('is_active');
        });

        DB::table('contact_items')
            ->whereIn('icon', ['phone', 'mail', 'globe', 'linkedin'])
            ->update(['show_in_cv' => true]);
    }

    public function down(): void
    {
        Schema::table('contact_items', function (Blueprint $table) {
            $table->dropColumn('show_in_cv');
        });
    }
};
