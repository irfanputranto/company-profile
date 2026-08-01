<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_companies', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->index();
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 40)->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $this->auditable($table);
        });

        Schema::create('managed_projects', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('client_company_id')->constrained()->restrictOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->string('status', 30)->default('planning');
            $table->date('started_at')->nullable();
            $table->date('due_at')->nullable();
            $table->date('completed_at')->nullable();
            $table->decimal('contract_value', 18, 2)->default(0);
            $table->decimal('estimated_cost', 18, 2)->default(0);
            $table->char('currency', 3)->default('IDR');
            $this->auditable($table);
            $table->index(['client_company_id', 'status', 'due_at'], 'managed_projects_listing_index');
        });

        Schema::create('project_documents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('managed_project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('category', 40)->default('other');
            $table->string('title');
            $table->string('disk', 50)->default('local');
            $table->string('path', 700)->unique();
            $table->string('original_name');
            $table->string('mime_type', 150);
            $table->unsignedBigInteger('byte_size');
            $table->text('notes')->nullable();
            $this->auditable($table);
            $table->index(['managed_project_id', 'category']);
        });

        Schema::create('project_phases', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('managed_project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->longText('deliverables')->nullable();
            $table->string('status', 30)->default('pending');
            $table->unsignedTinyInteger('progress')->default(0);
            $table->date('started_at')->nullable();
            $table->date('due_at')->nullable();
            $table->date('completed_at')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $this->auditable($table);
            $table->index(['managed_project_id', 'sort_order', 'status']);
        });

        Schema::create('project_features', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_phase_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('acceptance_criteria')->nullable();
            $table->string('status', 30)->default('backlog');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $this->auditable($table);
            $table->index(['project_phase_id', 'sort_order', 'status']);
        });

        Schema::create('project_technologies', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('managed_project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('category', 50)->nullable();
            $table->string('version', 80)->nullable();
            $table->text('notes')->nullable();
            $this->auditable($table);
            $table->unique(['managed_project_id', 'name', 'version'], 'project_technologies_unique');
        });

        Schema::create('project_servers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('managed_project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('provider')->nullable();
            $table->string('environment', 30)->default('production');
            $table->string('host', 2048)->nullable();
            $table->unsignedInteger('port')->nullable();
            $table->longText('username')->nullable();
            $table->longText('password')->nullable();
            $table->longText('api_secret')->nullable();
            $table->longText('credentials_note')->nullable();
            $table->string('billing_cycle', 20)->default('yearly');
            $table->decimal('base_price', 18, 2)->default(0);
            $table->decimal('selling_price', 18, 2)->default(0);
            $table->char('currency', 3)->default('IDR');
            $table->date('purchased_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->unsignedSmallInteger('reminder_days')->default(30);
            $table->timestamp('last_notified_at')->nullable();
            $table->string('status', 30)->default('active');
            $table->text('notes')->nullable();
            $this->auditable($table);
            $table->index(['status', 'expires_at'], 'project_servers_expiry_index');
            $table->index(['managed_project_id', 'environment']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_servers');
        Schema::dropIfExists('project_technologies');
        Schema::dropIfExists('project_features');
        Schema::dropIfExists('project_phases');
        Schema::dropIfExists('project_documents');
        Schema::dropIfExists('managed_projects');
        Schema::dropIfExists('client_companies');
    }

    private function auditable(Blueprint $table): void
    {
        $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
        $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamps();
        $table->softDeletes();
    }
};
