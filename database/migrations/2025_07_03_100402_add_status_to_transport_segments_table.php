<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transport_segments', function (Blueprint $table) {
            $table->enum('status', ['en attente', 'accepté', 'refusé'])
                ->default('en attente')
                ->after('to_lng');
        });
    }

    public function down(): void
    {
        Schema::table('transport_segments', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
