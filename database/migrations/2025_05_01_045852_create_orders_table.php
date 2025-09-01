<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('order_id')->primary();
            $table->uuid('user_id');
            $table->text('items');
            $table->timestamp('order_date');
            $table->enum('status', ['pending', 'cancelled', 'shipped']);
            $table->decimal('total_amount', 15, 2);
            $table->text('address');
            $table->enum('payment_method', ['COD', 'Bank']);
            $table->enum('payment_status', ['pending', 'paid', 'cancelled'])->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}