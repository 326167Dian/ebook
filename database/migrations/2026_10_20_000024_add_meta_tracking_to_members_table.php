<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Data browser yang disimpan saat konsumen upload bukti transfer (QRIS statis),
     * supaya event Purchase yang dikirim lewat Meta Conversions API saat admin
     * meng-approve bisa dicocokkan Meta dengan klik iklan konsumen tersebut.
     */
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('meta_fbp', 255)->nullable()->after('payment_rejected_at');
            $table->string('meta_fbc', 255)->nullable()->after('meta_fbp');
            $table->string('meta_client_ip', 45)->nullable()->after('meta_fbc');
            $table->text('meta_user_agent')->nullable()->after('meta_client_ip');
            $table->string('meta_source_url', 500)->nullable()->after('meta_user_agent');
            $table->timestamp('meta_purchase_sent_at')->nullable()->after('meta_source_url');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn([
                'meta_fbp',
                'meta_fbc',
                'meta_client_ip',
                'meta_user_agent',
                'meta_source_url',
                'meta_purchase_sent_at',
            ]);
        });
    }
};
