@extends('layouts.app')

@section('title', 'Panduan teknis · Skyreach')
@section('crumb', 'Beranda / Panduan teknis')

@section('content')
    <div class="page-head">
        <div>
            <h1>Panduan teknis</h1>
            <p class="lede">Monolith Laravel 8. Blade, CSS, dan JavaScript biasa. Tidak ada SPA dan tidak ada API klien terpisah. Pilih database di bawah.</p>
        </div>
    </div>

    <div class="setup-choice" role="group" aria-label="Pilihan database">
        <button type="button" class="is-active" data-setup="sqlite" aria-pressed="true">
            <span class="setup-kicker">Fast setup</span>
            <strong>SQLite</strong>
            <p>Satu berkas. phpMyAdmin tidak dipakai.</p>
        </button>
        <button type="button" data-setup="mysql" aria-pressed="false">
            <span class="setup-kicker">Deploy</span>
            <strong>MySQL</strong>
            <p>Database dibuat di phpMyAdmin. Blok di <code>.env.example</code> tinggal dibuka.</p>
        </button>
    </div>

    @include('partials.guide-switch')

    <div class="guide-layout">
        <article class="guide">
            <h2 id="runtime">Runtime</h2>
            <table>
                <thead><tr><th>Komponen</th><th>Versi / lokasi</th></tr></thead>
                <tbody>
                    <tr><td>PHP</td><td>7.4.33 dari XAMPP, <code>/Applications/XAMPP/xamppfiles/bin/php-7.4.33</code></td></tr>
                    <tr><td>Alias CLI</td><td><code>~/.local/bin/php</code> mengarah ke binary itu. <code>$HOME/.local/bin</code> didahulukan di PATH.</td></tr>
                    <tr><td>Composer</td><td>2.10.2, shebang <code>#!/usr/bin/env php</code>, jadi ikut PHP 7.4.33 di shell baru.</td></tr>
                    <tr><td>Framework</td><td><code>laravel/framework</code> ^8.75. Constraint PHP <code>^7.3|^8.0</code>.</td></tr>
                </tbody>
            </table>
            <div class="callout">
                <p>Jangan naik ke PHP 8.1, 8.2, atau 8.5. Laravel 8 tidak kompatibel di sana. PHP 8.5.9 Homebrew di <code>/opt/homebrew/bin/php</code> sengaja tetap ada. Jangan menaruh <code>/Applications/XAMPP/bin</code> di PATH: folder itu menimpa curl, openssl, perl, dan mysql.</p>
            </div>
            <p>Sebelum <code>composer</code> atau <code>php artisan</code>, shell baru harus menulis <code>PHP 7.4.33</code> pada <code>php -v</code>. Shell lama bisa masih memegang 8.5.9.</p>
            <p>Ekstensi: <code>pdo_sqlite</code>, <code>sqlite3</code>, <code>fileinfo</code>, <code>gd</code>, <code>mbstring</code>. GD hanya untuk sampul PNG seeder. Zona waktu <code>Asia/Jakarta</code>, locale <code>id</code>, di-set di <code>AppServiceProvider</code>.</p>

            <h2 id="boot">Menyalakan</h2>
            <p>Perintah dijalankan di folder proyek Skyreach. Pilihan di atas mengganti langkah database.</p>

            <div data-setup-panel="sqlite">
<pre><code>php -v
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve --host=127.0.0.1 --port=8000</code></pre>
                <p>Blok SQLite di <code>.env.example</code> sudah terbuka. <code>DB_DATABASE</code> dikosongkan supaya config jatuh ke <code>database_path('database.sqlite')</code>. Nilai seperti <code>laravel</code> membuat SQLite membuka berkas dengan nama itu. Service MySQL boleh tetap Stopped.</p>
            </div>

            <div data-setup-panel="mysql" hidden>
<pre><code>php -v
composer install
cp .env.example .env
php artisan key:generate</code></pre>
                <p>Di <code>.env</code>, komentari blok SQLite, lalu hapus tanda <code>#</code> pada blok MySQL. Jangan biarkan keduanya terbuka: nilai yang pertama yang dipakai.</p>
