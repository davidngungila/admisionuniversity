<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->boolean('acceptance_confirmed')->default(false)->after('submitted_at');
            $table->timestamp('acceptance_confirmed_at')->nullable()->after('acceptance_confirmed');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['acceptance_confirmed', 'acceptance_confirmed_at']);
        });
    }
};