<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('bar_id')->constrained('bars')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();

            $table->unsignedSmallInteger('people_count')->default(1);

            $table->date('booking_date');
            $table->time('booking_time'); // start time
            $table->integer('duration_minutes')->default(120); // default 2 hours
            $table->dateTime('ends_at')->nullable(); // computed for convenience

            $table->string('table_area')->nullable(); // optional table number/area
            $table->text('special_request')->nullable();

            $table->decimal('price', 10, 2)->nullable();

            $table->enum('status', ['pending','confirmed','cancelled','completed'])->default('pending');

            $table->boolean('created_by_admin')->default(false);

            $table->timestamps();

            // indexes for filtering
            $table->index(['bar_id', 'booking_date']);
            $table->index('status');
            $table->index('booking_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
