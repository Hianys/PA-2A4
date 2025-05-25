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
            $table->renameColumn('titre', 'title');
            $table->renameColumn('ville_depart', 'from_city');
            $table->renameColumn('ville_arrivee', 'to_city');
            $table->renameColumn('date_souhaitee', 'preferred_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('annonces', function (Blueprint $table) {
            $table->renameColumn('title', 'titre');
            $table->renameColumn('from_city', 'ville_depart');
            $table->renameColumn('to_city', 'ville_arrivee');
            $table->renameColumn('preferred_date', 'date_souhaitee');
        });
    }
};
