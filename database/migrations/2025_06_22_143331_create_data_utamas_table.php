<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataUtamasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_utamas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('subunit_id')->nullable();
            $table->unsignedBigInteger('jenis_data_ptj_id')->unique();
            $table->boolean('is_kpi')->default(false);
            $table->string('pi_no')->nullable();
            $table->decimal('pi_target', 8, 2)->nullable();
            $table->string('doc_link')->nullable();
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
        Schema::dropIfExists('data_utamas');
    }
}
