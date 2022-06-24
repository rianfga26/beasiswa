<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MhsBio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mhs_bio', function (Blueprint $table) {
            $table->id('nim');
            $table->string('nama');
            $table->string('prodi');
            $table->string('fakultas');
            $table->integer('periode_masuk');
            $table->string('link_photo');
            $table->string('jalur_penerimaan');
            $table->string('nomor',15)->nullable();
            $table->string('status');
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
        Schema::dropIfExists('mhs_bio');
    }
}
