<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActorKinshipTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actor_kinship', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('kinship_id');
            $table->unsignedBigInteger('actor_id');
            $table->unsignedBigInteger('relative_id');

            $table->foreign('kinship_id')->references('id')->on('kinships');
            $table->foreign('actor_id')->references('id')->on('actors');
            $table->foreign('relative_id')->references('id')->on('actors');
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
        Schema::dropIfExists('actor_kinship');
    }
}
