<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->string('exam_type', 40);
            $table->string('exam_body', 80)->nullable();
            $table->string('index_number', 40)->nullable();
            $table->string('school_name', 191)->nullable();
            $table->unsignedSmallInteger('exam_year')->nullable();
            $table->json('results')->nullable();
            $table->string('overall_grade', 20)->nullable();
            $table->unsignedInteger('total_subjects')->default(0);
            $table->unsignedInteger('passes_count')->default(0);
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_results');
    }
};