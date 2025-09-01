<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('user_id')->primary();
            $table->string('username', 255);
            $table->string('email', 255)->unique();
            $table->string('password_hash', 255);
            $table->boolean('admin')->default(false);
            $table->date('date_of_birth')->nullable();
            $table->string('profile_image', 255)->default('default.jpg');
            $table->text('cart')->nullable();
            $table->text('buildset');
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}