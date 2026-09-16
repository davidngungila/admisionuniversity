<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admission_window_id')->constrained()->cascadeOnDelete();
            $table->string('application_number')->nullable()->unique();
            $table->string('status', 40)->default('DRAFT');
            $table->string('current_step', 60)->nullable();
            $table->unsignedInteger('progress_percentage')->default(0);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->index(['applicant_id', 'admission_window_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};