<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->unsignedBigInteger('commission_amount')->nullable()->after('id_reseller');
            $table->string('commission_proof_path', 255)->nullable()->after('commission_amount');
            $table->timestamp('commission_paid_at')->nullable()->after('commission_proof_path');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['commission_amount', 'commission_proof_path', 'commission_paid_at']);
        });
    }
};