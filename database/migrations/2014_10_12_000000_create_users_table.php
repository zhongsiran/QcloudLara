<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_name')->nullable();
            $table->string('user_real_name')->nullable();

            $table->string('scjg_openid')->unique()->comment('局公众号的OPEN ID')->nullable();
            $table->string('slaic_openid')->unique()->comment('测试号的OPEN ID');;
            $table->string('wx_nickname')->nullable();

            $table->string('password')->nullable();

            $table->string('user_group')->default('normal_user');
            $table->boolean('active_status')->default(0);
            $table->string('user_aic_division')->nullable();
            $table->rememberToken();
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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('users');
    }
}
