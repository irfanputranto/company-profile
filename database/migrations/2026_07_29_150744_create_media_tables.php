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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('disk', 50);
            $table->string('path', 700);
            $table->string('original_name');
            $table->string('mime_type', 100);
            $table->string('extension', 20);
            $table->unsignedBigInteger('byte_size');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('alt_text')->nullable();
            $table->text('caption')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['disk', 'path']);
        });

        Schema::create('media_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')->constrained()->cascadeOnDelete();
            $table->string('name', 50);
            $table->string('disk', 50);
            $table->string('path', 700);
            $table->string('mime_type', 100)->default('image/webp');
            $table->unsignedBigInteger('byte_size');
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
            $table->timestamps();

            $table->unique(['media_id', 'name']);
            $table->unique(['disk', 'path']);
        });

        Schema::create('mediables', function (Blueprint $table) {
            $table->foreignId('media_id')->constrained()->cascadeOnDelete();
            $table->string('mediable_type');
            $table->unsignedBigInteger('mediable_id');
            $table->string('collection_name', 50)->default('default');
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->unique(
                ['media_id', 'mediable_type', 'mediable_id', 'collection_name'],
                'mediables_attachment_unique'
            );
            $table->index(
                ['mediable_type', 'mediable_id', 'collection_name', 'sort_order'],
                'mediables_owner_collection_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mediables');
        Schema::dropIfExists('media_variants');
        Schema::dropIfExists('media');
    }
};
