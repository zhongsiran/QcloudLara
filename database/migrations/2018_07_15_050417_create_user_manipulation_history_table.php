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
            $table->increments('id');

            $table->string('scjg_openid')->unique()->comment('局公众号的OPEN ID');
            $table->string('slaic_openid')->unique()->comment('测试号的OPEN ID');;
            $table->string('wx_nickname');

            $table->string('current_manipulating_corporation');
            $table->string('previous_manipulated_corporation');

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
