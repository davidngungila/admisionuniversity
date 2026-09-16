<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $seed = [
            ['key' => 'sms_enabled',    'value' => '1',          'group' => 'sms', 'description' => 'Master switch for SMS notifications (1 = on, 0 = off).'],
            ['key' => 'sms_provider',   'value' => 'log',        'group' => 'sms', 'description' => 'SMS provider: log (dev, records only) or messaging (messaging-service.co.tz live).'],
            ['key' => 'sms_sender_id',  'value' => 'TANZANIATIP','group' => 'sms', 'description' => 'Sender ID (from) registered on the network. Set in Messaging Service dashboard.'],
            ['key' => 'sms_api_key',    'value' => '',           'group' => 'sms', 'description' => 'Bearer token from Messaging Service dashboard (Customer Info -> Customization -> API Keys).'],
            ['key' => 'sms_test_mode',  'value' => '1',          'group' => 'sms', 'description' => 'Use the free test endpoints (1) so no credits are charged, or live (0) for real delivery.'],
        ];

        foreach ($seed as $row) {
            $exists = DB::table('settings')->where('key', $row['key'])->exists();
            if (! $exists) {
                DB::table('settings')->insert(array_merge($row, ['created_at' => now(), 'updated_at' => now()]));
            }
        }
    }

    public function down(): void
    {
        DB::table('settings')
            ->whereIn('key', ['sms_enabled', 'sms_provider', 'sms_sender_id', 'sms_api_key', 'sms_test_mode'])
            ->delete();
    }
};