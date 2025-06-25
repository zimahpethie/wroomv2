<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataJumlahPtjsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_jumlah_ptjs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('data_ptj_id');
            $table->unsignedBigInteger('tahun_id');
            $table->boolean('is_kpi')->default(false);
            $table->string('pi_no')->nullable();
            $table->decimal('pi_target', 12, 2)->nullable();
            $table->decimal('jumlah', 12, 2)->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('data_jumlah_ptjs');
    }
}
