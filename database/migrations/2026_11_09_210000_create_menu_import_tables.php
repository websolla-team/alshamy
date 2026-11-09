<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('menu_import_links')) {
            Schema::create('menu_import_links', function (Blueprint $table) {
                $table->id();
                $table->string('menu_key')->unique();
                $table->foreignId('product_id')->unique()->constrained()->cascadeOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('menu_addons')) {
            Schema::create('menu_addons', function (Blueprint $table) {
                $table->id();
                $table->string('menu_key')->unique();
                $table->string('name');
                $table->string('section');
                $table->json('choices');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Intentionally preserve stable product mappings and addon prices on
        // rollback. up() is idempotent, so running migrations again is safe.
    }
};