<pre><code>DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=skyreach
DB_USERNAME=root
DB_PASSWORD=</code></pre>
                <p>Di phpMyAdmin buat database <code>skyreach</code>, collation <code>utf8mb4_unicode_ci</code>. Sesuaikan nama, username, dan password dengan yang ada di sana. Ekstensi <code>pdo_mysql</code> harus aktif. Service MySQL harus Running.</p>
<pre><code>php artisan migrate --seed
php artisan serve --host=127.0.0.1 --port=8000</code></pre>
            </div>

            <p><code>APP_KEY</code> di mesin ini sudah terisi. Jangan generate ulang kalau session lama masih perlu dibaca. URL aplikasi <code>http://127.0.0.1:8000</code>.</p>
            <p>Reset total, termasuk data yang dibuat lewat UI:</p>
<pre><code>php artisan migrate:fresh --seed</code></pre>
            <p>Isi database yang sedang dipakai terhapus lalu diisi seeder. Berkas di <code>storage/app/contracts/</code> tidak ikut terhapus. Hapus direktori itu manual kalau storage juga harus bersih.</p>

            <h2 id="layout">Layout kode</h2>
<pre><code>app/Http/Controllers/     Auth, Contract, Dashboard, Document, Activity, Guide
app/Http/Middleware/      EnsureRole, ShareAppData
app/Http/Requests/        Store, Update, Reject
app/Models/               User, Contract, ContractDocument, Activity
app/Services/ContractNumber.php
database/migrations/
database/seeders/DatabaseSeeder.php
resources/views/
public/css/app.css
public/js/app.js
routes/web.php
tests/Feature/</code></pre>
            <p><code>routes/api.php</code> bawaan Laravel tidak dipakai.</p>

            <h2 id="auth">Autentikasi dan otorisasi</h2>
            <p>Guard <code>web</code>, session file di <code>storage/framework/sessions</code>. Tidak ada Breeze atau Jetstream. Login di <code>AuthController</code>. <code>RouteServiceProvider::HOME</code> = <code>/dashboard</code>.</p>
            <table>
                <thead><tr><th>Method</th><th>Path</th><th>Middleware</th><th>Perilaku</th></tr></thead>
                <tbody>
                    <tr><td>GET</td><td class="mono">/login</td><td><code>guest</code></td><td>Form login. Route <code>login</code>.</td></tr>
                    <tr><td>POST</td><td class="mono">/login</td><td><code>guest</code>, <code>throttle:10,1</code></td><td><code>Auth::attempt</code>. Gagal: error di field email.</td></tr>
                    <tr><td>POST</td><td class="mono">/logout</td><td><code>auth</code></td><td>Invalidate session, regenerate CSRF.</td></tr>
                    <tr><td>GET</td><td class="mono">/</td><td>—</td><td>Redirect ke <code>/dashboard</code> atau <code>/login</code>.</td></tr>
                </tbody>
            </table>
            <p>Role string di <code>users.role</code>: <code>staff</code> atau <code>supervisor</code>. Middleware <code>role</code> (<code>EnsureRole</code>) membandingkan string itu dan <code>abort(403)</code> kalau tidak cocok.</p>
            <table>
                <thead><tr><th>Capability</th><th>Staff</th><th>Supervisor</th></tr></thead>
                <tbody>
                    <tr><td><code>GET /contracts</code></td><td>ya</td><td>ya</td></tr>
                    <tr><td>Buat kontrak</td><td>ya</td><td>403</td></tr>
                    <tr><td>Edit</td><td>ya, hanya <code>pending</code>. Selain itu redirect ke detail</td><td>403</td></tr>
                    <tr><td>Approve, reject, delete</td><td>403</td><td>ya</td></tr>
                    <tr><td>Download dan preview</td><td>ya</td><td>ya</td></tr>
                </tbody>
            </table>
            <p>Form Request mengulang cek role. Update tidak menerima <code>number</code>, <code>created_by</code>, <code>status</code>, atau <code>approved_by</code> dari klien. Nomor diterbitkan di server. Staff boleh mengubah kontrak <code>pending</code> milik user lain.</p>
            <p>Login dibatasi 10 percobaan per menit per IP. Password demo di-hash bcrypt. Kartu login hanya menampilkan dua akun. <code>APP_DEBUG=true</code> untuk lokal. Matikan sebelum mesin ini dibuka ke orang lain.</p>
            <table>
                <thead><tr><th>Email</th><th>Password</th><th>role</th><th>name</th></tr></thead>
                <tbody>
                    <tr><td class="mono">supervisor@mail.com</td><td class="mono">supervisor</td><td>supervisor</td><td>Sinta Wijaya</td></tr>
                    <tr><td class="mono">staff@mail.com</td><td class="mono">staff123</td><td>staff</td><td>Raka Putra</td></tr>
                    <tr><td class="mono">dimas@mail.com</td><td class="mono">staff123</td><td>staff</td><td>Dimas Aji</td></tr>
                </tbody>
            </table>

            <h2 id="skema">Skema</h2>
            <p>Migration Laravel, bukan SQL manual. <code>users</code> adalah tabel bawaan ditambah <code>role</code>.</p>
            <table>
                <thead><tr><th>Kolom <code>contracts</code></th><th>Tipe</th><th>Catatan</th></tr></thead>
                <tbody>
                    <tr><td><code>number</code></td><td>string unique</td><td><code>INV/{MM}/{nn}</code></td></tr>
                    <tr><td><code>subject</code></td><td>string</td><td>Perihal, maks 180</td></tr>
                    <tr><td><code>activation_date</code></td><td>date</td><td></td></tr>
                    <tr><td><code>created_by</code></td><td>FK users</td><td></td></tr>
                    <tr><td><code>approved_by</code></td><td>FK users, nullable</td><td>Pemutus, termasuk penolakan</td></tr>
                    <tr><td><code>approved_at</code></td><td>timestamp, nullable</td><td>Waktu setuju atau tolak</td></tr>
                    <tr><td><code>status</code></td><td>string</td><td><code>pending</code>, <code>approved</code>, <code>rejected</code></td></tr>
                    <tr><td><code>rejection_reason</code></td><td>text, nullable</td><td>Wajib saat transisi ke rejected</td></tr>
                </tbody>
            </table>
            <p><code>contract_documents</code> cascade on delete: <code>original_name</code>, <code>path</code>, <code>mime</code>, <code>size</code>.</p>
            <p><code>activities</code>: <code>user_id</code> dan <code>contract_id</code> nullable, <code>nullOnDelete</code>. Deskripsi sudah memuat nomor, jadi jejak tetap ada setelah kontrak dihapus.</p>
            <p><code>contract_sequences</code>: PK <code>period</code> (<code>Y-m</code>), <code>last_number</code>. Cast integer pada <code>created_by</code> dan <code>approved_by</code> perlu karena PDO SQLite mengembalikan PK sebagai string.</p>

            <h2 id="nomor">Nomor kontrak</h2>
            <p><code>App\Services\ContractNumber</code>. Tampilan tidak memuat tahun:</p>
