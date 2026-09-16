<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_step_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workflow_step_id')->constrained('application_workflow_steps')->cascadeOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['application_id', 'workflow_step_id'], 'app_step_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_step_completions');
    }
};