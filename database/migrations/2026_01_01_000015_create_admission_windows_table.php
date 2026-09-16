<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admission_windows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admission_level_id')->constrained()->cascadeOnDelete();
            $table->foreignId('application_round_id')->constrained()->cascadeOnDelete();
            $table->string('applicant_category', 30)->default('Tanzanian');
            $table->dateTime('opens_at');
            $table->dateTime('closes_at');
            $table->string('timezone', 60)->default('Africa/Dar_es_Salaam');
            $table->decimal('application_fee', 12, 2)->default(0);
            $table->string('currency', 10)->default('TZS');
            $table->string('payment_reference_format', 60)->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->index(
                ['admission_level_id', 'academic_year_id', 'application_round_id', 'applicant_category'],
                'admission_windows_cat_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_windows');
    }
};