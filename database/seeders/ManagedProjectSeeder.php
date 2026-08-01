<?php

namespace Database\Seeders;

use App\Models\ClientCompany;
use App\Models\ManagedProject;
use App\Models\ProjectFeature;
use App\Models\ProjectPhase;
use App\Models\ProjectServer;
use App\Models\ProjectTechnology;
use Illuminate\Database\Seeder;

class ManagedProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ClientCompany::query()->each(function (ClientCompany $company): void {
            ManagedProject::factory()
                ->for($company)
                ->count(2)
                ->create()
                ->each(function (ManagedProject $project): void {
                    ProjectPhase::factory()->for($project)->count(2)->create()->each(
                        fn (ProjectPhase $phase) => ProjectFeature::factory()->for($phase)->count(3)->create()
                    );
                    ProjectTechnology::factory()->for($project)->count(3)->create();
                    ProjectServer::factory()->for($project)->create();
                });
        });
    }
}
