<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $seed = [
            // GePG — Government ePayment Gateway
            ['key' => 'gepg_enabled',            'value' => '0',     'group' => 'gepg',   'description' => 'Master switch for the GePG billing integration.'],
            ['key' => 'gepg_test_mode',          'value' => '1',     'group' => 'gepg',   'description' => 'Use the GePG sandbox/test environment instead of production.'],
            ['key' => 'gepg_biller_code',        'value' => '',      'group' => 'gepg',   'description' => 'GePG Biller Code assigned to the university (e.g. MoCU).'],
            ['key' => 'gepg_sp_system_code',     'value' => 'SP123', 'group' => 'gepg',   'description' => 'Service Provider system code used by GePG for bill generation.'],
            ['key' => 'gepg_psp_username',       'value' => '',      'group' => 'gepg',   'description' => 'PSP username provided by GePG for web-service authentication.'],
            ['key' => 'gepg_psp_password',       'value' => '',      'group' => 'gepg',   'description' => 'PSP password provided by GePG for web-service authentication.'],
            ['key' => 'gepg_psp_certificate',    'value' => '',      'group' => 'gepg',   'description' => 'GePG PSP certificate / public key used to encrypt and sign payloads.'],
            ['key' => 'gepg_production_url',     'value' => 'https://gfs.gepg.go.tz/gfs_billrequest', 'group' => 'gepg', 'description' => 'GePG production bill-request SOAP endpoint.'],
            ['key' => 'gepg_test_url',           'value' => 'https://gfs.tegov.go.tz/gfs_billrequest',   'group' => 'gepg', 'description' => 'GePG sandbox/test bill-request SOAP endpoint.'],
            ['key' => 'gepg_currency',           'value' => 'TZS',   'group' => 'gepg',   'description' => 'Currency code used when generating control numbers.'],
            ['key' => 'gepg_bill_payment_window','value' => '240',   'group' => 'gepg',   'description' => 'Bill payment validity window in hours before the control number expires.'],

            // NECTA — result verification
            ['key' => 'necta_enabled',           'value' => '0',     'group' => 'necta',  'description' => 'Enable NECTA academic-result verification for O/A-Level applicants.'],
            ['key' => 'necta_test_mode',         'value' => '1',     'group' => 'necta',  'description' => 'Use the NECTA test/staging API instead of production.'],
            ['key' => 'necta_base_url',          'value' => 'https://ws.necta.go.tz',   'group' => 'necta',  'description' => 'NECTA web-service base endpoint.'],
            ['key' => 'necta_api_token',         'value' => '',      'group' => 'necta',  'description' => 'API token / credentials for the NECTA verification service.'],

            // NACTVET — vocational results
            ['key' => 'nactvet_enabled',         'value' => '0',     'group' => 'nactvet','description' => 'Enable NACTVET (VETA) academic-result verification.'],
            ['key' => 'nactvet_test_mode',       'value' => '1',     'group' => 'nactvet','description' => 'Use the NACTVET test/staging API instead of production.'],
            ['key' => 'nactvet_base_url',        'value' => 'https://ws.nactvet.go.tz', 'group' => 'nactvet','description' => 'NACTVET web-service base endpoint.'],
            ['key' => 'nactvet_api_token',       'value' => '',      'group' => 'nactvet','description' => 'API token / credentials for the NACTVET verification service.'],

            // TCU — Tanzania Commission for Universities
            ['key' => 'tcu_enabled',             'value' => '0',     'group' => 'tcu',    'description' => 'Enable TCU integration for higher-education record checks.'],
            ['key' => 'tcu_test_mode',           'value' => '1',     'group' => 'tcu',    'description' => 'Use the TCU test/staging API instead of production.'],
            ['key' => 'tcu_base_url',            'value' => 'https://www.tcu.go.tz',    'group' => 'tcu',    'description' => 'TCU web-service base endpoint.'],
            ['key' => 'tcu_system_code',         'value' => '',      'group' => 'tcu',    'description' => 'TCU system code identifying the university.'],
            ['key' => 'tcu_api_token',           'value' => '',      'group' => 'tcu',    'description' => 'API token / credentials for the TCU verification service.'],
        ];

        foreach ($seed as $row) {
            if (! DB::table('settings')->where('key', $row['key'])->exists()) {
                DB::table('settings')->insert(array_merge($row, ['created_at' => now(), 'updated_at' => now()]));
            }
        }
    }

    public function down(): void
    {
        $groups = ['gepg', 'necta', 'nactvet', 'tcu'];
        DB::table('settings')->whereIn('group', $groups)->delete();
    }
};