<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends \Illuminate\Database\Migrations\Migration {
    public function up(): void
    {
        Schema::table('annonces', function (Blueprint $table) {
            if (Schema::hasColumn('annonces', 'from_city')) {
                $table->string('from_city')->nullable()->change();
            }
            if (Schema::hasColumn('annonces', 'to_city')) {
                $table->string('to_city')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('annonces', function (Blueprint $table) {
            if (Schema::hasColumn('annonces', 'from_city')) {
                $table->string('from_city')->nullable(false)->change();
            }
            if (Schema::hasColumn('annonces', 'to_city')) {
                $table->string('to_city')->nullable(false)->change();
            }
        });
    }
};