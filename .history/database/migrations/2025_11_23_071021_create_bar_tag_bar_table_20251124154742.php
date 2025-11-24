<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bar_tag', function (Blueprint $table) {
            $table->foreignId('bar_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['bar_id','tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bar_tag');
    }
};
