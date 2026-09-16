<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Demo/sample defaults: both result-verification providers run in TEST MODE by default
        // so the NECTA / NACTVET fetch works out of the box on fresh databases.
        // Flip test_mode to 0 and keep enabled=1 for live verification.
        DB::table('settings')
            ->whereIn('key', ['necta_enabled', 'nactvet_enabled'])
            ->update(['value' => '1', 'updated_at' => now()]);
    }

    public function down(): void
    {
        DB::table('settings')
            ->whereIn('key', ['necta_enabled', 'nactvet_enabled'])
            ->update(['value' => '0', 'updated_at' => now()]);
    }
};