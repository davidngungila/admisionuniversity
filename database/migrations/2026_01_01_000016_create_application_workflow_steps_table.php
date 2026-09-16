<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_level_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('step_number');
            $table->string('step_name');
            $table->string('route');
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_required')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['admission_level_id', 'step_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_workflow_steps');
    }
};