<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corps', function (Blueprint $table) {
            $table->string('corporation_name');
            $table->string('registration_num');
            $table->string('address')->nullable();
            $table->string('represent_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('nian_bao_status')->nullable();
            $table->string('inspection_status')->nullable();
            $table->string('phone_call_record')->nullable();
            $table->string('longitude')->comment('经度')->nullable();
            $table->string('latitude')->comment('纬度')->nullable();
            $table->unsignedInteger('photos_number')->default(0);
            $table->boolean('is_active')->default('1');
            $table->string('corporation_aic_division')->nullable();
            $table->timestamps();

            $table->primary('registration_num');
            $table->index('corporation_name');
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
        Schema::dropIfExists('corps');
    }
}
