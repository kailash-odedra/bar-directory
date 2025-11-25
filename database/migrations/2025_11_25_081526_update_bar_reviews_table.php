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
        Schema::table('bar_reviews', function (Blueprint $table) {

            // rename review → comment
            if (Schema::hasColumn('bar_reviews', 'review')) {
                $table->renameColumn('review', 'comment');
            }

            // change boolean status into ENUM
            $table->enum('status', ['pending', 'approved', 'hidden'])
                ->default('pending')
                ->change();
        });
    }

    public function down()
    {
        Schema::table('bar_reviews', function (Blueprint $table) {
            $table->renameColumn('comment', 'review');
            $table->boolean('status')->default(false)->change();
        });
    }

};
