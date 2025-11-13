<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sumit_recurring_billings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('payment_token_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('ILS');
            $table->decimal('vat_rate', 5, 2)->default(0);
            $table->string('frequency'); // daily, weekly, monthly, yearly
            $table->text('description')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('status')->default('active')->index(); // active, cancelled, suspended
            $table->timestamp('next_payment_date')->nullable()->index();
            $table->timestamp('last_payment_date')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            // Foreign key to payment token - set to null on delete to preserve billing history
            // This allows us to maintain subscription records even after a payment method is removed
            $table->foreign('payment_token_id')
                  ->references('id')
                  ->on('sumit_payment_tokens')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sumit_recurring_billings');
    }
};
