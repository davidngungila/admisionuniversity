<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->string('payment_reference')->unique();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('TZS');
            $table->string('status', 30)->default('PENDING');
            $table->string('payment_method', 40)->nullable();
            $table->string('control_number', 60)->nullable()->index();
            $table->string('phone_number', 30)->nullable();
            $table->string('transaction_id', 80)->nullable();
            $table->decimal('amount_paid', 12, 2)->nullable();
            $table->string('paid_by_name', 191)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->string('receipt_number')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['application_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};