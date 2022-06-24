<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MhsBerkas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mhs_berkas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nim');
            $table->integer('periode');
            $table->string('penggunaan_bh')->nullable();
            $table->string('bukti_pencairan_bh')->nullable();
            $table->string('khs')->nullable();
            $table->string('prestasi')->nullable();
            $table->string('status');
            $table->timestamps();
            
            $table->foreign('nim')->references('nim')->on('mhs_bio')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mhs_berkas');
    }
}
