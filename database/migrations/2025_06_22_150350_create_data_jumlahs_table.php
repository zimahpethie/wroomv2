<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataJumlahsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_jumlahs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('data_utama_id');
            $table->unsignedBigInteger('tahun_id');
            $table->decimal('jumlah', 12, 2)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['data_utama_id', 'tahun_id'], 'uniq_utama_tahun');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_jumlahs');
    }
}
