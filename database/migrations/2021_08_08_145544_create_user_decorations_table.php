<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDecorationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_decorations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('decoration_id')
                ->nullable(false);
            $table->unsignedBigInteger('user_id')
                ->nullable(false);
            $table->timestamps();
        });


        Schema::table('user_decorations', function($table) {
            $table->foreign('decoration_id')
                ->references('id')
                ->on('decorations')
                ->onDelete('cascade');

            $table->foreign('user_id')
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
        Schema::dropIfExists('user_decorations');
    }
}
