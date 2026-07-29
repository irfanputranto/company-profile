<?php

namespace Database\Seeders;

use App\Models\Profile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeatureSeeder extends Seeder
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

        $features = [
            ['konsultasi-kebutuhan', 'Konsultasi Kebutuhan', 'Kebutuhan bisnis dipetakan lebih dulu agar solusi yang dibangun tepat sasaran.', 'messages', 1],
            ['solusi-custom', 'Solusi Sesuai Bisnis', 'Aplikasi dirancang mengikuti alur kerja bisnis, bukan memaksa bisnis mengikuti template.', 'adjustments', 2],
            ['clean-code', 'Clean Code', 'Kode terstruktur, teruji, dan mudah dikembangkan saat kebutuhan bisnis bertambah.', 'code', 3],
            ['aman-scalable', 'Aman dan Scalable', 'Arsitektur dipersiapkan untuk keamanan, performa, dan pertumbuhan trafik.', 'shield-check', 4],
            ['proses-transparan', 'Proses Transparan', 'Progress, ruang lingkup, dan prioritas dikomunikasikan secara terbuka.', 'timeline', 5],
            ['support-berkelanjutan', 'Support Berkelanjutan', 'Pendampingan setelah peluncuran untuk perbaikan, optimasi, dan pengembangan lanjutan.', 'headset', 6],
        ];
        $now = now();

        foreach ($features as [$slug, $title, $description, $icon, $sortOrder]) {
            DB::table('features')->updateOrInsert(
                ['slug' => $slug],
                [
                    'profile_id' => $profileId,
                    'title' => $title,
                    'description' => $description,
                    'icon' => $icon,
                    'sort_order' => $sortOrder,
                    'is_featured' => true,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }
    }
}
