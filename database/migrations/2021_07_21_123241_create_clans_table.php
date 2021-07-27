<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clans', function (Blueprint $table) {
            $table->id();
            $table->string('avatar')
                ->default(null);
            $table->string('title');
            $table->text('description')
                ->nullable(true);
            $table->unsignedBigInteger('owner_id')
                ->nullable(false);
            $table->boolean('closed')
                ->default(true);   // Закрыт ли клан
            $table->integer('slots')
                ->default(50);      // Количество слотов
            $table->float('score')
                ->default(0.0);       // Очки рейтинга
            $table->timestamps();
        });

        Schema::table('clans', function($table) {
            $table->foreign('owner_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clans');
    }
}
