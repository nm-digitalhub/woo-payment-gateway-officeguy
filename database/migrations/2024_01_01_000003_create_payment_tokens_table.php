<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('token')->unique();
            $table->string('last_four', 4)->nullable();
            $table->string('card_type')->nullable(); // visa, mastercard, amex, discover
            $table->date('expiry_date')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('is_default');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_tokens');
    }
};
