<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\PricingPlan;
use App\Models\Profile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PricingPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $profileId = Profile::query()
            ->where('slug', 'irfan-putranto-pratama')
            ->value('id');

        if (! $profileId) {
            return;
        }

        $plans = [
            [
                'slug' => 'starter',
                'title' => 'Starter',
                'tagline' => 'Untuk kebutuhan digital sederhana',
                'description' => 'Cocok untuk company profile, landing page, atau MVP dengan ruang lingkup terarah.',
                'price' => 3_500_000,
                'features' => ['konsultasi-kebutuhan', 'solusi-custom', 'clean-code'],
                'sort_order' => 1,
                'is_featured' => false,
            ],
            [
                'slug' => 'professional',
                'title' => 'Professional',
                'tagline' => 'Untuk aplikasi bisnis yang siap berkembang',
                'description' => 'Cocok untuk dashboard, sistem internal, integrasi API, e-commerce, dan POS.',
                'price' => 7_500_000,
                'features' => ['konsultasi-kebutuhan', 'solusi-custom', 'clean-code', 'aman-scalable', 'proses-transparan'],
                'sort_order' => 2,
                'is_featured' => true,
            ],
            [
                'slug' => 'business',
                'title' => 'Business',
                'tagline' => 'Untuk sistem kompleks dan kebutuhan khusus',
                'description' => 'Cocok untuk solusi multi-modul, high traffic, integrasi kompleks, dan pendampingan lanjutan.',
                'price' => 15_000_000,
                'features' => ['konsultasi-kebutuhan', 'solusi-custom', 'clean-code', 'aman-scalable', 'proses-transparan', 'support-berkelanjutan'],
                'sort_order' => 3,
                'is_featured' => false,
            ],
        ];
        $now = now();

        foreach ($plans as $planData) {
            $featureSlugs = $planData['features'];
            unset($planData['features']);

            DB::table('pricing_plans')->updateOrInsert(
                ['slug' => $planData['slug']],
                [
                    'profile_id' => $profileId,
                    ...$planData,
                    'currency' => 'IDR',
                    'billing_period' => 'project',
                    'call_to_action_label' => 'Konsultasikan paket',
                    'call_to_action_url' => null,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );

            $pricingPlan = PricingPlan::query()->where('slug', $planData['slug'])->firstOrFail();
            $featureIds = Feature::query()->whereIn('slug', $featureSlugs)->pluck('id');
            $pricingPlan->features()->sync($featureIds);
        }
    }
}
