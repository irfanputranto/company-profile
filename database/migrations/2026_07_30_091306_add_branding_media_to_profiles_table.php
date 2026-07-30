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
        Schema::table('profiles', function (Blueprint $table) {
            $table->foreignId('logo_media_id')
                ->nullable()
                ->constrained('media')
                ->nullOnDelete();
            $table->foreignId('favicon_media_id')
                ->nullable()
                ->constrained('media')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('favicon_media_id');
            $table->dropConstrainedForeignId('logo_media_id');
        });
    }
};
