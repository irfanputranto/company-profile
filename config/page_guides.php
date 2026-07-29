<?php

return [
    'local_routes' => [],

    'fallback' => [
        'title' => 'Panduan Halaman',
        'description' => 'Panduan singkat untuk menggunakan halaman dengan aman.',
        'icon' => 'icon-[tabler--help-circle]',
        'steps' => [
            ['title' => 'Kenali tujuan halaman', 'description' => 'Baca judul dan keterangan untuk memahami data yang sedang dikelola.'],
            ['title' => 'Gunakan pencarian dan filter', 'description' => 'Persempit data sebelum memilih tindakan.'],
            ['title' => 'Periksa sebelum menyimpan', 'description' => 'Pastikan kolom wajib terisi dan data sudah benar.'],
        ],
        'tips' => ['Arahkan kursor ke ikon untuk membaca nama tindakan.', 'Pesan merah menunjukkan data yang perlu diperbaiki.'],
        'warning' => 'Periksa kembali sebelum mengubah atau menghapus data.',
    ],

    'pages' => [
        [
            'routes' => ['dashboard'],
            'title' => 'Panduan Dashboard',
            'description' => 'Dashboard menampilkan ringkasan fondasi aplikasi.',
            'icon' => 'icon-[tabler--layout-dashboard]',
            'steps' => [
                ['title' => 'Periksa akun aktif', 'description' => 'Pastikan nama pengguna yang tampil adalah akun Anda.'],
                ['title' => 'Lihat ringkasan akses', 'description' => 'Kartu menampilkan jumlah pengguna aktif, role, dan permission.'],
                ['title' => 'Pilih menu', 'description' => 'Gunakan sidebar untuk mengelola akses atau membuka activity log.'],
            ],
        ],
        [
            'routes' => ['profile'],
            'title' => 'Panduan Profil',
            'description' => 'Periksa identitas akun dan perbarui foto profil.',
            'icon' => 'icon-[tabler--user-circle]',
            'steps' => [
                ['title' => 'Periksa identitas', 'description' => 'Pastikan nama, email, username, dan role sudah sesuai.'],
                ['title' => 'Pilih foto', 'description' => 'Gunakan JPG, PNG, atau WebP sesuai batas ukuran.'],
                ['title' => 'Simpan perubahan', 'description' => 'Pastikan avatar berubah setelah unggahan selesai.'],
            ],
        ],
        [
            'routes' => ['master.users.*'],
            'title' => 'Panduan Pengguna',
            'description' => 'Kelola akun, status, role, dan foto pengguna.',
            'icon' => 'icon-[tabler--users]',
            'steps' => [
                ['title' => 'Cari akun', 'description' => 'Gunakan pencarian dan filter sebelum menambah akun baru.'],
                ['title' => 'Tetapkan role', 'description' => 'Pilih role sesuai kebutuhan akses pengguna.'],
                ['title' => 'Jaga keamanan', 'description' => 'Gunakan kata sandi kuat dan nonaktifkan akun yang tidak digunakan.'],
            ],
        ],
        [
            'routes' => ['master.roles.*', 'master.permissions.*'],
            'title' => 'Panduan Hak Akses',
            'description' => 'Kelola role dan permission yang membatasi tindakan pengguna.',
            'icon' => 'icon-[tabler--user-shield]',
            'steps' => [
                ['title' => 'Gunakan prinsip akses minimum', 'description' => 'Berikan hanya permission yang diperlukan.'],
                ['title' => 'Kelompokkan lewat role', 'description' => 'Gunakan role agar pengelolaan akses tetap konsisten.'],
                ['title' => 'Periksa sebelum mengubah', 'description' => 'Perubahan role dapat langsung memengaruhi banyak pengguna.'],
            ],
        ],
        [
            'routes' => ['system.activity-logs.*'],
            'title' => 'Panduan Activity Log',
            'description' => 'Telusuri perubahan penting yang terjadi pada aplikasi.',
            'icon' => 'icon-[tabler--activity]',
            'steps' => [
                ['title' => 'Tentukan filter', 'description' => 'Batasi waktu, pelaku, jenis kejadian, atau subjek.'],
                ['title' => 'Buka detail', 'description' => 'Bandingkan nilai sebelum dan sesudah perubahan.'],
                ['title' => 'Gunakan untuk audit', 'description' => 'Catatan membantu menelusuri perubahan, bukan menggantikan backup.'],
            ],
        ],
    ],
];
