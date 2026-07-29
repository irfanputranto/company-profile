# Laravel Application Skeleton

Template Laravel 12 untuk memulai aplikasi baru tanpa domain bisnis bawaan.

Fondasi yang tersedia:

- Login berbasis email atau username, logout aman, rate limiting, dan security headers.
- Manajemen pengguna, role, dan permission.
- Profil dan upload avatar privat dengan validasi serta optimasi gambar.
- Activity log dan Laravel Telescope.
- Base CRUD serta fondasi import/export XLSX dan CSV.
- Queue, cache, Redis, MySQL, health check, dan konfigurasi Docker untuk development maupun production.
- Landing page dan admin panel responsif berbasis Tailwind CSS v4.

## Menjalankan secara lokal

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run dev
php artisan serve
```

Password akun seed diambil dari `SEED_DEFAULT_PASSWORD` dan wajib minimal 12 karakter. Akun awal:

- Username: `administrator`
- Email: `admin@example.test`

## Docker development

```bash
cp .env.developer.example .env.developer
docker compose -f compose.developer.yaml up --build
```

Aplikasi tersedia di `http://localhost:8080`.

## Verifikasi

```bash
vendor/bin/pint --format agent
php artisan test --compact
npm run build
```

Tambahkan fitur baru sebagai slice yang lengkap: route, controller/request, model/migrasi, view, permission, seeder/factory bila diperlukan, dan test.
