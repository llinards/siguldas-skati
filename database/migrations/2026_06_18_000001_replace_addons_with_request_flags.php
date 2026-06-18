<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->boolean('wants_sauna_jacuzzi')->default(false)->after('children');
            $table->boolean('wants_baby_cot')->default(false)->after('wants_sauna_jacuzzi');
        });

        Schema::dropIfExists('booking_addon');
        Schema::dropIfExists('addons');
    }

    public function down(): void
    {
        Schema::create('addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->json('name');
            $table->json('description')->nullable()->after('name');
            $table->unsignedInteger('price');
            $table->string('pricing_type')->default('per_stay');
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('booking_addon', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('addon_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->unsignedInteger('price');
            $table->string('pricing_type');
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['wants_sauna_jacuzzi', 'wants_baby_cot']);
        });
    }
};
