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
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content')->nullable(); // HTML/WYSIWYG content
            $table->string('section_type')->default('custom'); // hero, about, services, testimonials, etc.
            $table->string('page')->nullable(); // home, about, contact, etc. or null for global
            $table->integer('order')->default(0); // for ordering sections
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable(); // for storing additional settings like colors, images, etc.
            $table->string('image')->nullable(); // section image/background
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->timestamps();
            
            $table->index(['page', 'is_active']);
            $table->index(['section_type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
