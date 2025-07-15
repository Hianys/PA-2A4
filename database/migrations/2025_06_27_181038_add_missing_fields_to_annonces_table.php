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
        Schema::table('annonces', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->decimal('volume', 8, 2)->nullable();
            $table->string('photo')->nullable();
            $table->text('constraints')->nullable();
            // ⚠️ On a supprimé la colonne "status" qui est déjà dans une autre migration
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('annonces', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->dropColumn('weight');
            $table->dropColumn('volume');
            $table->dropColumn('photo');
            $table->dropColumn('constraints');
        });
    }
};