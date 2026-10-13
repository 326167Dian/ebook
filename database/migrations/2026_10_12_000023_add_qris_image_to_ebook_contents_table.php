<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ebook_contents', function (Blueprint $table) {
            $table->string('payment_qris_image_path')->nullable()->after('payment_bank_account_holder');
        });
    }

    public function down(): void
    {
        Schema::table('ebook_contents', function (Blueprint $table) {
            $table->dropColumn('payment_qris_image_path');
        });
    }
};
