<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $seed = [
            ['key' => 'admissions_email',   'value' => 'admissions@university.ac.tz', 'group' => 'contact', 'description' => 'Public admissions contact email shown on the site, support panel and documents.'],
            ['key' => 'admissions_phone',   'value' => '+255 26 231 0300',            'group' => 'contact', 'description' => 'Public admissions phone number (Call Now button, documents).'],
            ['key' => 'contact_box',        'value' => 'P.O. Box 259',                'group' => 'contact', 'description' => 'Postal box shown in the footer and documents.'],
            ['key' => 'contact_city',       'value' => 'Dodoma, Tanzania',            'group' => 'contact', 'description' => 'City / country shown in the footer and documents.'],
            ['key' => 'admissions_office',  'value' => 'Directorate of Undergraduate Studies', 'group' => 'contact', 'description' => 'Office name shown under the university in documents and contact blocks.'],
            ['key' => 'contact_campus',     'value' => 'Main Campus',                 'group' => 'contact', 'description' => 'Campus name shown in the site footer contact block.'],
            ['key' => 'support_hours',      'value' => 'Mon-Sat 08:00-20:00 EAT',     'group' => 'contact', 'description' => 'Support helpline availability shown in the support panel.'],
        ];

        foreach ($seed as $row) {
            if (! DB::table('settings')->where('key', $row['key'])->exists()) {
                DB::table('settings')->insert(array_merge($row, ['created_at' => now(), 'updated_at' => now()]));
            }
        }
    }

    public function down(): void
    {
        $keys = array_column([
            ['key' => 'admissions_email'],
            ['key' => 'admissions_phone'],
            ['key' => 'contact_box'],
            ['key' => 'contact_city'],
            ['key' => 'admissions_office'],
            ['key' => 'contact_campus'],
            ['key' => 'support_hours'],
        ], 'key');
        DB::table('settings')->whereIn('key', $keys)->delete();
    }
};