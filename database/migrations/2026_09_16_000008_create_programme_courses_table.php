<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programme_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programme_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('year');
            $table->unsignedTinyInteger('semester')->default(1);
            $table->string('course_code', 30)->nullable();
            $table->string('course_name')->nullable();
            $table->decimal('credit_hours', 5, 1)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programme_courses');
    }
};
