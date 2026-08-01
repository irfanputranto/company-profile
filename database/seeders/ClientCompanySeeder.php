<?php

namespace Database\Seeders;

use App\Models\ClientCompany;
use Illuminate\Database\Seeder;

class ClientCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ClientCompany::factory()->count(3)->create();
    }
}
