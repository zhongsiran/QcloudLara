<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddResponsibleGroupColumnToSpecialActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('special_actions', function (Blueprint $table) {
            $table->string('sp_responsible_group')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('special_actions', function (Blueprint $table) {
            $table->dropColumn(['sp_responsible_group']);
        });
    }
}
