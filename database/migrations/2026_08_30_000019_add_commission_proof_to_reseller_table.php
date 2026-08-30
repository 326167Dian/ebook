<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reseller', function (Blueprint $table) {
            $table->string('commission_proof_path', 255)->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('reseller', function (Blueprint $table) {
            $table->dropColumn('commission_proof_path');
        });
    }
};