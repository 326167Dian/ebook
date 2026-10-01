<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('midtrans_payments', function (Blueprint $table) {
            $table->id();
            $table->string('flow', 20);
            $table->string('order_id')->unique();
            $table->string('midtrans_transaction_id')->nullable();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->string('pending_name', 120)->nullable();
            $table->string('pending_email', 150)->nullable();
            $table->unsignedBigInteger('id_reseller')->nullable();
            $table->unsignedInteger('amount');
            $table->text('qr_string')->nullable();
            $table->text('qr_image_url')->nullable();
            $table->string('status', 20)->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->dateTime('expires_at');
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();

            $table->foreign('id_reseller')->references('id_reseller')->on('reseller')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('midtrans_payments');
    }
};
