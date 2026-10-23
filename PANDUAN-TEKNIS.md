# Panduan teknis Skyreach Contracts

Aplikasi monolith Laravel 8. UI server-rendered (Blade + CSS + vanilla JS). Tidak ada SPA React dan tidak ada API publik untuk klien terpisah. Dua pilihan database: fast setup SQLite, atau MySQL lewat phpMyAdmin.

Dokumen pemakaian untuk Staff dan Supervisor ada di `PANDUAN-PENGGUNA.md`. Versi layar kedua panduan: `/guides` dan `/guides/technical`, keduanya di dalam sesi login.

## Runtime yang wajib

| Komponen | Versi / lokasi |
| --- | --- |
| PHP | 7.4.33 dari XAMPP, binary `/Applications/XAMPP/xamppfiles/bin/php-7.4.33` |
| Alias CLI | `/Users/justputoff/.local/bin/php` → binary di atas. `$HOME/.local/bin` didahulukan di `PATH` lewat `~/.zshrc` |
| Composer | 2.10.2 (Homebrew), shebang `#!/usr/bin/env php`, jadi ikut PHP 7.4.33 di shell baru |
| Framework | `laravel/framework` ^8.75 |
| Constraint PHP di `composer.json` | `^7.3\|^8.0` |

Jangan naik ke PHP 8.1, 8.2, atau 8.5. Laravel 8 tidak kompatibel dengan seri itu. PHP 8.5.9 Homebrew sengaja tetap terpasang di `/opt/homebrew/bin/php`. Jangan dihapus dan jangan menaruh `/Applications/XAMPP/bin` di `PATH`: folder itu menimpa `curl`, `openssl`, `perl`, dan `mysql` dengan binary lama.

Sebelum `composer` atau `php artisan`, shell harus melihat 7.4.33:

```bash
php -v
```

Shell yang sudah terbuka sebelum `PATH` diubah masih bisa memegang PHP 8.5.9. Buka terminal baru.

## PHP 7.4.30

Brief minta PHP 7.4.30. Mac ini M4, Homebrew-nya sudah tidak punya PHP 7.4. Cabang itu EOL 28 Nov 2022. Yang ada di `/opt/homebrew/bin/php` cuma 8.5.9, Laravel 8 tidak jalan.

Yang kepasang XAMPP 7.4.33. Itu rilis terakhir 7.4 (3 Nov 2022), setelah 7.4.30. Paket Mac untuk 7.4.30 tidak ada. Binary XAMPP-nya Intel (`x86_64`), di M4 lewat Rosetta.

Tetap PHP 7.4. Laravel 8 nerima (`^7.3|^8.0`). Di VPS pilihannya juga cuma "PHP 7.4", tidak ada 7.4.30.

Ekstensi yang dipakai: `pdo_sqlite`, `sqlite3`, `fileinfo`, `gd`, `mbstring`. `gd` hanya untuk sampul PNG yang dibuat seeder.

Zona waktu aplikasi `Asia/Jakarta`. Locale `id`. Carbon di-set di `AppServiceProvider`.

## Menyalakan dari nol

Direktori kerja adalah folder proyek Skyreach. Pilih salah satu.

### Fast setup SQLite

```bash
php -v
composer install
cp .env.example .env   # lewati kalau .env sudah ada
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve --host=127.0.0.1 --port=8000
```

Blok SQLite di `.env.example` sudah terbuka. `DB_DATABASE` dikosongkan supaya `config/database.php` jatuh ke `database_path('database.sqlite')`. Kalau `DB_DATABASE` terisi nama seperti `laravel`, SQLite akan membuka berkas dengan nama itu, bukan `database.sqlite`. Service MySQL boleh tetap Stopped.

### MySQL / phpMyAdmin

```bash
php -v
composer install
cp .env.example .env
php artisan key:generate
```

