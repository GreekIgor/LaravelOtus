<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pivot_ingredient_recipe', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->integer('ingredient_id')->foreign()->references('id')->on('ingredients')->onDelete('cascade');
            $table->integer('recipe_id')->foreign()->references('id')->on('recipes')->onDelete('cascade');
            $table->string('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pivot_ingredient_recipe');
    }
};
