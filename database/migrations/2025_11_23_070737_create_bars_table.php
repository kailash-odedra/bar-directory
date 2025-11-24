<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bars', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('short_description')->nullable();
            $table->longText('full_description')->nullable();

            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('video_url')->nullable();

            $table->decimal('avg_rating', 3, 2)->default(0.00);
            $table->unsignedInteger('reviews_count')->default(0);

            $table->boolean('claimed')->default(false);
            $table->foreignId('claimed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('claim_verification_status', ['pending','approved','rejected'])->default('pending');

            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();

            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('youtube')->nullable();
            $table->string('website')->nullable();

            $table->json('features_json')->nullable();
            $table->json('opening_hours_json')->nullable();
            $table->json('holiday_hours_json')->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);

            $table->foreignId('last_updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_featured']);
            $table->index(['avg_rating']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bars');
    }
};
