<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->string('action', 255)->change();
            $table->string('method', 10)->nullable()->after('action');
            $table->string('url')->nullable()->after('method');
            $table->unsignedSmallInteger('response_status')->nullable()->after('url');
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropColumn(['method', 'url', 'response_status']);
            $table->string('action', 80)->change();
        });
    }
};