<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('cooking_plans') || ! Schema::hasTable('recipes')) {
            return;
        }

        $exists = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'cooking_plans')
            ->where('CONSTRAINT_NAME', 'cooking_plans_recipe_id_foreign')
            ->exists();

        if (! $exists) {
            Schema::table('cooking_plans', function (Blueprint $table) {
                $table->foreign('recipe_id')
                    ->references('id')
                    ->on('recipes')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('cooking_plans')) {
            return;
        }

        $exists = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'cooking_plans')
            ->where('CONSTRAINT_NAME', 'cooking_plans_recipe_id_foreign')
            ->exists();

        if ($exists) {
            Schema::table('cooking_plans', function (Blueprint $table) {
                $table->dropForeign('cooking_plans_recipe_id_foreign');
            });
        }
    }
};