<pre><code>sprintf('INV/%s/%02d', now()->format('m'), $sequence)</code></pre>
            <p>Counter disimpan per <code>Y-m</code>. Unique <code>contracts.number</code> tetap bisa bentrok antar tahun karena string tidak memuat tahun. <code>issue()</code> menaikkan urutan sampai nomor bebas. Batas 99 per bulan, selebihnya <code>RuntimeException</code>.</p>
            <p><code>peek()</code> hanya untuk form. Yang disimpan selalu <code>issue()</code> di dalam <code>withLock()</code>: <code>flock()</code> pada <code>storage/framework/cache/contract-number.lock</code>, lalu <code>DB::transaction</code>. <code>FOR UPDATE</code> tidak dipakai karena grammar SQLite Laravel mengabaikannya.</p>

            <h2 id="berkas">Berkas</h2>
            <p>Validasi <code>documents.*</code>: <code>file|mimes:pdf,jpg,jpeg,png|max:10240</code>. Array boleh kosong.</p>
            <p>Disk <code>local</code> → <code>storage/app/contracts/{id}/{hash}</code>. Nama asli di <code>original_name</code>. Download lewat <code>Storage::download</code>. Preview lewat <code>response()->file</code> dengan <code>Content-Disposition: inline</code>. Dokumen yang bukan milik kontrak di URL ditolak 404.</p>
            <p>Tidak ada <code>storage:link</code>. Hapus kontrak menghapus direktori berkas lalu model. Baris dokumen ikut cascade.</p>

            <h2 id="polling">Daftar, dashboard, polling</h2>
            <p><code>GET /contracts</code> eager-load creator, approver, documents. Filter <code>q</code>, <code>status</code>, <code>from</code>/<code>to</code>, <code>creator</code>. Sort whitelist <code>number</code>, <code>subject</code>, <code>activation</code>, <code>status</code>, <code>creator</code>, <code>approver</code>, <code>approved</code>, default <code>created</code>. Pagination 5 dengan query string.</p>
            <p>Detail, approve, reject, dan delete memakai query <code>detail</code>, <code>approve</code>, <code>reject</code>, <code>delete</code> di halaman yang sama. Dialog putusan hanya untuk supervisor, dan hanya jika status masih <code>pending</code>.</p>
            <p><code>GET /pulse</code> mengembalikan JSON <code>latest</code> (max <code>updated_at</code>) dan <code>count</code>. <code>public/js/app.js</code> mem-poll tiap 15 detik. Kalau signature berubah, tombol <strong>Reload</strong> muncul. Tidak ada websocket dan tabel tidak ditulis ulang di DOM.</p>

            <h2 id="seeder">Seeder dan tes</h2>
            <p><code>DatabaseSeeder</code> membuat 3 user, 27 kontrak sebelum ada data UI, PDF 1.4 Helvetica, PNG via GD, counter, dan 3 activity. Spesimen: <code>INV/10/04</code>, <code>INV/10/03</code>, <code>INV/10/02</code>, <code>INV/09/18</code>, <code>INV/09/14</code>.</p>
            <p>Agregat Oktober 2026: 8 pending, 14 approved bulan itu, 3 rejected bulan itu, 5 dokumen bertanggal saat seed. Seed di bulan lain membuat kartu “bulan ini” mengikuti jam mesin.</p>
