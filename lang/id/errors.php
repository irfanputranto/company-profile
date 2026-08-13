<?php

return [
    'validation_failed' => 'Validasi gagal. Silakan periksa kembali data yang Anda masukkan.',
    'unexpected_title' => 'Terjadi Kesalahan',
    'unexpected' => 'Terjadi kesalahan pada sistem. Silakan coba kembali atau hubungi administrator.',
    'not_found' => 'Data yang Anda cari tidak ditemukan.',
    'forbidden' => 'Anda tidak memiliki izin untuk melakukan tindakan ini.',
    'unauthenticated' => 'Silakan masuk terlebih dahulu untuk melanjutkan.',
    'too_many_requests' => 'Terlalu banyak permintaan. Silakan coba kembali beberapa saat lagi.',
    'error_center' => 'Pusat pemulihan',
    'status' => 'Status',
    'back_home' => 'Kembali ke beranda',
    'try_again' => 'Coba lagi',
    'reassurance' => 'Tenang, data Anda tetap aman.',
    'pages' => [
        '401' => [
            'title' => 'Aksesmu perlu dikenali',
            'message' => 'Kami belum dapat mengenali sesi ini. Silakan kembali ke beranda atau masuk kembali untuk melanjutkan perjalananmu.',
        ],
        '402' => [
            'title' => 'Langkah ini memerlukan pembayaran',
            'message' => 'Permintaan belum dapat diteruskan karena pembayaran perlu diselesaikan terlebih dahulu.',
        ],
        '403' => [
            'title' => 'Area ini sedang terkunci',
            'message' => 'Kamu tidak memiliki izin untuk membuka halaman ini. Kembali ke beranda untuk memilih jalur lain yang tersedia.',
        ],
        '404' => [
            'title' => 'Halaman tersesat di dimensi lain',
            'message' => 'Tautan yang kamu ikuti mungkin sudah berpindah, berubah nama, atau memang tidak pernah ada.',
        ],
        '419' => [
            'title' => 'Sesi ini sudah tertidur',
            'message' => 'Demi keamanan, sesi yang terlalu lama tidak aktif akan berakhir. Muat ulang halaman dan coba sekali lagi.',
        ],
        '429' => [
            'title' => 'Permintaan datang terlalu cepat',
            'message' => 'Sistem sedang mengatur napas. Tunggu sebentar, lalu coba lagi agar perjalanan tetap lancar.',
        ],
        '500' => [
            'title' => 'Ada gangguan di pusat kendali',
            'message' => 'Sesuatu yang tidak terduga terjadi di sisi kami. Tim sistem dapat memeriksanya sementara kamu mencoba kembali.',
        ],
        '503' => [
            'title' => 'Layanan sedang mengisi energi',
            'message' => 'Kami sedang melakukan perawatan singkat agar semuanya kembali prima. Silakan berkunjung lagi sebentar lagi.',
        ],
        '4xx' => [
            'title' => 'Permintaan belum dapat dilanjutkan',
            'message' => 'Ada bagian dari permintaan ini yang belum dapat kami proses. Kembali ke beranda dan coba jalur lain.',
        ],
        '5xx' => [
            'title' => 'Sistem sedang memulihkan diri',
            'message' => 'Layanan mengalami kendala sementara. Silakan coba kembali dalam beberapa saat.',
        ],
    ],
];
