<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kode promo huruf+angka (mis. untuk fitur kode promo Meta Ads) sebagai
     * alternatif dari ID reseller angka. Disimpan dalam huruf besar.
     */
    public function up(): void
    {
        Schema::table('reseller', function (Blueprint $table) {
            $table->string('promo_code', 20)->nullable()->unique()->after('id_reseller');
        });
    }

    public function down(): void
    {
        Schema::table('reseller', function (Blueprint $table) {
            $table->dropUnique(['promo_code']);
            $table->dropColumn('promo_code');
        });
    }
};
