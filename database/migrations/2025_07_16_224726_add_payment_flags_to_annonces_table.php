<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('annonces', function (Blueprint $table) {
            $table->boolean('is_paid')->default(false);
            $table->boolean('is_confirmed')->default(false);
            $table->unsignedBigInteger('livreur_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('annonces', function (Blueprint $table) {
            $table->dropColumn('is_paid');
            $table->dropColumn('is_confirmed');
        });
    }
};
