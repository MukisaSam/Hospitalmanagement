<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->restrictOnDelete();
            $table->decimal('amount_paid', 12, 2);
            $table->enum('payment_method', ['cash', 'card', 'mobile_money', 'bank_transfer', 'insurance']);
            $table->string('reference_number', 100)->nullable();
            $table->date('payment_date');
            $table->unsignedBigInteger('paid_by')->nullable();
            $table->foreign('paid_by')->references('id')->on('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
