<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sumit_payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->nullable()->index();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('ILS');
            $table->string('status')->index(); // completed, failed, pending, refunded
            $table->string('transaction_id')->nullable()->unique();
            $table->string('document_id')->nullable();
            $table->string('customer_id')->nullable();
            $table->string('auth_number')->nullable();
            $table->string('card_last4', 4)->nullable();
            $table->string('card_brand')->nullable();
            $table->json('response_data')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sumit_payment_transactions');
    }
};
