<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToCorpPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('corp_photos', function (Blueprint $table) {
            $table->index('corporation_name');
            $table->index('uploader');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    Schema::table('corp_photos', function (Blueprint $table) {
        $table->dropIndex('corporation_name');
        $table->dropIndex('uploader');
    });
   }
}
