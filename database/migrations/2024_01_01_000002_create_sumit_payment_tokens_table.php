<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sumit_payment_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('token');
            $table->string('card_type')->default('card');
            $table->string('card_last4', 4);
            $table->string('card_brand')->nullable();
            $table->string('exp_month', 2);
            $table->string('exp_year', 4);
            $table->string('citizen_id')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sumit_payment_tokens');
    }
};
