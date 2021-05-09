<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->string('IMessageID')->unique()->index();
            $table->text('input1')->nullable();
            $table->text('input2')->nullable();
            $table->tinyInteger('row1')->default(0);
            $table->tinyInteger('row2')->default(0);
            $table->tinyInteger('col1')->default(0);
            $table->tinyInteger('col2')->default(0);
            $table->tinyInteger('pc1')->default(0);
            $table->tinyInteger('pp1')->default(0);
            $table->tinyInteger('pc2')->default(0);
            $table->tinyInteger('pp2')->default(0);
            $table->integer('user1')->default(-1);
            $table->integer('user2')->default(-1);
            $table->string('username1')->nullable();
            $table->string('username2')->nullable();
            $table->string('hash1')->default("[]");
            $table->string('hash2')->default("[]");
            $table->tinyInteger('mode')->default(4);
            $table->tinyInteger('duplicate')->default(-1);
            $table->tinyInteger('round1')->default(0);
            $table->tinyInteger('round2')->default(0);
            $table->tinyInteger('win')->default(-1);
            $table->text('inputs')->nullable();
            $table->string('currentInput1',1024)->default("[]");
            $table->string('currentInput2',1024)->default("[]");
            $table->integer('turn')->default(-1);

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
        Schema::dropIfExists('groups');
    }
}
