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
        Schema::table('recipes', function (Blueprint $table) {
            $table->index('created_at', 'recipes_created_at_index');
            $table->index('difficulty', 'recipes_difficulty_index');
            $table->index('cooking_time', 'recipes_cooking_time_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropIndex('recipes_created_at_index');
            $table->dropIndex('recipes_difficulty_index');
            $table->dropIndex('recipes_cooking_time_index');
        });
    }
};
