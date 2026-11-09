<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sizes')) {
            Schema::create('sizes', function (Blueprint $table) {
                $table->id();
                $table->json('name');
                $table->timestamps();
            });
        }

        if (Schema::hasTable('categories') && ! Schema::hasColumn('categories', 'order_num')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->unsignedInteger('order_num')->nullable();
            });
        }

        if (Schema::hasTable('variants')) {
            if (Schema::hasColumn('variants', 'color_id')) {
                Schema::table('variants', function (Blueprint $table) {
                    $table->foreignId('color_id')->nullable()->change();
                });
            } else {
                Schema::table('variants', function (Blueprint $table) {
                    $table->foreignId('color_id')->nullable()->constrained()->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        // Keep existing sizes and variant rows: making color_id required again
        // could invalidate products created without a color.
    }
};
