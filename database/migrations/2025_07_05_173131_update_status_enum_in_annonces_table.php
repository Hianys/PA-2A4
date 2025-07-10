<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('annonces', function (Blueprint $table) {
            $table->enum('status', [
                'publiée',
                'prise en charge',
                'complétée',
                'archivée',
            ])->default('publiée')->change();
        });
    }

    public function down(): void
    {
        Schema::table('annonces', function (Blueprint $table) {
            $table->enum('status', [
                'publiée',
                'prise en charge',
                'complétée',
            ])->default('publiée')->change();
        });
    }
};

