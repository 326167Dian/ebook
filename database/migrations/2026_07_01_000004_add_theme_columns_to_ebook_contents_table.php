<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ebook_contents', function (Blueprint $table) {
            $table->string('theme_primary', 7)->default('#1e5fae')->after('cover_image');
            $table->string('theme_secondary', 7)->default('#4ea3e6')->after('theme_primary');
            $table->string('theme_accent', 7)->default('#9fd8ff')->after('theme_secondary');
            $table->string('theme_bg_start', 7)->default('#f7fcff')->after('theme_accent');
            $table->string('theme_bg_end', 7)->default('#dcebfa')->after('theme_bg_start');
            $table->string('theme_text', 7)->default('#16314f')->after('theme_bg_end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ebook_contents', function (Blueprint $table) {
            $table->dropColumn([
                'theme_primary',
                'theme_secondary',
                'theme_accent',
                'theme_bg_start',
                'theme_bg_end',
                'theme_text',
            ]);
        });
    }
};
