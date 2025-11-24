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
        Schema::create('bars', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('slug')->unique();
        $table->text('short_description')->nullable();
        $table->longText('full_description')->nullable();
        $table->string('logo')->nullable();
        $table->string('cover_image')->nullable();
        $table->boolean('claimed')->default(false);
        $table->foreignId('claimed_by')->nullable()->constrained('users');
        $table->boolean('verified')->default(false);
        $table->string('meta_title')->nullable();
        $table->string('meta_description')->nullable();
        $table->string('meta_keywords')->nullable();
        $table->string('facebook')->nullable();
        $table->string('instagram')->nullable();
        $table->string('tiktok')->nullable();
        $table->string('youtube')->nullable();
        $table->string('website')->nullable();
        $table->boolean('status')->default(true);
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bars');
    }
};
