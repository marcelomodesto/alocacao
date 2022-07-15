<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('codtur');
            $table->string('coddis');
            $table->string('tiptur')->nullable();
            $table->string('nomdis')->nullable();
            $table->integer('estmtr')->unsigned()->nullable();
            $table->timestamp('dtainitur')->nullable();
            $table->timestamp('dtafimtur')->nullable();
            $table->unsignedBigInteger('school_term_id');
            $table->unsignedBigInteger('room_id')->nullable();
            $table->unsignedBigInteger('fusion_id')->nullable();
            $table->unique(['codtur', 'coddis']);
            $table->foreign('school_term_id')->references('id')->on('school_terms')->onDelete('cascade');
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('school_classes');
    }
}
