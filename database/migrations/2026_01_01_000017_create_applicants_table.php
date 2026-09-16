<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('applicant_number')->nullable()->unique();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 20)->nullable();
            $table->foreignId('citizenship_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('exam_index_number', 40)->nullable();
            $table->boolean('disability_status')->default(false);
            $table->string('disability_type')->nullable();
            $table->string('nida_number', 40)->nullable();
            $table->string('marital_status', 30)->nullable();
            $table->string('place_of_birth')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};