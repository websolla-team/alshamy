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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('code');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_name');
            $table->string('phone');
            $table->string('delivery');
            $table->string('city');
            $table->text('address');
            $table->unsignedDecimal('shipping', 6, 2)->default(0);
            $table->unsignedDecimal('total', 12, 2);
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled', 'returned'])->default('pending');
            $table->boolean('readed')->default(false);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};