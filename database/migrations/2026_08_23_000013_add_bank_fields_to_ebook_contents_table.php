<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ebook_contents', function (Blueprint $table) {
            $table->string('payment_bank_name', 80)->default('BCA')->after('payment_note');
            $table->string('payment_bank_account_number', 50)->default('1391928130')->after('payment_bank_name');
            $table->string('payment_bank_account_holder', 120)->default('Eneng Siti Wulandari')->after('payment_bank_account_number');
        });
    }

    public function down(): void
    {
        Schema::table('ebook_contents', function (Blueprint $table) {
            $table->dropColumn(['payment_bank_name', 'payment_bank_account_number', 'payment_bank_account_holder']);
        });
    }
};
