<?php

return [
    'navigation' => ['title' => 'Manajemen Proyek', 'projects' => 'Proyek Klien', 'companies' => 'Perusahaan Klien'],
    'common' => ['delete_confirm' => 'Yakin ingin menghapus data ini?'],
    'board' => [
        'title' => 'Board Proyek', 'open' => 'Buka Board', 'description' => 'Pindahkan kartu fitur antarkolom untuk memperbarui status seperti Trello.',
        'back_to_detail' => 'Detail Proyek', 'drag_hint' => 'Tarik kartu ke kolom lain atau gunakan pilihan status pada perangkat sentuh.',
        'empty' => 'Belum ada kartu pada kolom ini.', 'moved' => 'Status kartu berhasil diperbarui.',
        'move_failed' => 'Kartu gagal dipindahkan. Silakan coba kembali.', 'cards' => ':count kartu',
    ],
    'companies' => [
        'title' => 'Perusahaan Klien', 'description' => 'Kelola perusahaan dan kontak klien. Satu perusahaan dapat memiliki banyak proyek.',
        'add' => 'Tambah perusahaan', 'search' => 'Cari perusahaan atau kontak...', 'empty' => 'Belum ada perusahaan klien.',
        'create_title' => 'Tambah Perusahaan Klien', 'edit_title' => 'Edit Perusahaan Klien',
        'created' => 'Perusahaan berhasil ditambahkan.', 'updated' => 'Perusahaan berhasil diperbarui.', 'deleted' => 'Perusahaan berhasil dihapus.',
        'has_projects' => 'Perusahaan tidak dapat dihapus karena masih memiliki proyek.', 'delete_confirm' => 'Yakin ingin menghapus perusahaan ini?',
    ],
    'projects' => [
        'title' => 'Proyek Klien', 'description' => 'Pantau scope, fase, fitur, dokumen, teknologi, biaya, dan server setiap proyek.',
        'add' => 'Tambah proyek', 'search' => 'Cari nama atau kode proyek...', 'empty' => 'Belum ada proyek klien.',
        'create_title' => 'Tambah Proyek Klien', 'create_description' => 'Catat informasi kontrak, waktu, dan biaya awal proyek.',
        'edit_title' => 'Edit Proyek Klien', 'edit' => 'Edit proyek', 'created' => 'Proyek berhasil ditambahkan.',
        'updated' => 'Proyek berhasil diperbarui.', 'deleted' => 'Proyek berhasil dihapus.',
    ],
    'phases' => [
        'title' => 'Fase dan Progress', 'short' => 'fase', 'description' => 'Rinci pekerjaan per fase agar developer mengetahui hasil yang sudah dan akan dikerjakan.',
        'add' => 'Tambah fase', 'edit' => 'Edit fase', 'delete' => 'Hapus fase', 'empty' => 'Belum ada fase proyek.',
        'created' => 'Fase berhasil ditambahkan.', 'updated' => 'Fase berhasil diperbarui.', 'deleted' => 'Fase berhasil dihapus.',
    ],
    'features' => [
        'title' => 'Fitur dalam fase', 'add' => 'Tambah fitur', 'edit' => 'Edit fitur', 'empty' => 'Belum ada fitur pada fase ini.',
        'created' => 'Fitur berhasil ditambahkan.', 'updated' => 'Fitur berhasil diperbarui.', 'deleted' => 'Fitur berhasil dihapus.',
    ],
    'documents' => [
        'title' => 'Dokumen Proyek', 'short' => 'dokumen', 'description' => 'Kontrak, silabus, requirement, desain, laporan, dan dokumen pendukung lainnya.',
        'upload' => 'Unggah dokumen', 'empty' => 'Belum ada dokumen.', 'created' => 'Dokumen berhasil diunggah.', 'deleted' => 'Dokumen berhasil dihapus.',
    ],
    'technologies' => [
        'title' => 'Teknologi', 'description' => 'Stack dan versi teknologi yang digunakan proyek.', 'add' => 'Tambah teknologi',
        'empty' => 'Belum ada teknologi.', 'created' => 'Teknologi berhasil ditambahkan.', 'deleted' => 'Teknologi berhasil dihapus.',
    ],
    'servers' => [
        'title' => 'Server dan Infrastruktur', 'description' => 'Kelola akses, biaya, margin, dan masa berlaku server.', 'add' => 'Tambah server',
        'edit' => 'Edit server', 'credentials' => 'Kredensial server', 'credentials_warning' => 'Informasi ini bersifat rahasia. Jangan membagikan atau menyalinnya ke tempat yang tidak aman.',
        'empty' => 'Belum ada server.', 'created' => 'Server berhasil ditambahkan.', 'updated' => 'Server berhasil diperbarui.', 'deleted' => 'Server berhasil dihapus.',
    ],
    'notifications' => ['title' => 'Pengingat Server', 'read_all' => 'Tandai semua dibaca', 'empty' => 'Tidak ada pengingat baru.', 'expires' => 'Kedaluwarsa :date'],
    'fields' => [
        'company_name' => 'Nama perusahaan', 'contact_person' => 'Kontak utama', 'phone' => 'Telepon', 'address' => 'Alamat', 'notes' => 'Catatan',
        'projects' => 'Jumlah proyek', 'company' => 'Perusahaan', 'select_company' => 'Pilih perusahaan', 'all_companies' => 'Semua perusahaan',
        'code' => 'Kode proyek', 'project_name' => 'Nama proyek', 'project' => 'Proyek', 'scope' => 'Scope / Silabus Proyek',
        'scope_hint' => 'Jelaskan tujuan, ruang lingkup, batasan, dan hasil utama proyek.', 'all_status' => 'Semua status', 'currency' => 'Mata uang',
        'started_at' => 'Tanggal mulai', 'due_at' => 'Target selesai', 'completed_at' => 'Tanggal selesai', 'timeline' => 'Jangka waktu', 'until' => 'sampai',
        'original_cost' => 'Biaya asli/modal', 'project_price' => 'Harga proyek', 'sell_price' => 'Harga jual', 'cost' => 'Biaya:', 'profit' => 'Keuntungan',
        'margin_hint' => 'Persentase keuntungan dihitung otomatis dari harga dan biaya.', 'progress' => 'Progress keseluruhan', 'duration' => 'Durasi proyek', 'data' => 'Data',
        'phase_name' => 'Nama fase, contoh: Phase 1 — MVP', 'phase_scope' => 'Scope fase', 'deliverables' => 'Hasil/deliverables fase',
        'feature_name' => 'Nama fitur', 'description' => 'Deskripsi', 'acceptance_criteria' => 'Kriteria selesai / acceptance criteria',
        'server_name' => 'Nama server', 'username' => 'Username', 'password' => 'Password', 'credential_notes' => 'Catatan kredensial',
        'secret_unchanged' => 'Kosongkan jika tidak ingin mengubah data rahasia.', 'billing_cycle' => 'Siklus tagihan', 'purchased_at' => 'Tanggal pembelian',
        'expires_at' => 'Tanggal kedaluwarsa', 'reminder_days' => 'Ingatkan sebelum (hari)',
    ],
    'status' => [
        'projects' => ['planning' => 'Perencanaan', 'in_progress' => 'Berjalan', 'on_hold' => 'Ditunda', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'],
        'phases' => ['pending' => 'Belum dimulai', 'in_progress' => 'Dikerjakan', 'review' => 'Review', 'completed' => 'Selesai', 'blocked' => 'Terhambat'],
        'features' => ['backlog' => 'Backlog', 'in_progress' => 'Dikerjakan', 'review' => 'Review', 'done' => 'Selesai', 'blocked' => 'Terhambat'],
        'servers' => ['active' => 'Aktif', 'expired' => 'Kedaluwarsa', 'cancelled' => 'Dihentikan'],
    ],
    'categories' => ['documents' => ['contract' => 'Kontrak', 'syllabus' => 'Silabus', 'requirement' => 'Requirement', 'design' => 'Desain', 'report' => 'Laporan', 'invoice' => 'Invoice', 'other' => 'Lainnya']],
    'billing_cycles' => ['monthly' => 'Bulanan', 'quarterly' => 'Tiga bulanan', 'yearly' => 'Tahunan', 'one_time' => 'Sekali bayar'],
];
