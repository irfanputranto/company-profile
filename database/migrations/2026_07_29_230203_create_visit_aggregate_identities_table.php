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
        Schema::create('visit_aggregate_identities', function (Blueprint $table) {
            $table->id();
            $table->string('period_type', 10);
            $table->date('period_start');
            $table->string('scope_type', 50);
            $table->unsignedBigInteger('scope_id');
            $table->string('identity_type', 10);
            $table->char('identity_hash', 64);
            $table->timestamp('created_at')->useCurrent();

            $table->unique(
                [
                    'period_type',
                    'period_start',
                    'scope_type',
                    'scope_id',
                    'identity_type',
                    'identity_hash',
                ],
                'visit_identities_period_scope_identity_unique'
            );
            $table->index(
                ['scope_type', 'scope_id', 'period_type', 'period_start'],
                'visit_identities_scope_period_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_aggregate_identities');
    }
};
