<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Harga promo khusus (override) untuk reseller/kode tertentu, mis. kode event
     * seperti "JUMAT". Kosong = pakai harga promo standar (payment_price_final).
     */
    public function up(): void
    {
        Schema::table('reseller', function (Blueprint $table) {
            $table->unsignedInteger('custom_price_final')->nullable()->after('promo_code');
        });
    }

    public function down(): void
    {
        Schema::table('reseller', function (Blueprint $table) {
            $table->dropColumn('custom_price_final');
        });
    }
};
