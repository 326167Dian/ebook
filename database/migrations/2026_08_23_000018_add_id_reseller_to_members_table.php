<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->unsignedBigInteger('id_reseller')->nullable()->after('paid_at');
            $table->foreign('id_reseller')->references('id_reseller')->on('reseller')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['id_reseller']);
            $table->dropColumn('id_reseller');
        });
    }
};
