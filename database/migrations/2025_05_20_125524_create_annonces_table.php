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
        $table->string('status')->default('published');
        $table->unsignedBigInteger('provider_id')->nullable()->after('user_id');
    });
}

public function down(): void
{
    Schema::table('annonces', function (Blueprint $table) {
        $table->dropColumn('status');
        $table->dropColumn('provider_id');
    });
}
};
