<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bar_timings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bar_id')->constrained()->cascadeOnDelete();
            $table->string('weekday'); // Monday..Sunday
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->timestamps();
            $table->index(['bar_id','weekday']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bar_timings');
    }
};
