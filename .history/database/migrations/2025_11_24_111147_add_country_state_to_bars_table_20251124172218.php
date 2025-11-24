<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
    {
        Schema::table('bars', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->nullable()->after('id');
            $table->unsignedBigInteger('state_id')->nullable()->after('country_id');
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bars', function (Blueprint $table) {
            //
        });
    }
};
