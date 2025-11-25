<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add status to bars
        Schema::table('bars', function (Blueprint $table) {
            $table->tinyInteger('status')->default(2)->after('updated_at'); // 1=Active, 2=Inactive
        });

        // Add status to events
        Schema::table('events', function (Blueprint $table) {
            $table->tinyInteger('status')->default(2)->after('updated_at'); // 1=Active, 2=Inactive
        });

        // Add status to bar_tags
        Schema::table('tags', function (Blueprint $table) {
            $table->tinyInteger('status')->default(1)->after('updated_at'); // 1=Active, 2=Inactive
        });
        Schema::table('bar_menu_categories', function (Blueprint $table) {
            $table->tinyInteger('status')->default(1)->after('updated_at'); // 1=Active, 2=Inactive
        });
        Schema::table('bar_menu_items', function (Blueprint $table) {
            $table->tinyInteger('status')->default(1)->after('updated_at'); // 1=Active, 2=Inactive
        });

    }

    public function down(): void
    {
        // Drop for additional tables if added
    }
};
