<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('login');
            $table->string('password');
            $table->string('api_token', 80)
                ->unique()
                ->nullable()
                ->default(null);
            $table->string('provider');
            $table->float('balance_coin', 32, 4)
                ->default(0);
            $table->float('mining_speed', 32, 4)
                ->default(0.0001);
            $table->integer('group_vk')
                ->default(0);
            $table->unsignedBigInteger('clan_id')
                ->default(0);
            $table->timestamps();
        });
        /*
        Schema::table('users', function($table) {
            $table->foreign('clan_id')
                ->references('id')
                ->on('clans');
        });
        */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
