<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ebook_contents', function (Blueprint $table) {
            $table->string('author_photo')->nullable()->after('intro_note');
        });
    }

    public function down(): void
    {
        Schema::table('ebook_contents', function (Blueprint $table) {
            $table->dropColumn('author_photo');
        });
    }
};