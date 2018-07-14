<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpecialActionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('special_action', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sp_corp_id');
            $table->string('corporation_name');
            $table->string('registration_num')->unique()->index();
            $table->string('predefined_name');
            $table->string('predefined_registration_num');
            $table->string('finish_status');
            $table->string('start_inspect_time');
            $table->string('end_inspect_time');
            $table->string('inspection_record');
            $table->string('phone_call_record');
            $table->string('sp_aic_division');
            $table->timestamps();
            $table->foreign('registration_num')->references('registration_num')->on('corps');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('special_action');
    }
}
