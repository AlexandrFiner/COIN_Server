<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClanMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clan_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clan_id')
                ->nullable(false);
            $table->unsignedBigInteger('user_id')
                ->nullable(false);
            $table->enum('role', ['guest', 'invited', 'application', 'member', 'admin', 'moderator', 'owner'])
                ->default("member");
            $table->timestamps();
        });

        Schema::table('clan_members', function($table) {
            $table->foreign('clan_id')
                ->references('id')
                ->on('clans')
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
        Schema::dropIfExists('clan_members');
    }
}
