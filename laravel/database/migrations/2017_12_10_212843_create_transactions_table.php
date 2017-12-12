<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('plate_number');
            $table->string('type');
            $table->double('gvw');
            $table->double('axle_load_1');
            $table->double('axle_load_2');
            $table->double('axle_load_3');
            $table->double('axle_load_4');
            $table->double('axle_load_5');
            $table->double('axle_load_6');
            $table->double('axle_load_7');
            $table->double('axle_load_8');
            $table->string('area_of_operation');
            $table->string('affiliation');
            $table->date('date');
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
        Schema::dropIfExists('transactions');
    }
}
