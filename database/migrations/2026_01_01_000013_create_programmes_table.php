<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programmes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campus_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admission_level_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 30)->unique();
            $table->unsignedInteger('duration_years')->default(3);
            $table->string('study_mode', 30)->default('Full Time');
            $table->decimal('tuition_fee', 12, 2)->default(0);
            $table->unsignedInteger('capacity')->nullable();
            $table->text('description')->nullable();
            $table->text('career_opportunities')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->index(['admission_level_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programmes');
    }
};