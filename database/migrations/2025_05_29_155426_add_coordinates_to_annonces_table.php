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
            $table->decimal('from_lat', 10, 7)->nullable()->after('from_city');
            $table->decimal('from_lng', 10, 7)->nullable()->after('from_lat');
            $table->decimal('to_lat', 10, 7)->nullable()->after('to_city');
            $table->decimal('to_lng', 10, 7)->nullable()->after('to_lat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('annonces', function (Blueprint $table) {
            //
        });
    }
};
