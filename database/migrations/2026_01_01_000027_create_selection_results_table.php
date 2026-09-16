<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('selection_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('selection_batch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('programme_id')->constrained()->cascadeOnDelete();
            $table->string('status', 30)->default('NOT_SELECTED');
            $table->decimal('rank_score', 6, 2)->default(0);
            $table->unsignedInteger('position')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();

            $table->unique(['selection_batch_id', 'application_id', 'programme_id'], 'sel_result_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('selection_results');
    }
};