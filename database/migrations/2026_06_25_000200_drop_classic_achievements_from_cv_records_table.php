<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('cv_records', 'classic_achievements')) {
            return;
        }

        DB::table('cv_records')
            ->whereNotNull('classic_achievements')
            ->select(['id', 'experiences', 'classic_achievements'])
            ->orderBy('id')
            ->each(function (object $record): void {
                $experiences = json_decode((string) $record->experiences, true) ?: [];
                $achievements = json_decode((string) $record->classic_achievements, true) ?: [];

                foreach (['tr', 'en'] as $locale) {
                    if (
                        empty($achievements[$locale])
                        || ! is_array($achievements[$locale])
                        || empty($experiences[$locale])
                        || ! is_array($experiences[$locale])
                    ) {
                        continue;
                    }

                    $lines = collect($achievements[$locale])
                        ->map(function (mixed $achievement): ?string {
                            if (! is_array($achievement)) {
                                return null;
                            }

                            $title = trim((string) ($achievement['title'] ?? ''));
                            $description = trim((string) ($achievement['description'] ?? ''));

                            if ($description === '') {
                                return null;
                            }

                            return $title !== '' ? '- '.$title.': '.$description : '- '.$description;
                        })
                        ->filter()
                        ->values()
                        ->all();

                    if ($lines === []) {
                        continue;
                    }

                    $existing = trim((string) ($experiences[$locale][0]['detailed_description'] ?? ''));
                    $experiences[$locale][0]['detailed_description'] = trim($existing."\n".implode("\n", $lines));
                }

                DB::table('cv_records')
                    ->where('id', $record->id)
                    ->update([
                        'experiences' => json_encode($experiences, JSON_UNESCAPED_UNICODE),
                    ]);
            });

        Schema::table('cv_records', function (Blueprint $table) {
            $table->dropColumn('classic_achievements');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('cv_records', 'classic_achievements')) {
            return;
        }

        Schema::table('cv_records', function (Blueprint $table) {
            $table->json('classic_achievements')->nullable()->after('experiences');
        });
    }
};
