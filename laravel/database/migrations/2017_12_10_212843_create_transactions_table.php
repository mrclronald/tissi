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
        Schema::create('uploads', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('plate_number');
            $table->string('type');
            $table->double('gvw');
            $table->double('axle_load_1')->default(0);
            $table->double('axle_load_2')->default(0);
            $table->double('axle_load_3')->default(0);
            $table->double('axle_load_4')->default(0);
            $table->double('axle_load_5')->default(0);
            $table->double('axle_load_6')->default(0);
            $table->double('axle_load_7')->default(0);
            $table->double('axle_load_8')->default(0);
            $table->string('area_of_operation');
            $table->string('affiliation');
            $table->date('date');
            $table->integer('upload_id')->unsigned();
            $table->foreign('upload_id')->references('id')->on('uploads');
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
