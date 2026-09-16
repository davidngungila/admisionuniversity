<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_programmes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('programme_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('preference_order')->default(1);
            $table->string('status', 30)->default('selected');
            $table->timestamps();

            $table->unique(['application_id', 'programme_id'], 'app_programme_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_programmes');
    }
};