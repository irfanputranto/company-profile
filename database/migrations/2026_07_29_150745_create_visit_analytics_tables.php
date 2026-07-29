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
        Schema::create('page_visits', function (Blueprint $table) {
            $table->id();
            $table->string('scope_type', 50)->default('site');
            $table->unsignedBigInteger('scope_id')->default(0);
            $table->string('route_name')->nullable();
            $table->string('path', 2048);
            $table->char('visitor_hash', 64)->nullable();
            $table->char('session_hash', 64)->nullable();
            $table->string('referrer_host')->nullable();
            $table->string('device_type', 30)->nullable();
            $table->char('country_code', 2)->nullable();
            $table->boolean('is_bot')->default(false);
            $table->timestamp('occurred_at');

            $table->index('occurred_at');
            $table->index(
                ['scope_type', 'scope_id', 'occurred_at'],
                'page_visits_scope_time_index'
            );
            $table->index(['visitor_hash', 'occurred_at'], 'page_visits_visitor_time_index');
        });

        Schema::create('visit_aggregates', function (Blueprint $table) {
            $table->id();
            $table->string('period_type', 10);
            $table->date('period_start');
            $table->string('scope_type', 50)->default('site');
            $table->unsignedBigInteger('scope_id')->default(0);
            $table->unsignedBigInteger('page_views')->default(0);
            $table->unsignedBigInteger('unique_visitors')->default(0);
            $table->unsignedBigInteger('sessions')->default(0);
            $table->timestamps();

            $table->unique(
                ['period_type', 'period_start', 'scope_type', 'scope_id'],
                'visit_aggregates_period_scope_unique'
            );
            $table->index(
                ['scope_type', 'scope_id', 'period_type', 'period_start'],
                'visit_aggregates_dashboard_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_aggregates');
        Schema::dropIfExists('page_visits');
    }
};
