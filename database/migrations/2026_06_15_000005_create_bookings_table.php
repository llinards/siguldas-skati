<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->string('reference')->unique();
            $table->date('check_in');
            $table->date('check_out');
            $table->unsignedInteger('adults')->default(1);
            $table->unsignedInteger('children')->default(0);
            $table->string('guest_name');
            $table->string('guest_email');
            $table->string('guest_phone');
            $table->unsignedInteger('nights_total')->default(0);
            $table->unsignedInteger('cleaning_fee')->default(0);
            $table->unsignedInteger('addons_total')->default(0);
            $table->unsignedInteger('grand_total')->default(0);
            $table->string('currency', 3)->default('eur');
            $table->string('status')->default('pending');
            $table->timestamp('expires_at')->nullable();
            $table->uuid('management_token')->unique();
            $table->string('stripe_session_id')->nullable();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->unsignedInteger('refund_amount')->nullable();
            $table->string('stripe_refund_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'status', 'check_in', 'check_out']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
