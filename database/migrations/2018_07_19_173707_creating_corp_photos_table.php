<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatingCorpPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corp_photos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('corporation_name');
            $table->string('link');
            $table->string('uploader');
            $table->json('special_actions')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // Schema::table('corp_photos', function(){
        //     DB::statement('ALTER TABLE corp_photos ADD COLUMN special_actions json');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('corp_photos');
    }
}
