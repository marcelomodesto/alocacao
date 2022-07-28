<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_informations', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('nomcur');
            $table->string('codcur');
            $table->string('numsemidl');
            $table->string('perhab');
            $table->string('codhab');
            $table->string('nomhab');
            $table->string('tipobg');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_informations');
    }
}
