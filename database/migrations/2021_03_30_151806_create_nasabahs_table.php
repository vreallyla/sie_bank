<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNasabahsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nasabahs', function (Blueprint $table) {
            $table->id();
            $table->string('nik',100)->unique();
            $table->date('tgl_real');
            $table->string('no_debitur',35);
            $table->string('nama',100);
            $table->date('tgl_lahir');
            $table->string('alamat_anggunan',150);
            $table->string('alamat_instansi',150);
            $table->foreignId('wilayah_id')->constrained('wilayahs');
            $table->foreignId('pekerjaan_id')->constrained('pekerjaans');
            $table->foreignId('penghasilan_id')->constrained('penghasilans');
            $table->foreignId('pendidikan_id')->constrained('pendidikans');
            $table->foreignId('kolektibilitas_id')->constrained('kolektibilitas');
            $table->foreignId('produk_id')->constrained('produks');
            $table->foreignId('team_id')->constrained('team_pemasarans');
            $table->string('telp',60);


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
        Schema::dropIfExists('nasabahs');
    }
}
