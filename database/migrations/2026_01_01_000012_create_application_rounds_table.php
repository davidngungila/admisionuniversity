<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_rounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('round_number');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_current')->default(false);
            $table->boolean('allow_multiple_applications')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['academic_year_id', 'round_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_rounds');
    }
};