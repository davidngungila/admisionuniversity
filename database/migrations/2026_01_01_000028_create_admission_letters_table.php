<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admission_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('selection_result_id')->nullable()->constrained()->nullOnDelete();
            $table->string('letter_number')->unique();
            $table->text('content')->nullable();
            $table->string('file_path')->nullable();
            $table->string('status', 30)->default('ISSUED');
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('downloaded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_letters');
    }
};