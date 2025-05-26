<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends \Illuminate\Database\Migrations\Migration {
    public function up(): void
    {
        Schema::table('annonces', function (Blueprint $table) {
            $table->string('from_city')->nullable()->change();
            $table->string('to_city')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('annonces', function (Blueprint $table) {
            $table->string('from_city')->nullable(false)->change();
            $table->string('to_city')->nullable(false)->change();
        });
    }
};

