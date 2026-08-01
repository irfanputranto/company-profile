<?php

use App\Models\ClientCompany;
use App\Models\ManagedProject;
use App\Models\ProjectFeature;
use App\Models\ProjectPhase;
use App\Models\ProjectServer;
use App\Models\User;
use App\Notifications\ProjectServerExpiryNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('local');
    $this->administrator = User::factory()->create(['uuid' => (string) Str::uuid(), 'is_active' => true]);
    grantMasterPermissions($this->administrator, 'client_companies');
    grantMasterPermissions($this->administrator, 'managed_projects');
    $this->administrator->givePermissionTo(Permission::findOrCreate('show_project_credentials', 'web'));
    $this->actingAs($this->administrator);
});

it('mencatat satu perusahaan dengan banyak proyek beserta rincian delivery', function (): void {
    $company = ClientCompany::factory()->create(['name' => 'Perusahaan A']);
    $firstProject = ManagedProject::factory()->for($company)->create(['name' => 'Sistem ERP', 'code' => 'PRJ-ERP']);
    ManagedProject::factory()->for($company)->create(['name' => 'Mobile Sales', 'code' => 'PRJ-MOBILE']);

    $this->post(route('project-management.projects.phases.store', $firstProject), [
        'name' => 'Phase 1 — MVP',
        'description' => 'Autentikasi dan master data.',
        'deliverables' => 'MVP siap UAT.',
        'status' => 'in_progress',
        'progress' => 60,
        'started_at' => today()->toDateString(),
        'due_at' => today()->addMonth()->toDateString(),
        'completed_at' => null,
        'sort_order' => 1,
    ])->assertSessionHasNoErrors();

    $phase = ProjectPhase::query()->firstOrFail();
    $this->post(route('project-management.projects.phases.features.store', [$firstProject, $phase]), [
        'name' => 'Login multi-role', 'description' => 'Hak akses per role.', 'acceptance_criteria' => 'Semua role lolos UAT.',
        'status' => 'in_progress', 'sort_order' => 1,
    ])->assertSessionHasNoErrors();
    $this->post(route('project-management.projects.technologies.store', $firstProject), [
        'name' => 'Laravel', 'category' => 'Backend', 'version' => '12', 'notes' => null,
    ])->assertSessionHasNoErrors();

    $this->get(route('project-management.projects.show', $firstProject))
        ->assertSuccessful()
        ->assertSee('Perusahaan A')
        ->assertSee('Phase 1 — MVP')
        ->assertSee('Login multi-role')
        ->assertSee('Laravel');

    expect($company->managedProjects()->count())->toBe(2)
        ->and($firstProject->phases()->count())->toBe(1);
});

it('menampilkan fitur proyek dalam board kanban berdasarkan status', function (): void {
    $project = ManagedProject::factory()->create(['name' => 'Sistem ERP']);
    $phase = ProjectPhase::factory()->for($project)->create(['name' => 'Phase 1 — MVP']);
    ProjectFeature::factory()->for($phase)->create([
        'name' => 'Login multi-role',
        'status' => 'in_progress',
    ]);

    $this->get(route('project-management.projects.board', $project))
        ->assertSuccessful()
        ->assertSee('Board Proyek')
        ->assertSee('Phase 1 — MVP')
        ->assertSee('Login multi-role')
        ->assertSee('data-kanban-column', false)
        ->assertSee('data-status="in_progress"', false)
        ->assertSee('data-kanban-card', false);
});

it('memindahkan kartu kanban dan menghitung ulang progres fase', function (): void {
    $project = ManagedProject::factory()->create();
    $phase = ProjectPhase::factory()->for($project)->create([
        'status' => 'in_progress',
        'progress' => 50,
    ]);
    ProjectFeature::factory()->for($phase)->create(['status' => 'done', 'sort_order' => 1]);
    $feature = ProjectFeature::factory()->for($phase)->create(['status' => 'in_progress', 'sort_order' => 2]);

    $this->patchJson(route('project-management.projects.phases.features.move', [$project, $phase, $feature]), [
        'status' => 'done',
        'sort_order' => 2,
    ])->assertSuccessful()
        ->assertJsonPath('feature.status', 'done')
        ->assertJsonPath('phase.status', 'completed')
        ->assertJsonPath('phase.progress', 100);

    expect($feature->refresh()->status)->toBe('done')
        ->and($phase->refresh()->progress)->toBe(100)
        ->and($phase->status)->toBe('completed')
        ->and($phase->completed_at)->not->toBeNull();
});

it('mengunggah banyak dokumen privat dan mencegah akses lintas proyek', function (): void {
    $project = ManagedProject::factory()->create();
    $otherProject = ManagedProject::factory()->create();

    $this->post(route('project-management.projects.documents.store', $project), [
        'category' => 'syllabus',
        'notes' => 'Dokumen awal',
        'documents' => [
            UploadedFile::fake()->create('silabus.pdf', 120, 'application/pdf'),
            UploadedFile::fake()->create('requirement.docx', 80, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
        ],
    ])->assertSessionHasNoErrors();

    expect($project->documents()->count())->toBe(2);
    $document = $project->documents()->firstOrFail();
    Storage::disk('local')->assertExists($document->path);

    $this->get(route('project-management.projects.documents.download', [$project, $document]))
        ->assertSuccessful()
        ->assertHeader('X-Content-Type-Options', 'nosniff');
    $this->get(route('project-management.projects.documents.download', [$otherProject, $document]))
        ->assertNotFound();
});

it('mengenkripsi kredensial server dan tidak memasukkannya ke activity log', function (): void {
    $project = ManagedProject::factory()->create();
    $server = ProjectServer::factory()->for($project)->create([
        'username' => 'root-secret',
        'password' => 'very-secret-password',
        'api_secret' => 'token-secret',
    ]);

    $raw = DB::table('project_servers')->where('id', $server->id)->first();
    expect($raw->username)->not->toBe('root-secret')
        ->and($raw->password)->not->toBe('very-secret-password')
        ->and($server->toArray())->not->toHaveKeys(['username', 'password', 'api_secret']);

    $this->get(route('project-management.projects.servers.credentials', [$project, $server]))
        ->assertSuccessful()
        ->assertSee('root-secret')
        ->assertSee('very-secret-password');

    expect(Activity::query()->get()->pluck('properties')->flatten()->implode(' '))
        ->not->toContain('very-secret-password')
        ->not->toContain('token-secret');
});

it('mengirim satu pengingat saat server mendekati kedaluwarsa', function (): void {
    Notification::fake();
    $project = ManagedProject::factory()->create();
    $server = ProjectServer::factory()->for($project)->create([
        'expires_at' => today()->addDays(7),
        'reminder_days' => 14,
        'last_notified_at' => null,
    ]);

    $this->artisan('projects:send-server-expiry-reminders')->assertSuccessful();
    Notification::assertSentTo($this->administrator, ProjectServerExpiryNotification::class);
    expect($server->refresh()->last_notified_at)->not->toBeNull();

    Notification::fake();
    $this->artisan('projects:send-server-expiry-reminders')->assertSuccessful();
    Notification::assertNothingSent();
});

it('menolak pengguna tanpa permission manajemen proyek', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('project-management.projects.index'))->assertForbidden();
    $this->actingAs($user)->get(route('project-management.companies.index'))->assertForbidden();
});
