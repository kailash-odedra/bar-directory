<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bar_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bar_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('type')->default('gallery'); // gallery / thumbnail / video_thumbnail
            $table->string('caption')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
            $table->index('bar_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bar_images');
    }
};
