<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTableHomeworks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('homeworks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->string('filename');
            $table->string('location');
            $table->boolean('corrected')->default(false);
            $table->unsignedBigInteger('corrector_id')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users');
            $table->foreign('corrector_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('homeworks');
    }
}
