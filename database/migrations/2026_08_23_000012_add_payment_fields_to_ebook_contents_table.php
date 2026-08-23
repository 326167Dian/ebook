<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ebook_contents', function (Blueprint $table) {
            $table->unsignedInteger('payment_price_original')->default(250000)->after('chapters');
            $table->unsignedInteger('payment_price_final')->default(99000)->after('payment_price_original');
            $table->string('payment_note', 255)->default('Admin akan verifikasi sebelum akses penuh dibuka.')->after('payment_price_final');
        });
    }

    public function down(): void
    {
        Schema::table('ebook_contents', function (Blueprint $table) {
            $table->dropColumn(['payment_price_original', 'payment_price_final', 'payment_note']);
        });
    }
};
