<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE transport_segments MODIFY status ENUM(
            'en attente',
            'accepté',
            'refusé',
            'publiée',
            'en attente de paiement',
            'payée',
            'bloqué',
            'prise en charge',
            'complétée',
            'archivée'
        ) DEFAULT 'en attente'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE transport_segments MODIFY status ENUM(
            'en attente',
            'accepté',
            'refusé'
        ) DEFAULT 'en attente'");
    }
};