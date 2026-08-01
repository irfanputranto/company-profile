<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\SeoMetadata;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class PopularContentSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::query()
            ->where('email', 'admin@example.test')
            ->firstOrFail();

        $category = ArticleCategory::query()->updateOrCreate(
            ['slug' => 'teknologi-2026'],
            [
                'name' => 'Teknologi 2026',
                'description' => 'Panduan praktis software engineering, keamanan, dan infrastruktur yang relevan pada 2026.',
            ],
        );

        $tags = collect([
            'laravel' => 'Laravel',
            'php' => 'PHP',
            'postgresql' => 'PostgreSQL',
            'security' => 'Security',
            'owasp' => 'OWASP',
            'passkeys' => 'Passkeys',
            'webauthn' => 'WebAuthn',
            'backend' => 'Backend',
        ])->mapWithKeys(function (string $name, string $slug): array {
            $tag = Tag::query()->updateOrCreate(['slug' => $slug], ['name' => $name]);

            return [$slug => $tag];
        });

        foreach ($this->articles() as $content) {
            $article = Article::query()->updateOrCreate(
                ['slug' => $content['slug']],
                [
                    'author_id' => $author->id,
                    'article_category_id' => $category->id,
                    'title' => $content['title'],
                    'excerpt' => $content['excerpt'],
                    'content' => $content['content'],
                    'status' => 'published',
                    'is_featured' => true,
                    'reading_time_minutes' => $content['reading_time_minutes'],
                    'published_at' => $content['published_at'],
                ],
            );

            $article->tags()->sync(
                collect($content['tags'])->map(fn (string $slug): int => $tags->get($slug)->id),
            );

            $canonicalUrl = route('blog.show', ['article' => $article->slug]);

            SeoMetadata::query()->updateOrCreate(
                [
                    'seoable_type' => Article::class,
                    'seoable_id' => $article->id,
                ],
                [
                    'meta_title' => $content['meta_title'],
                    'meta_description' => $content['meta_description'],
                    'canonical_url' => $canonicalUrl,
                    'robots_index' => true,
                    'robots_follow' => true,
                    'open_graph_title' => $content['meta_title'],
                    'open_graph_description' => $content['meta_description'],
                    'twitter_card' => 'summary_large_image',
                    'structured_data' => [
                        '@context' => 'https://schema.org',
                        '@type' => 'TechArticle',
                        'headline' => $content['title'],
                        'description' => $content['meta_description'],
                        'datePublished' => $content['published_at'].'T08:00:00+07:00',
                        'dateModified' => $content['published_at'].'T08:00:00+07:00',
                        'inLanguage' => 'id-ID',
                        'mainEntityOfPage' => $canonicalUrl,
                        'author' => [
                            '@type' => 'Person',
                            'name' => 'Irfan Putranto Pratama',
                        ],
                        'citation' => $content['sources'],
                    ],
                ],
            );
        }
    }

    /**
     * @return list<array{
     *     slug: string,
     *     title: string,
     *     excerpt: string,
     *     content: string,
     *     tags: list<string>,
     *     sources: list<string>,
     *     reading_time_minutes: int,
     *     published_at: string,
     *     meta_title: string,
     *     meta_description: string
     * }>
     */
    private function articles(): array
    {
        return [
            [
                'slug' => 'laravel-13-panduan-upgrade-aman-2026',
                'title' => 'Laravel 13 di 2026: Panduan Upgrade Aman dari Laravel 12',
                'excerpt' => 'Peta upgrade Laravel 12 ke 13, mulai dari PHP 8.3, perubahan keamanan, hingga strategi rollout yang aman untuk aplikasi production.',
                'content' => <<<'CONTENT'
Laravel 13 resmi dirilis pada 17 Maret 2026. Rilis ini menarik karena membawa Laravel AI SDK, JSON:API resources, dukungan semantic/vector search, peningkatan keamanan request forgery, dan routing queue berbasis class. Namun, keputusan upgrade sebaiknya tetap dimulai dari kebutuhan produk, bukan sekadar mengejar versi terbaru.

Apa yang perlu diperiksa lebih dulu?

Laravel 13 membutuhkan minimal PHP 8.3. Artinya, inventarisasi versi PHP di laptop developer, CI, worker queue, scheduler, dan server production harus selesai sebelum dependency framework dinaikkan. Periksa juga package pihak ketiga dengan composer why-not laravel/framework ^13.0 agar hambatan kompatibilitas terlihat sejak awal.

Perubahan yang paling layak diperhatikan

Pertama, perlindungan request forgery kini diformalkan melalui PreventRequestForgery dan melakukan pemeriksaan berbasis origin. Kedua, Laravel 13 menambahkan Queue::route(...) untuk memusatkan pemilihan connection dan queue per class job. Ketiga, atribut PHP diperluas untuk middleware, authorization, serta pengaturan retry dan timeout job. Untuk produk AI, Laravel AI SDK menyediakan abstraksi agent, tool, embedding, audio, image, dan vector store dengan API yang konsisten.

Strategi upgrade bertahap

1. Buat branch upgrade dan kunci baseline dengan seluruh automated test hijau.
2. Naikkan runtime ke PHP 8.3 atau lebih baru tanpa mengubah framework terlebih dahulu.
3. Perbarui constraint Composer dan ikuti upgrade guide resmi satu per satu.
4. Jalankan test, static analysis, queue smoke test, dan pemeriksaan route cache.
5. Uji staging menggunakan salinan konfigurasi production tanpa membawa rahasia production.
6. Deploy dengan canary atau rolling release, lalu pantau error rate, latency, queue lag, dan query lambat.

Jangan mencampur upgrade framework dengan refactor besar. Perubahan yang kecil dan terisolasi membuat rollback jauh lebih mudah. Jika aplikasi Laravel 12 masih stabil, security support-nya tercatat sampai 24 Februari 2027; tim tetap memiliki waktu untuk menyusun upgrade yang terukur.

Kesimpulan

Nilai terbesar Laravel 13 bukan hanya fitur baru, melainkan kesempatan merapikan fondasi runtime, test, dan observability. Upgrade dinyatakan berhasil ketika perilaku bisnis tetap konsisten, bukan hanya ketika composer update selesai.

Sumber tepercaya:
- Laravel 13 Release Notes: https://laravel.com/docs/13.x/releases
- Laravel 13 Upgrade Guide: https://laravel.com/docs/13.x/upgrade
CONTENT,
                'tags' => ['laravel', 'php', 'backend'],
                'sources' => [
                    'https://laravel.com/docs/13.x/releases',
                    'https://laravel.com/docs/13.x/upgrade',
                ],
                'reading_time_minutes' => 6,
                'published_at' => '2026-07-28',
                'meta_title' => 'Upgrade Laravel 13 yang Aman di 2026',
                'meta_description' => 'Panduan upgrade Laravel 12 ke 13: syarat PHP 8.3, perubahan penting, testing, staging, deployment, dan rollback yang aman.',
            ],
            [
                'slug' => 'php-85-fitur-backend-modern-2026',
                'title' => 'PHP 8.5 untuk Backend Modern: Fitur yang Layak Dipakai di 2026',
                'excerpt' => 'Mengenal URI extension, pipe operator, clone with, NoDiscard, serta checklist kompatibilitas sebelum mengadopsi PHP 8.5.',
                'content' => <<<'CONTENT'
PHP 8.5 dirilis pada 20 November 2025 dan menjadi versi yang sangat relevan untuk proyek backend pada 2026. Pembaruannya bukan sekadar sintaks; beberapa fitur membantu tim menulis kode yang lebih eksplisit, aman, dan mudah dirawat.

URI extension yang lebih dapat diprediksi

PHP 8.5 menyediakan URI extension untuk memproses URI RFC 3986 dan URL WHATWG. Untuk aplikasi yang sering memvalidasi callback URL, webhook, atau link eksternal, objek URI mengurangi ketergantungan pada manipulasi string manual. Tetap terapkan allowlist host dan scheme karena parser URL bukan pengganti kebijakan keamanan.

Pipe operator untuk alur transformasi

Operator |> mengalirkan hasil dari kiri ke callable berikutnya. Ia cocok untuk rangkaian transformasi kecil yang murni, misalnya normalisasi teks. Hindari pipeline terlalu panjang atau fungsi yang memiliki side effect karena debugging justru dapat menjadi lebih sulit.

Clone with untuk value object

Sintaks clone(...) membuat pola immutable “with-er” lebih ringkas. Ini berguna untuk DTO atau value object readonly: objek awal tetap utuh, sedangkan versi baru hanya mengganti properti tertentu. Hasilnya lebih aman untuk kode yang berjalan di queue atau dipakai lintas service.

NoDiscard dan peningkatan diagnostik

Atribut #[NoDiscard] dapat memperingatkan ketika nilai kembalian penting diabaikan. PHP 8.5 juga menyertakan backtrace pada fatal error tertentu, fungsi array_first() dan array_last(), serta persistent cURL share handles yang dapat mengurangi inisialisasi koneksi berulang.

Checklist sebelum upgrade

1. Pastikan framework dan seluruh extension mendukung PHP 8.5.
2. Jalankan test dengan error reporting lengkap untuk menemukan deprecation.
3. Audit cast non-kanonis seperti (integer) dan magic method __sleep()/__wakeup().
4. Uji image container, CLI, FPM, scheduler, dan worker queue dengan versi yang sama.
5. Benchmark endpoint penting; fitur baru tidak otomatis membuat semua workload lebih cepat.

Adopsi secara pragmatis

Tidak semua fitur harus langsung dipakai. Mulailah dari API yang mengurangi bug nyata, tetapkan coding standard, lalu hindari mencampur upgrade runtime dengan perubahan domain besar. Dengan begitu, manfaat PHP 8.5 dapat diukur dan rollback tetap sederhana.

Sumber tepercaya:
- PHP 8.5 Release Announcement: https://www.php.net/releases/8.5/en.php
- PHP 8.5 Migration Guide: https://www.php.net/manual/en/migration85.php
CONTENT,
                'tags' => ['php', 'backend'],
                'sources' => [
                    'https://www.php.net/releases/8.5/en.php',
                    'https://www.php.net/manual/en/migration85.php',
                ],
                'reading_time_minutes' => 6,
                'published_at' => '2026-07-21',
                'meta_title' => 'PHP 8.5 untuk Backend Modern di 2026',
                'meta_description' => 'Ringkasan PHP 8.5 untuk backend: URI extension, pipe operator, clone with, NoDiscard, deprecation, dan checklist upgrade production.',
            ],
            [
                'slug' => 'postgresql-18-production-2026',
                'title' => 'PostgreSQL 18 di Production: AIO, UUIDv7, Upgrade, dan Keamanan',
                'excerpt' => 'Panduan menilai PostgreSQL 18 untuk production, termasuk asynchronous I/O, uuidv7(), observability, dan patch keamanan terbaru.',
                'content' => <<<'CONTENT'
PostgreSQL 18 membawa perubahan yang relevan untuk workload modern: asynchronous I/O, skip scan untuk lebih banyak pola index, uuidv7(), virtual generated columns, observability EXPLAIN yang lebih kaya, dan autentikasi OAuth. Pada 2026, fokus tim seharusnya bukan hanya upgrade major version, tetapi memastikan patch release tetap mutakhir.

Asynchronous I/O bukan tombol ajaib

Subsystem AIO memungkinkan PostgreSQL menjalankan beberapa permintaan I/O secara bersamaan pada operasi seperti sequential scan, bitmap heap scan, dan vacuum. Tim PostgreSQL melaporkan peningkatan hingga tiga kali pada skenario tertentu. Angka tersebut bukan jaminan untuk setiap aplikasi; lakukan benchmark dengan ukuran data, storage, dan pola query sendiri.

UUIDv7 lebih ramah index

Fungsi uuidv7() menghasilkan UUID yang terurut berdasarkan waktu. Dibanding UUID acak, pola insert yang lebih berurutan dapat mengurangi fragmentasi B-tree dan memperbaiki locality. Sebelum migrasi primary key, ukur dampaknya pada foreign key, replikasi, ukuran index, dan kontrak API.

Upgrade dan observability

PostgreSQL 18 mempertahankan optimizer statistics saat pg_upgrade, sehingga waktu menuju performa normal setelah upgrade dapat berkurang. EXPLAIN ANALYZE juga menampilkan informasi buffer secara otomatis, sementara mode VERBOSE memberi detail CPU, WAL, dan rata-rata pembacaan. Simpan baseline query plan sebelum upgrade agar regresi mudah dibandingkan.

Keamanan wajib mengikuti patch release

PostgreSQL 18.2 dirilis pada 12 Februari 2026 bersama perbaikan lima kerentanan dan lebih dari 65 bug pada seluruh versi yang didukung. Versi 18 juga mendeprekasi autentikasi password MD5; gunakan SCRAM untuk autentikasi berbasis password. Pastikan backup, restore drill, dan rollback binary telah diuji sebelum maintenance window.

Checklist implementasi

1. Audit extension, driver, pooler, dan replication topology.
2. Ambil backup terverifikasi dan lakukan simulasi restore.
3. Rekam latency, throughput, buffer hit ratio, vacuum, dan query plan utama.
4. Uji pg_upgrade pada data yang representatif.
5. Naikkan ke patch 18.x terbaru yang tersedia saat deployment.
6. Pantau replication lag, WAL, disk, error, dan perubahan query plan.

Upgrade database adalah perubahan operasional, bukan sekadar perubahan nomor versi. Keberhasilannya ditentukan oleh data yang utuh, performa yang terukur, dan prosedur rollback yang benar-benar pernah diuji.

Sumber tepercaya:
- PostgreSQL 18 Release: https://www.postgresql.org/about/news/postgresql-18-released-3142/
- PostgreSQL 18.2 Security and Bug Fix Release: https://www.postgresql.org/about/news/postgresql-182-178-1612-1516-and-1421-released-3235/
CONTENT,
                'tags' => ['postgresql', 'backend', 'security'],
                'sources' => [
                    'https://www.postgresql.org/about/news/postgresql-18-released-3142/',
                    'https://www.postgresql.org/about/news/postgresql-182-178-1612-1516-and-1421-released-3235/',
                ],
                'reading_time_minutes' => 7,
                'published_at' => '2026-07-14',
                'meta_title' => 'PostgreSQL 18 untuk Production di 2026',
                'meta_description' => 'Panduan PostgreSQL 18: AIO, UUIDv7, pg_upgrade, EXPLAIN, patch keamanan 18.2, benchmark, serta checklist rollout production.',
            ],
            [
                'slug' => 'checklist-keamanan-web-owasp-2026',
                'title' => 'Checklist Keamanan Web 2026 Berdasarkan OWASP Top 10:2025',
                'excerpt' => 'Checklist praktis untuk access control, konfigurasi, supply chain, kriptografi, injection, autentikasi, logging, dan error handling.',
                'content' => <<<'CONTENT'
OWASP Top 10:2025 menempatkan Broken Access Control di posisi pertama, diikuti Security Misconfiguration dan Software Supply Chain Failures. Daftar ini bukan checklist kepatuhan yang berdiri sendiri, tetapi peta risiko yang baik untuk menyusun prioritas engineering pada 2026.

1. Access control harus diuji di server

Setiap aksi baca, ubah, unduh, dan hapus wajib memeriksa izin pada backend. Gunakan deny-by-default, policy terpusat, dan test untuk horizontal privilege escalation. Jangan mengandalkan tombol yang disembunyikan di frontend.

2. Konfigurasi aman sejak awal

Matikan debug di production, batasi CORS, aktifkan security headers, hapus akun default, dan pisahkan rahasia dari repository. Infrastruktur, container, web server, database, object storage, serta dashboard monitoring perlu baseline konfigurasi yang dapat diaudit.

3. Lindungi software supply chain

Kunci dependency, tinjau perubahan lock file, aktifkan dependency scanning, hasilkan SBOM bila diperlukan, dan batasi permission pipeline. Artefak deployment harus dibangun dari commit yang diketahui dan diverifikasi sebelum dirilis.

4. Kriptografi dan injection

Gunakan TLS yang benar, algoritma modern, rotasi key, dan penyimpanan secret terkelola. Untuk database, selalu gunakan parameter binding atau ORM. Validasi input berdasarkan konteks dan encode output dengan benar; sanitasi global tidak memahami seluruh konteks HTML, URL, SQL, dan shell.

5. Desain dan autentikasi

Threat modeling perlu dilakukan sebelum coding untuk alur bernilai tinggi seperti pembayaran, reset password, dan perubahan rekening. Terapkan MFA atau passkeys, rate limiting, session rotation, serta recovery flow yang tidak lebih lemah dari login utama.

6. Integritas, logging, dan exception

Verifikasi signature webhook dan artefak, cegah deserialisasi data tak tepercaya, serta lindungi pipeline CI/CD. Log kejadian keamanan dengan correlation ID tanpa merekam password atau token. Alert harus memiliki pemilik dan runbook. Pesan error publik dibuat generik, sedangkan detail teknis masuk ke sistem observability yang aksesnya dibatasi.

Cara menerapkannya dalam sprint

Mulai dari pemetaan endpoint dan aset, pilih risiko dengan dampak serta kemungkinan tertinggi, lalu ubah kontrol menjadi acceptance criteria dan automated test. Jadwalkan review dependency, akses, backup, dan incident drill secara berkala. Keamanan yang efektif adalah proses berulang yang menghasilkan bukti, bukan dokumen yang hanya dibuka menjelang audit.

Sumber tepercaya:
- OWASP Top 10:2025: https://owasp.org/Top10/
- OWASP Top 10:2025 Introduction: https://owasp.org/Top10/2025/0x00_2025-Introduction/
CONTENT,
                'tags' => ['security', 'owasp', 'backend'],
                'sources' => [
                    'https://owasp.org/Top10/',
                    'https://owasp.org/Top10/2025/0x00_2025-Introduction/',
                ],
                'reading_time_minutes' => 7,
                'published_at' => '2026-07-07',
                'meta_title' => 'Checklist Keamanan Web OWASP untuk 2026',
                'meta_description' => 'Checklist keamanan web 2026 berdasarkan OWASP Top 10:2025, dari access control dan supply chain hingga logging serta exception.',
            ],
            [
                'slug' => 'passkeys-webauthn-level-3-2026',
                'title' => 'Passkeys dan WebAuthn Level 3: Strategi Login Tanpa Password di 2026',
                'excerpt' => 'Memahami model keamanan passkeys, status WebAuthn Level 3, rollout bertahap, recovery akun, dan metrik keberhasilan implementasi.',
                'content' => <<<'CONTENT'
Passkeys memindahkan autentikasi dari rahasia yang diketik pengguna ke pasangan public-key credential. Private key tetap berada di authenticator, sedangkan server menyimpan public key. Karena credential terikat pada relying party dan origin, model ini lebih tahan phishing dibanding password biasa.

Pada 26 Mei 2026, Web Authentication Level 3 dipublikasikan sebagai Candidate Recommendation Snapshot oleh W3C. Status ini mengundang implementasi dan pengujian interoperabilitas; tim produk tetap perlu memeriksa dukungan browser, sistem operasi, dan authenticator yang menjadi target pengguna.

Bagaimana alurnya bekerja?

Saat registrasi, server membuat challenge acak lalu browser memanggil navigator.credentials.create() dalam secure context. Authenticator membuat pasangan kunci dan mengembalikan public-key credential. Saat login, server kembali mengirim challenge, browser memanggil navigator.credentials.get(), lalu server memverifikasi signature, challenge, origin, RP ID, dan counter atau sinyal terkait sesuai kebijakan.

Private key tidak pernah diberikan ke JavaScript aplikasi. Namun, implementasi server tetap harus ketat: challenge hanya sekali pakai, memiliki masa berlaku singkat, terikat pada session, dan dibandingkan secara aman. HTTPS wajib karena WebAuthn tersedia dalam secure context.

Strategi rollout yang realistis

1. Mulai sebagai opsi tambahan setelah login yang sudah terverifikasi.
2. Tampilkan nama perangkat/credential dan waktu terakhir digunakan.
3. Izinkan lebih dari satu passkey per akun untuk mengurangi risiko lockout.
4. Sediakan revocation, audit log, dan notifikasi ketika credential baru ditambahkan.
5. Rancang account recovery dengan verifikasi kuat; recovery yang lemah membatalkan manfaat passkey.
6. Pertahankan fallback sementara untuk perangkat yang belum kompatibel, lalu ukur pemakaiannya.

Metrik yang perlu dipantau

Ukur tingkat keberhasilan registrasi, keberhasilan login, waktu login, fallback rate, recovery rate, dan tiket dukungan. Pisahkan data berdasarkan platform tanpa merekam informasi credential yang sensitif. FIDO Alliance melaporkan pada State of Passkeys 2026 bahwa deployment, pilot, atau rollout passkeys di lingkungan workforce telah mendekati arus utama; angka industri tersebut tetap perlu diterjemahkan ke kebutuhan pengguna produk sendiri.

Passkeys bukan sekadar pengganti field password. Ia mengubah onboarding, pengelolaan perangkat, recovery, dukungan pelanggan, dan respons insiden. Rollout bertahap dengan telemetry dan recovery yang kuat jauh lebih penting daripada memaksa migrasi sekaligus.

Sumber tepercaya:
- W3C WebAuthn Level 3: https://www.w3.org/TR/webauthn-3/
- FIDO Alliance — The State of Passkeys 2026: https://fidoalliance.org/wp-content/uploads/2026/05/The-State-of-Passkeys-Global-Consumer-and-Workforce-Report-1.pdf
CONTENT,
                'tags' => ['passkeys', 'webauthn', 'security'],
                'sources' => [
                    'https://www.w3.org/TR/webauthn-3/',
                    'https://fidoalliance.org/wp-content/uploads/2026/05/The-State-of-Passkeys-Global-Consumer-and-Workforce-Report-1.pdf',
                ],
                'reading_time_minutes' => 7,
                'published_at' => '2026-06-30',
                'meta_title' => 'Passkeys dan WebAuthn Level 3 di 2026',
                'meta_description' => 'Panduan passkeys 2026: cara kerja WebAuthn Level 3, rollout aman, multi-credential, recovery akun, telemetry, dan mitigasi risiko.',
            ],
        ];
    }
}
