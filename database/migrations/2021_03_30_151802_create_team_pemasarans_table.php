<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamPemasaransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_pemasarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_leader_id')->constrained('users');
            $table->foreignId('wilayah_id')->constrained('wilayahs');
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
        Schema::dropIfExists('team_pemasarans');
    }
}
