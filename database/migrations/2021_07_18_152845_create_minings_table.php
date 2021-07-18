<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMiningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('minings', function (Blueprint $table) {
            $table->id();
            $table->string('title');        // Название
            $table->text('description');    // Описание
            $table->float('earn');          // Сколько зарабатывает
            $table->integer('class');       // К какому кейсу относится
            $table->integer('rare');        // Редкость: 1 - обычный, 2 - редкий, 3 - очень редкий, 4 - невозможный
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
        Schema::dropIfExists('minings');
    }
}
