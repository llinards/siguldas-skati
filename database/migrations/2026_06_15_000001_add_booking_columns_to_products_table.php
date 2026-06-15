<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('base_price')->default(0)->after('person_count');
            $table->unsignedInteger('cleaning_fee')->default(0)->after('base_price');
            $table->unsignedInteger('min_nights')->default(1)->after('cleaning_fee');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['base_price', 'cleaning_fee', 'min_nights']);
        });
    }
};
