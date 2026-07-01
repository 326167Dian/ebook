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
        Schema::create('ebook_contents', function (Blueprint $table) {
            $table->id();
            $table->string('badge')->default('Panduan Strategis');
            $table->string('hero_title');
            $table->text('hero_description');
            $table->text('intro_note')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('chapters');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ebook_contents');
    }
};
