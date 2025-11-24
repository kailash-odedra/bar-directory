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
        Schema::create('bar_menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bar_menu_category_id')->constrained('bar_menu_categories')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('price', 8,2);
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bar_menu_items');
    }
};
