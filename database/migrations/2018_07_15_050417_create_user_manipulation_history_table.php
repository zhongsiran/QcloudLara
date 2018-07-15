<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserManipulationHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_manipulation_history', function (Blueprint $table) {

            $table->string('id')->primary()->comment('slaic_openid');
            $table->string('wx_nickname')->nullable();

            $table->string('current_manipulating_corporation')->nullable();
            $table->string('previous_manipulated_corporation')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_manipulation_history');
    }
}
