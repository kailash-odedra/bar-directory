<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('states', function (Blueprint $table) {
            if (! Schema::hasColumn('states', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('name');
            }

            if (! Schema::hasColumn('states', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('slug');
            }
        });

        DB::table('states')
            ->select('id', 'name', 'slug')
            ->orderBy('id')
            ->lazy()
            ->each(function ($state) {
                if (! $state->slug) {
                    DB::table('states')
                        ->where('id', $state->id)
                        ->update(['slug' => Str::slug($state->name) ?: (string) Str::uuid()]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('states', function (Blueprint $table) {
            if (Schema::hasColumn('states', 'is_active')) {
                $table->dropColumn('is_active');
            }

            if (Schema::hasColumn('states', 'slug')) {
                $table->dropUnique('states_slug_unique');
                $table->dropColumn('slug');
            }
        });
    }
};

