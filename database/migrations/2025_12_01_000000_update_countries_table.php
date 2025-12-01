<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            if (! Schema::hasColumn('countries', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('name');
            }

            if (! Schema::hasColumn('countries', 'iso_code')) {
                $table->string('iso_code')->nullable()->after('slug')->index();
            }

            if (! Schema::hasColumn('countries', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('iso_code');
            }
        });

        DB::table('countries')
            ->select('id', 'name', 'slug')
            ->orderBy('id')
            ->lazy()
            ->each(function ($country) {
                if (! $country->slug) {
                    DB::table('countries')
                        ->where('id', $country->id)
                        ->update(['slug' => Str::slug($country->name) ?: (string) Str::uuid()]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            if (Schema::hasColumn('countries', 'is_active')) {
                $table->dropColumn('is_active');
            }

            if (Schema::hasColumn('countries', 'iso_code')) {
                $table->dropColumn('iso_code');
            }

            if (Schema::hasColumn('countries', 'slug')) {
                $table->dropUnique('countries_slug_unique');
                $table->dropColumn('slug');
            }
        });
    }
};

