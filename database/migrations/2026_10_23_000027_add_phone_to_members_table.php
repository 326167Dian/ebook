<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Nomor WhatsApp calon pembeli, diisi di langkah 1 registrasi supaya admin
     * bisa follow up calon pembeli yang belum menyelesaikan pembayaran.
     *
     * Hosting tanpa terminal: jalankan SQL ini di phpMyAdmin.
     *   ALTER TABLE members ADD phone VARCHAR(20) NULL AFTER email;
     *   INSERT INTO migrations (migration, batch)
     *     SELECT '2026_10_23_000027_add_phone_to_members_table', MAX(batch) + 1 FROM migrations;
     */
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('phone');
        });
    }
};
