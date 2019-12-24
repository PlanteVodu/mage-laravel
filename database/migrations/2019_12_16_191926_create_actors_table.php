<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('note')->nullable();

            $table->date('date_start')->nullable();
            $table->date('date_end')->nullable();

            $enum = ['exactly', 'circa', 'before', 'after'];
            $table->enum('date_start_accuracy', $enum)->nullable();
            $table->enum('date_end_accuracy', $enum)->nullable();

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
        Schema::dropIfExists('actors');
    }
}
