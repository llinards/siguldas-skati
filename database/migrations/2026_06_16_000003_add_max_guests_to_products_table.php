<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('max_guests')->default(0)->after('children_count');
        });

        // Backfill existing houses: total capacity = current adult + child caps.
        DB::table('products')->update([
            'max_guests' => DB::raw('person_count + children_count'),
        ]);
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('max_guests');
        });
    }
};