<pre><code>php artisan test</code></pre>
            <p>PHPUnit 9 memakai SQLite <code>:memory:</code>. <code>ContractFlowTest</code> menutup peran dan alur putusan. <code>DemoDataTest</code> menutup akun, spesimen, dan header <code>%PDF</code>.</p>

            <h2 id="jebakan">Hal yang sering menjebak</h2>
            <ul>
                <li><code>php artisan</code> di shell lama memakai PHP 8.5 dan gagal di platform check Composer.</li>
                <li>Fast setup SQLite tidak memakai MySQL, servicenya boleh Stopped. Opsi MySQL butuh service Running dan database yang sudah dibuat di phpMyAdmin.</li>
                <li><code>migrate:fresh --seed</code> aman diulang. <code>db:seed</code> di atas data yang ada gagal di unique email dan nomor.</li>
                <li>Unggahan juga dibatasi <code>upload_max_filesize</code> dan <code>post_max_size</code> php.ini XAMPP.</li>
                <li><code>INV/10/27</code> ada karena dibuat lewat UI saat verifikasi, bukan seeder. Fresh seed menghapusnya.</li>
            </ul>
        </article>

        <aside class="guide-toc">
            <p>Isi halaman</p>
            <a href="#runtime">Runtime</a>
            <a href="#boot">Menyalakan</a>
            <a href="#layout">Layout kode</a>
            <a href="#auth">Otorisasi</a>
            <a href="#skema">Skema</a>
            <a href="#nomor">Nomor kontrak</a>
            <a href="#berkas">Berkas</a>
            <a href="#polling">Polling</a>
            <a href="#seeder">Seeder dan tes</a>
            <a href="#jebakan">Jebakan</a>
        </aside>
    </div>
@endsection
