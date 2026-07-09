<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            if (! Schema::hasColumn('members', 'google_id')) {
                $table->string('google_id')->nullable()->unique()->after('email');
            }

            if (! Schema::hasColumn('members', 'avatar')) {
                $table->string('avatar')->nullable()->after('google_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            if (Schema::hasColumn('members', 'avatar')) {
                $table->dropColumn('avatar');
            }

            if (Schema::hasColumn('members', 'google_id')) {
                $table->dropUnique('members_google_id_unique');
                $table->dropColumn('google_id');
            }
        });
    }
};