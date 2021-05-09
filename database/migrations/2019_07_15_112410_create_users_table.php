<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('users', function (Blueprint $table) {
            $table->string('id')->unique()->index();
            $table->string('username')->default("بدون نام");
            $table->integer('score')->default(0);
            $table->integer('coins')->default(0);
            $table->integer('wins')->default(0);
            $table->integer('ties')->default(0);
            $table->integer('loses')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('users');
    }
}
