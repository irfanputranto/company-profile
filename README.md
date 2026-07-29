# Laravel Skeleton FlyonUI

Starter project Laravel 12 untuk membangun aplikasi baru tanpa domain bisnis
bawaan. Repository ini menyediakan fondasi autentikasi, otorisasi, keamanan,
observability, upload file, import/export, Docker, landing page, dan admin panel
berbasis FlyonUI.

Fitur bisnis seperti POS, produk, pembelian, penjualan, supplier, dan shift tidak
disertakan. Tambahkan hanya modul yang dibutuhkan oleh project berikutnya.

## Fitur bawaan

- Login menggunakan email atau username, logout, dan pembatasan percobaan login.
- Manajemen user, role, dan permission.
- Profil akun serta upload avatar ke private storage.
- Activity log untuk melacak aktivitas pengguna.
- Import/export XLSX dan CSV.
- Landing page, dashboard, dan halaman panduan.
- Theme switcher FlyonUI dengan default theme dari environment.
- Security headers, Content Security Policy, validasi upload, dan secure media.
- Queue, scheduler, cache, Redis, MySQL, health check, Telescope, dan Pail.
- Docker image terpisah untuk development dan production.
- Test suite berbasis Pest.

## Teknologi

- PHP 8.2+
- Laravel 12
- MySQL 8.4
- Redis 7.4
- Tailwind CSS 4
- FlyonUI 2
- Alpine.js
- Vite
- Pest 3

## Instalasi lokal

Pastikan PHP, Composer, Node.js, npm, MySQL, dan Redis sudah tersedia.

```bash
git clone https://github.com/irfanputranto/laravel-skeleton-flyonui.git
cd laravel-skeleton-flyonui

composer install
cp .env.example .env
php artisan key:generate
```

Sesuaikan koneksi database dan Redis pada `.env`, kemudian jalankan:

```bash
php artisan migrate --seed
npm install
```

Jalankan seluruh service development:

```bash
composer run dev
```

Aplikasi tersedia di `http://localhost:8000`.

## Akun awal

Seeder membuat akun administrator berikut:

- Username: `administrator`
- Email: `admin@example.test`
- Password: nilai `SEED_DEFAULT_PASSWORD` pada `.env`

`SEED_DEFAULT_PASSWORD` wajib diisi minimal 12 karakter sebelum menjalankan
seeder. Ganti password akun administrator setelah instalasi.

## Docker development

```bash
cp .env.developer.example .env.developer
docker compose -f compose.developer.yaml up --build
```

Aplikasi tersedia di `http://localhost:8080`, sedangkan Vite menggunakan port
`5173`.

Service development yang tersedia:

- `app`
- `node`
- `queue`
- `scheduler`
- `mysql`
- `redis`

Untuk menghentikan container:

```bash
docker compose -f compose.developer.yaml down
```

Tambahkan opsi `-v` hanya jika volume database, Redis, dependency, dan data
development memang ingin dihapus.

## Docker production

Salin dan lengkapi konfigurasi production terlebih dahulu:

```bash
cp .env.production.example .env.production
php artisan key:generate --show
```

Masukkan hasil key ke `APP_KEY`, lalu isi password database, root MySQL, URL
aplikasi, dan konfigurasi lain pada `.env.production`.

```bash
docker compose --env-file .env.production -f compose.production.yaml up --build -d
```

Migration dijalankan oleh service `migrate`. Seeder production bersifat manual:

```bash
docker compose --env-file .env.production -f compose.production.yaml --profile tools run --rm seed
```

Jangan commit `.env`, `.env.developer`, atau `.env.production`. Repository hanya
menyimpan file `.example`.

## Konfigurasi theme

Default theme dibaca dari environment:

```dotenv
APP_THEME=valorant
```

Pilihan yang tersedia:

`light`, `dark`, `black`, `claude`, `corporate`, `ghibli`, `gourmet`, `luxury`,
`mintlify`, `pastel`, `perplexity`, `shadcn`, `slack`, `soft`, `spotify`,
`valorant`, dan `vscode`.

Konfigurasi theme berada di `config/theme.php`. Setelah mengubah `APP_THEME` pada
environment yang menggunakan config cache, jalankan:

```bash
php artisan optimize:clear
php artisan config:cache
```

`APP_THEME` menjadi default aplikasi. Theme yang dipilih pengguna melalui theme
switcher disimpan di browser dan akan digunakan kembali selama masih tersedia
dalam daftar theme. Untuk mengembalikan browser ke default environment, hapus
item local storage `laravel-skeleton-theme`.

## Struktur modul

Feature aplikasi ditempatkan di `app/Modules`:

```text
app/Modules/
├── Auth/
├── Master/
│   ├── Permission/
│   ├── Role/
│   └── User/
└── System/
```

Saat menambahkan feature baru, sertakan bagian yang relevan:

1. Route dan permission.
2. Controller serta Form Request.
3. Model, migration, factory, dan seeder.
4. View atau komponen UI.
5. Activity log.
6. Import/export bila dibutuhkan.
7. Feature test dan unit test.

Ikuti pola modul yang sudah tersedia agar feature tetap mudah dipindahkan,
diuji, dan dikembangkan.

## Perintah penting

```bash
# Menjalankan aplikasi, queue, log viewer, dan Vite
composer run dev

# Menjalankan test
php artisan test --compact

# Memformat PHP
vendor/bin/pint --format agent

# Build asset production
npm run build

# Menjalankan queue worker
php artisan queue:work

# Menjalankan scheduler lokal
php artisan schedule:work
```

## Verifikasi

Sebelum commit atau deployment:

```bash
vendor/bin/pint --format agent
php artisan test --compact
npm run build
```
