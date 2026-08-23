<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reseller', function (Blueprint $table) {
            $table->id('id_reseller');
            $table->string('username', 60)->unique();
            $table->string('password');
            $table->string('nm_reseller', 120);
            $table->string('telp', 30);
            $table->text('alamat')->nullable();
            $table->string('bank', 60);
            $table->string('rekening', 50);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reseller');
    }
};
