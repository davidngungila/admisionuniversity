<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_deletions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_id')->nullable()->index();
            $table->string('application_number', 191)->nullable();
            $table->unsignedBigInteger('applicant_id')->nullable()->index();
            $table->string('applicant_name', 255)->nullable();
            $table->string('applicant_email', 191)->nullable();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('reason');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_deletions');
    }
};