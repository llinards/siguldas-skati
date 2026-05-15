<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('header_images', 'header_media');

        Schema::table('header_media', function (Blueprint $table) {
            $table->string('type', 16)->default('image')->after('order')->index();
        });
    }

    public function down(): void
    {
        Schema::table('header_media', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropColumn('type');
        });

        Schema::rename('header_media', 'header_images');
    }
};
