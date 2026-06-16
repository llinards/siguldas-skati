<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('cleaning_fee');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['cleaning_fee', 'addons_total']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('cleaning_fee')->default(0)->after('base_price');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedInteger('cleaning_fee')->default(0)->after('nights_total');
            $table->unsignedInteger('addons_total')->default(0)->after('cleaning_fee');
        });
    }
};
