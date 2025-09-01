<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('product_id')->primary();
            $table->foreignId('category_id')->constrained('categories');
            $table->text('username')->nullable();
            $table->string('brand', 255)->nullable();
            $table->integer('price')->nullable();
            $table->text('image')->nullable();
            $table->text('ratings')->nullable();
            $table->jsonb('attributes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}