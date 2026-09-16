<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->string('application_type', 40)->nullable()->after('exam_index_number');
            $table->date('olevel_completion_date')->nullable()->after('application_type');
        });
    }

    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn(['application_type', 'olevel_completion_date']);
        });
    }
};