<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $uniName = DB::table('settings')->where('key', 'university_name')->value('value') ?? '';
        $isMocu = stripos($uniName, 'Moshi') !== false || stripos($uniName, 'MoCU') !== false || stripos($uniName, 'Co-operative') !== false;

        $row = [
            'key' => 'university_name_sw',
            'value' => $isMocu ? 'Chuo Kikuu cha Ushirika Moshi' : 'Chuo Kikuu Cha Dodoma',
            'group' => 'general',
            'description' => 'Swahili name of the university shown under the English name in PDFs and headers (e.g. Chuo Kikuu Cha Dodoma for UDOM, Chuo Kikuu cha Ushirika Moshi for MoCU).',
        ];

        if (! DB::table('settings')->where('key', $row['key'])->exists()) {
            DB::table('settings')->insert(array_merge($row, ['created_at' => now(), 'updated_at' => now()]));
        } elseif ($isMocu) {
            $currentSw = DB::table('settings')->where('key', 'university_name_sw')->value('value');
            if ($currentSw === 'Chuo Kikuu Cha Dodoma') {
                DB::table('settings')->where('key', 'university_name_sw')->update(['value' => 'Chuo Kikuu cha Ushirika Moshi', 'updated_at' => now()]);
            }
        }

        // If this is a MoCU installation but contact_city still shows Dodoma, correct it.
        if ($isMocu) {
            $city = DB::table('settings')->where('key', 'contact_city')->value('value');
            if ($city === 'Dodoma, Tanzania') {
                DB::table('settings')->where('key', 'contact_city')->update(['value' => 'Moshi, Kilimanjaro, Tanzania', 'updated_at' => now()]);
            }
        }
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'university_name_sw')->delete();
    }
};