Di `.env`, komentari blok SQLite, lalu hapus tanda `#` pada blok MySQL. Jangan biarkan keduanya terbuka: nilai yang pertama yang dipakai.

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=skyreach
DB_USERNAME=root
DB_PASSWORD=
```

Di phpMyAdmin buat database `skyreach`, collation `utf8mb4_unicode_ci`. Sesuaikan nama, username, dan password. Ekstensi `pdo_mysql` harus aktif dan service MySQL harus Running.

```bash
php artisan migrate --seed
php artisan serve --host=127.0.0.1 --port=8000
```

`APP_KEY` di mesin ini sudah terisi. Jangan generate ulang di environment yang sudah menyimpan session, atau session lama tidak bisa didekripsi.

URL: http://127.0.0.1:8000

Reset total, termasuk data uji yang sudah dibuat lewat UI:

```bash
php artisan migrate:fresh --seed
```

Perintah itu menghapus seluruh isi database yang sedang dipakai, lalu mengisi ulang dari seeder. Berkas fisik di `storage/app/contracts/` tidak dihapus oleh migration. Hapus direktori itu manual kalau ingin storage ikut bersih.

## Layout kode

```
app/Http/Controllers/     Auth, Contract, Dashboard, Document, Activity
app/Http/Middleware/      EnsureRole, ShareAppData
app/Http/Requests/        Store, Update, Reject
app/Models/               User, Contract, ContractDocument, Activity
app/Services/ContractNumber.php
database/migrations/
database/seeders/DatabaseSeeder.php
database/database.sqlite  # gitignore
resources/views/          Blade
public/css/app.css
public/js/app.js
routes/web.php
tests/Feature/
```

Route `routes/api.php` bawaan Laravel tidak dipakai aplikasi ini.

## Autentikasi dan otorisasi

Guard `web`, driver session, session file (`storage/framework/sessions`). Tidak ada Laravel Breeze/Jetstream. Login ada di `AuthController`.

| Method | Path | Middleware | Perilaku |
| --- | --- | --- | --- |
| GET | `/login` | `guest` | Form login. Nama route `login` |
| POST | `/login` | `guest`, `throttle:10,1` | `Auth::attempt`. Gagal: validation error di field email |
| POST | `/logout` | `auth` | Invalidate session + regenerate CSRF token |
| GET | `/` | — | Redirect ke `/dashboard` atau `/login` |

`RouteServiceProvider::HOME` = `/dashboard`.

Role disimpan sebagai string di `users.role`: `staff` atau `supervisor`. Tidak ada package permission. Middleware `role` (`EnsureRole`) membandingkan string itu dan `abort(403)` kalau tidak cocok.

| Capability | Staff | Supervisor |
| --- | --- | --- |
| `GET /contracts` | ya | ya |
| `GET/POST /contracts/create`, `POST /contracts` | ya | 403 |
| `GET/PUT /contracts/{id}/edit` | ya, dan hanya jika `status = pending`. Selain itu redirect ke detail | 403 |
| `POST .../approve`, `POST .../reject`, `DELETE /contracts/{id}` | 403 | ya |
| Download / preview dokumen | ya | ya |

Form Request (`authorize()`) mengulang cek role yang sama dengan middleware. Update tidak menerima `number`, `created_by`, `status`, atau `approved_by` dari request. Nomor diterbitkan di server.

Staff dapat mengubah kontrak `pending` milik user lain. Bukan dibatasi `created_by`.

Login dibatasi 10 percobaan per menit per IP. Akun demo di-seed:

| Email | Password plaintext | `role` | `name` |
| --- | --- | --- | --- |
| supervisor@mail.com | `supervisor` | supervisor | Sinta Wijaya |
| staff@mail.com | `staff123` | staff | Raka Putra |
| dimas@mail.com | `staff123` | staff | Dimas Aji |

Password di-hash bcrypt lewat `Hash::make`. Kartu demo di login hanya menampilkan dua akun pertama. Password demo lemah dan tertulis di HTML. Jangan pakai pola ini di luar lingkungan uji.

`APP_DEBUG=true` di `.env` lokal. Matikan sebelum dipindah ke mesin yang bisa diakses orang lain.

## Skema

Migration, bukan SQL manual.

`users` — kolom bawaan Laravel 8 ditambah `role` string.

`contracts`

| Kolom | Tipe | Catatan |
| --- | --- | --- |
| `number` | string unique | `INV/{MM}/{nn}` |
| `subject` | string(180) | Perihal |
| `activation_date` | date | |
| `created_by` | FK `users` | |
| `approved_by` | FK `users` nullable | Pengambil keputusan, termasuk penolakan |
| `approved_at` | timestamp nullable | Waktu setuju atau tolak |
| `status` | string | `pending`, `approved`, `rejected` |
| `rejection_reason` | text nullable | Wajib diisi hanya pada transisi ke `rejected` |

`contract_documents` — `contract_id` cascade on delete, `original_name`, `path`, `mime`, `size`.

`activities` — `user_id` dan `contract_id` nullable, `nullOnDelete`. Deskripsi sudah mengandung nomor, jadi jejak tetap ada setelah kontrak dihapus.

`contract_sequences` — PK `period` (`Y-m`), `last_number`. Counter per bulan kalender.

Cast integer pada `created_by` dan `approved_by` perlu, karena PDO SQLite mengembalikan PK sebagai string.

## Nomor kontrak

`App\Services\ContractNumber`.

Format tampilan tidak memuat tahun: `sprintf('INV/%s/%02d', now()->format('m'), $sequence)`. Counter yang disimpan memakai `Y-m`, jadi Oktober 2026 dan Oktober 2027 tidak berbagi counter. Unique `contracts.number` tetap bisa bentrok antar tahun karena string `INV/10/01` tidak memuat tahun. `issue()` menaikkan urutan sampai nomor bebas. Batas keras 99 per bulan; selebihnya melempar `RuntimeException`.

`peek()` hanya membaca counter untuk ditampilkan di form. Nilai yang disimpan selalu dari `issue()` di dalam `withLock()`. Lock-nya `flock()` pada `storage/framework/cache/contract-number.lock`, lalu `DB::transaction`. `SELECT ... FOR UPDATE` tidak dipakai: grammar SQLite Laravel mengabaikan lock itu.

## Berkas

Validasi: `documents.*` = `file|mimes:pdf,jpg,jpeg,png|max:10240` (10 MB). Array boleh kosong.

Disk `local` → `storage/app/contracts/{contract_id}/{hash}`. Nama asli disimpan di `original_name` (`basename` dari client name). Download memakai `Storage::download`. Preview memakai `response()->file` dengan `Content-Disposition: inline`. `DocumentController` menolak dokumen yang `contract_id`-nya tidak sama dengan kontrak di URL (404).

Tidak ada `storage:link`. Berkas tidak di-serve dari `public/`.

Hapus kontrak: `Storage::deleteDirectory('contracts/'.$id)` lalu delete model. Baris dokumen ikut cascade.

## Daftar, dashboard, polling

`GET /contracts` — eager load `creator`, `approver`, `documents`. Filter `q` (nomor atau perihal, `LIKE`), `status`, `from`/`to` (`Y-m-d`), `creator` (id staff). Sort whitelist: `number`, `subject`, `activation`, `status`, `creator`, `approver`, `approved`, default `created`. Sort nama memakai `leftJoin` dan `select contracts.*`. Pagination 5, `withQueryString()`.

Detail, approve, reject, dan delete bukan route terpisah untuk UI-nya. Query string `detail`, `approve`, `reject`, `delete` di-render di halaman yang sama. Dialog reject/approve/delete hanya disusun jika user supervisor. `approve` dan `reject` diabaikan kalau status bukan `pending`.

`GET /pulse` mengembalikan JSON:

```json
{ "latest": "<max contracts.updated_at>", "count": 28 }
```

`ShareAppData` me-share `pulseLatest` dan `pulseCount` ke semua view di grup `auth`. `public/js/app.js` mem-poll tiap 15 detik. Signature `latest|count`. Kalau berubah, node `[data-pulse]` dapat class `is-fresh` dan tombol **Reload** (`location.reload()`). Tidak ada websocket, Echo, atau Pusher. Tidak ada rewrite DOM tabel.

Dashboard: count `pending` global; `approved` / `rejected` dengan `whereYear` + `whereMonth` pada `approved_at`; dokumen `whereDate(created_at, hari ini)`; tiga pending terbaru; tiga activity terbaru; bar status adalah count global.

## Seeder

`DatabaseSeeder` membuat 3 user, 27 kontrak (angka ini sebelum ada data dari UI), lampiran PDF minimal dan PNG via GD, counter `contract_sequences`, dan 3 activity yang sengaja disusun supaya feed dasbor terbaca.

Lima baris yang diminta spesimen:

| Nomor | Status | Pembuat |
| --- | --- | --- |
| INV/10/04 | pending | Raka |
| INV/10/03 | pending, 2 berkas | Raka |
| INV/10/02 | approved 2026-10-08 | Raka, diputuskan Sinta |
| INV/09/18 | rejected | Raka, diputuskan Sinta, PNG |
| INV/09/14 | approved | Dimas, diputuskan Sinta |

Agregat yang dijahit ke bulan berjalan Oktober 2026: 8 `pending`, 14 `approved` dengan `approved_at` di Oktober 2026, 3 `rejected` di Oktober 2026, 5 dokumen dengan `created_at = now()` saat seed. Kalau di-seed ulang di bulan lain, kartu “bulan ini” dan “hari ini” mengikuti jam mesin, bukan angka mockup.

PDF seeder adalah PDF 1.4 tulisan Helvetica, xref dihitung manual. Cukup untuk pratinjau di browser. Bukan dokumen kontrak sungguhan.

## Tes

PHPUnit 9, `phpunit.xml` memaksa `DB_CONNECTION=sqlite` dan `DB_DATABASE=:memory:`.

```bash
php artisan test
```

`ContractFlowTest` menutup login tamu, create staff, larangan silang peran, edit terkunci setelah putusan, reject tanpa alasan, increment nomor. `DemoDataTest` menutup akun, hash password, lima baris spesimen, agregat, dan header `%PDF`.

`Storage::fake('local')` hanya pada tes upload. Seeder tes menulis ke disk `local` sungguhan di `storage/app/contracts/`.

## Hal yang sering menjebak

- `php artisan` di shell lama memakai PHP 8.5 dan langsung gagal di platform check Composer.
- Fast setup SQLite tidak memakai MySQL, service “MySQL Database” di XAMPP boleh tetap Stopped. Opsi MySQL butuh service itu Running dan database yang sudah dibuat di phpMyAdmin.
- `migrate:fresh --seed` dua kali aman. `db:seed` di atas data yang sudah ada gagal di unique email dan unique nomor.
- Unggahan ikut `upload_max_filesize` dan `post_max_size` php.ini XAMPP, selain rule 10 MB.
- Kontrak `INV/10/27` (perihal Pontianak) ada di SQLite mesin ini karena dibuat lewat UI saat verifikasi, bukan dari seeder. `migrate:fresh --seed` menghapusnya.
