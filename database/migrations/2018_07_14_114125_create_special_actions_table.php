<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpecialActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('special_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sp_name')->nullable();
            $table->string('sp_num')->nullable();
            $table->unsignedInteger('sp_corp_id')->nullable();
            $table->string('corporation_name')->nullable();
            $table->string('registration_num')->index();
            $table->string('predefined_name')->nullable();
            $table->string('predefined_registration_num')->nullable();
            $table->string('finish_status')->nullable();
            $table->string('start_inspect_time')->nullable();
            $table->string('end_inspect_time')->nullable();
            $table->string('inspection_record')->nullable();
            $table->string('phone_call_record')->nullable();
            $table->string('sp_aic_division')->nullable();
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
        Schema::dropIfExists('special_actions');
    }
}
