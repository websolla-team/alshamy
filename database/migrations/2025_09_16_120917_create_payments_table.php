<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
                      $table->integer('order_id')->nullable(); // special_reference
            $table->integer('amount'); // المبلغ بـ القروش
            $table->string('currency')->default('EGP');
            $table->string('payment_method')->nullable(); // Visa - VodafoneCash
            $table->string('status')->default('pending'); // pending - paid - failed
            $table->string('transaction_id')->nullable(); // ID من Paymob
            $table->json('raw_response')->nullable(); // تخزين الريسبونس كامل للرجوع ليه
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
