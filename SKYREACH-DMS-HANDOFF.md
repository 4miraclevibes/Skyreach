# Skyreach DMS — handoff untuk agent di editor

Baca file ini sampai habis sebelum menulis kode. Ini aplikasi yang harus dibangun, plus apa yang sudah dikerjakan di chat sebelumnya. Belum ada kode aplikasi. Yang sudah ada hanya lingkungan PHP di Mac dan prompt desain.

## Yang harus dikerjakan

Bangun **Skyreach Contracts**, aplikasi internal manajemen kontrak untuk perusahaan telekomunikasi VSAT [Skyreach](https://www.skyreach.id/).

- Frontend: **React**
- Backend: **Laravel 8**
- Database: **MySQL** lewat XAMPP (di Mac ini XAMPP memakai MariaDB, label di managernya "MySQL Database"; itu yang dipakai)
- PHP yang wajib dipakai: **7.4.33** dari XAMPP, bukan PHP Homebrew

Ini berasal dari tes teknis Document Management System. UI mengikuti desain Stitch di bagian bawah file ini dan gambar referensi.

### Data kontrak

CRUD kontrak dengan field:

- Nomor kontrak, otomatis dari bulan dan urutan. Contoh: `INV/08/01`, `INV/10/05`. Format `INV/{MM}/{urutan 2 digit}`. Tidak diketik pengguna.
- Perihal (dipakai di desain supaya tabel bisa dibaca; ini tambahan di atas field wajib tes)
- Tanggal aktivasi kontrak
- Tanggal approve kontrak
- Created By
- Approved By
- Dokumen pendukung, PDF atau gambar, bisa lebih dari satu
- Status: `Menunggu`, `Disetujui`, `Ditolak`
- Alasan penolakan, diisi Supervisor saat menolak

### Daftar kontrak

Tabel dengan filter, sort, dan pagination. Staff dan Supervisor sama-sama melihat daftar beserta statusnya.

### Detail

Tombol Detail membuka **drawer di halaman yang sama**. Jangan pindah ke halaman baru.

### Hak akses

Dua role saja.

| Role | Boleh | Tidak boleh |
| --- | --- | --- |
| Staff | Lihat daftar, lihat detail, buat kontrak, edit kontrak yang masih `Menunggu` | Approve, reject, hapus |
| Supervisor | Lihat daftar, lihat detail, approve, reject, hapus | Buat kontrak, edit kontrak |

Edit terkunci setelah status `Disetujui` atau `Ditolak`.

Akun yang harus ada dari seeder:

- `supervisor@mail.com` / `supervisor` — role Supervisor. Nama di desain: Sinta Wijaya
- `staff@mail.com` / `staff123` — role Staff. Nama di desain: Raka Putra

### Wajib dari tes

- Migration dan seeder Laravel, bukan SQL manual
- Dua role dan dua akun di atas
- Alur Staff membuat dan mengedit, Supervisor menyetujui, menolak, dan menghapus

### Nilai plus

- Data daftar terasa langsung: indikator "Diperbarui baru saja" (polling cukup)
- Dasbor ringkas seperti layar 8
- Fitur yang membuat aplikasi terasa selesai, tanpa mengubah aturan role

### Yang tidak dikerjakan di chat sebelumnya

Belum ada project Laravel, belum ada project React, belum ada database, belum ada repo khusus aplikasi ini. Jangan menganggap folder `holacom_rust` atau `holacom_flutter` sebagai aplikasi ini. Itu proyek lain.

## Lingkungan yang sudah disiapkan

Mac pengguna: MacBook Air, Apple Silicon, macOS baru. XAMPP **7.4.33** sudah terpasang dan sempat dibuka.

PHP 7.4.30 tidak ada untuk Mac. Rilis Mac terdekat adalah 7.4.33. Laravel 8 menerima PHP 7.3, 7.4, dan 8.0, jadi 7.4.33 sah. Jangan naik ke PHP 8.1, 8.2, atau 8.5. Laravel 8 sering error di sana. Di dalam XAMPP ada juga binary `php-8.2.4`; jangan dipakai.

Perintah `php` untuk Terminal baru sudah diarahkan ke 7.4.33:

- Symlink: `/Users/justputoff/.local/bin/php` → `/Applications/XAMPP/xamppfiles/bin/php-7.4.33`
- File `/Users/justputoff/.zshrc` menambahkan `$HOME/.local/bin` di depan `PATH`
- Composer Homebrew 2.10.2 (`#!/usr/bin/env php`) ikut memakai PHP 7.4.33. Sudah dicek lewat login shell: `PHP version 7.4.33`

PHP 8.5.9 **sengaja tidak dihapus**. Masih terpasang dari Homebrew:

- `/opt/homebrew/bin/php` → `/opt/homebrew/Cellar/php/8.5.9/bin/php`
- Cek versi lama: `/opt/homebrew/bin/php -v`

Jangan menghapus PHP 8.5. Jangan menaruh seluruh `/Applications/XAMPP/bin` di `PATH`. Folder itu menimpa `curl`, `openssl`, `perl`, `mysql`, dan banyak perintah lain dengan binary lama.

Sebelum `composer` atau `php artisan`, pastikan terminal baru. Terminal lama yang belum di-`source` bisa masih melihat PHP 8.5.9. Cek dengan `php -v`. Harus tertulis `PHP 7.4.33`.

XAMPP manager, kondisi terakhir yang terlihat:

- Apache Web Server: Running
- MySQL Database: **Stopped** — harus di-Start sebelum migrasi
- ProFTPD: tidak dipakai

Akun database bawaan XAMPP biasanya `root` tanpa kata sandi, port `3306`. Cek dulu, jangan mengarang kata sandi.

## Cara memakai desain

Gambar hasil Stitch ada di:

- `/Users/justputoff/Documents/HolaCom/.stitch-ref/login.jpg`
- `/Users/justputoff/Documents/HolaCom/.stitch-ref/list.jpg`
- `/Users/justputoff/Documents/HolaCom/.stitch-ref/detail.jpg`
- `/Users/justputoff/Documents/HolaCom/.stitch-ref/dash.jpg`

Itu sumber tampilan. Prompt di bawah adalah instruksi yang menghasilkan desain itu. Salin gambar ke project aplikasi kalau workspace-nya pindah.

Identitas:

- Nama: Skyreach. Tagline: Everywhere Excellence
- Produk di aplikasi: Manajemen Kontrak, internal, bukan website marketing skyreach.id
- Logo publik: `https://www.skyreach.id/assets/images/logo-238x238.png`
- Navy `#023C8E`, biru `#2A5CAA`, emas `#EFA646`, teal `#2F989D`, canvas `#F4F7FB`, tinta `#0E1A2B`, garis `#E4EAF2`
- UI: Plus Jakarta Sans. Nomor kontrak: IBM Plex Mono
- Status: Menunggu emas, Disetujui teal, Ditolak merah
- Lebar desain: desktop 1440px
- Bahasa antarmuka: Indonesia

Urutan layar yang didesain: daftar Staff, detail drawer, buat kontrak, edit, daftar Supervisor, dialog tolak, login, dasbor.

## Prompt Stitch

### Blok gaya

Tempel di awal setiap prompt setelah layar pertama.

```text
Continue the same Skyreach Contracts desktop web app, 1440px, light theme.
Same left sidebar, same top bar, same components.
Colors: navy #023C8E, blue #2A5CAA, gold #EFA646, teal #2F989D, canvas #F4F7FB, ink #0E1A2B, hairline #E4EAF2.
Fonts: Plus Jakarta Sans for UI, IBM Plex Mono for contract numbers.
Indonesian interface copy. High-fidelity production UI. Realistic Indonesian telecom contract data. No lorem ipsum, no marketing homepage, no stock photos of people, no purple gradients.
```

### Layar 1 — Daftar kontrak, akun Staff

```text
Design a high-fidelity desktop web app screen, 1440px wide, for Skyreach Contracts. Skyreach is an Indonesian VSAT telecommunications company. Tagline: Everywhere Excellence. This is their internal contract document system, not their public marketing website.

Visual system:
- Calm mission-control aesthetic. Precise, premium, quiet. Plenty of air, 8px spacing rhythm, hairline borders, soft shadow only on overlays.
- Canvas #F4F7FB. Ink #0E1A2B. Navy #023C8E. Blue #2A5CAA. Gold #EFA646. Teal #2F989D. Hairline #E4EAF2.
- Left sidebar 248px, navy #023C8E, wordmark “SKYREACH” in white with a small gold arc above the letter R suggesting a signal. Under it, “Manajemen Kontrak” in small caps, letterspacing wide.
- Sidebar items: Dasbor, Kontrak (active, gold left bar and slightly lighter navy pill), Dokumen. Bottom of sidebar: user card “Raka Putra”, role “Staff”, email staff@mail.com, and a quiet logout icon.
- Font: Plus Jakarta Sans. Contract numbers in IBM Plex Mono.
- Status pills, small and rounded: Menunggu (gold text on #FFF6E6), Disetujui (teal text on #E7F6F6), Ditolak (rose #9F2C24 on #FDECEC).

Page:
- Top bar: breadcrumb “Kontrak”, search field “Cari nomor kontrak”, and a gold primary button “Buat kontrak”.
- Page title “Kontrak” and subtitle “Dokumen yang Anda buat dan yang menunggu persetujuan.”
- Filter row, visually light: Status (Semua), Rentang tanggal aktivasi, Pembuat. A “Reset” text button.
- Data table on a white card, sticky header, sortable column labels with a subtle arrow. Columns: Nomor, Perihal, Aktivasi, Status, Dibuat oleh, Disetujui oleh, Tgl disetujui, Dokumen, Aksi.
- Rows, real data:
  1. INV/10/04 · Sewa kapasitas VSAT site Balikpapan · 12 Okt 2026 · Menunggu · Raka Putra · — · — · PDF · Detail, Edit
  2. INV/10/03 · Perpanjangan link VSAT kapal niaga · 09 Okt 2026 · Menunggu · Raka Putra · — · — · 2 file · Detail, Edit
  3. INV/10/02 · Instalasi VSAT kantor cabang Makassar · 05 Okt 2026 · Disetujui · Raka Putra · Sinta Wijaya · 08 Okt 2026 · PDF · Detail only
  4. INV/09/18 · Relokasi antena site Sorong · 21 Sep 2026 · Ditolak · Raka Putra · Sinta Wijaya · 24 Sep 2026 · Gambar · Detail only
  5. INV/09/14 · Kontrak bandwidth backbone Papua · 18 Sep 2026 · Disetujui · Dimas Aji · Sinta Wijaya · 20 Sep 2026 · PDF · Detail only
- Hover row: very pale blue. Approved and rejected rows have no Edit action. Pending rows show Edit as a text button.
- Document column is a small file chip, not a raw filename dump.
- Footer of the table: “Menampilkan 1–5 dari 24 kontrak” and pagination 1 (active) 2 3 … 5. A small live note at the right: a teal dot and “Diperbarui baru saja”.
- Empty illustration area is not needed; the table is full.

Do not show Approve, Reject, or Delete. Those belong to the Supervisor only. Do not open a new page. This screen is only the list.
```

Kalau generasi pertama meleset, prompt ulang yang dipakai:

```text
Regenerate this same screen. Keep every column, status, and Indonesian label. Make it calmer and more expensive: more whitespace, thinner borders, navy sidebar #023C8E, gold only on the primary button and the active nav, contract numbers in IBM Plex Mono. Remove any illustration, gradient blob, and dummy lorem text.
```

### Layar 2 — Detail kontrak, drawer

```text
Continue the same Skyreach Contracts desktop web app, 1440px, light theme.
Same left sidebar, same top bar, same components.
Colors: navy #023C8E, blue #2A5CAA, gold #EFA646, teal #2F989D, canvas #F4F7FB, ink #0E1A2B, hairline #E4EAF2.
Fonts: Plus Jakarta Sans for UI, IBM Plex Mono for contract numbers.
Indonesian interface copy. High-fidelity production UI. Realistic Indonesian telecom contract data. No lorem ipsum, no marketing homepage, no stock photos of people, no purple gradients.

Design the same contract list, now with a contract detail drawer open over it. The list stays visible and dimmed on the left. No new browser page, no route change visual.

Drawer, 520px, white, slides from the right, full height, soft shadow.
Header: mono number INV/10/04, status pill Menunggu, close icon.
Title: Sewa kapasitas VSAT site Balikpapan.
Meta grid:
- Aktivasi: 12 Oktober 2026
- Dibuat oleh: Raka Putra · Staff · 12 Okt 2026, 09.14
- Disetujui oleh: belum ada
- Tanggal disetujui: —
A short activity timeline: “Dibuat” done, “Menunggu tinjauan Supervisor” current in gold, “Disetujui / Ditolak” upcoming and muted.

Supporting document block: one PDF card “Kontrak_VSAT_Balikpapan.pdf”, 1.8 MB, with a document thumbnail, and buttons “Pratinjau” and “Unduh”. Preview panel under the card shows the first page of a clean contract cover, Skyreach navy header, not a broken image icon.

Footer of the drawer, because the viewer is Staff and status is Menunggu: secondary button “Edit kontrak”. No approve, no delete.

The table behind shows the same five rows, with the INV/10/04 row highlighted.
```

### Layar 3 — Buat kontrak, akun Staff

```text
Continue the same Skyreach Contracts desktop web app, 1440px, light theme.
Same left sidebar, same top bar, same components.
Colors: navy #023C8E, blue #2A5CAA, gold #EFA646, teal #2F989D, canvas #F4F7FB, ink #0E1A2B, hairline #E4EAF2.
Fonts: Plus Jakarta Sans for UI, IBM Plex Mono for contract numbers.
Indonesian interface copy. High-fidelity production UI. Realistic Indonesian telecom contract data. No lorem ipsum, no marketing homepage, no stock photos of people, no purple gradients.

Design a create-contract screen inside the same app shell. Staff is logged in as Raka Putra.

The main area is a centered white form card, max width 760px, not a giant empty admin page.
Title “Kontrak baru”. Helper text “Nomor dibuat otomatis dari bulan dan urutan. Contoh INV/10/05.”

Fields, clearly labeled, 16px labels, generous inputs:
- Nomor kontrak, read-only, mono, value INV/10/05, with a small lock icon and caption “Otomatis · Oktober 2026 · urutan 05”.
- Perihal, empty, placeholder “Contoh: Sewa kapasitas VSAT site Pontianak”.
- Tanggal aktivasi, date field showing a calendar icon, placeholder “Pilih tanggal”.
- Dibuat oleh, read-only, “Raka Putra · Staff”. Do not ask for Approved By or approval date on this form.
- Dokumen pendukung. A large dropzone: dashed border #2A5CAA at low opacity, icon of a document, text “Letakkan PDF atau gambar di sini”, subtext “PDF, JPG, atau PNG · bisa lebih dari satu file”. Below it, one attached file chip already shown as an example state is wrong — leave the dropzone empty.

Bottom bar stuck to the card: text button “Batal” and gold primary button “Simpan kontrak”.
A one-line note under the buttons: “Kontrak masuk sebagai Menunggu dan hanya bisa diedit sebelum disetujui atau ditolak.”
```

### Layar 4 — Edit kontrak yang masih Menunggu

```text
Continue the same Skyreach Contracts desktop web app, 1440px, light theme.
Same left sidebar, same top bar, same components.
Colors: navy #023C8E, blue #2A5CAA, gold #EFA646, teal #2F989D, canvas #F4F7FB, ink #0E1A2B, hairline #E4EAF2.
Fonts: Plus Jakarta Sans for UI, IBM Plex Mono for contract numbers.
Indonesian interface copy. High-fidelity production UI. Realistic Indonesian telecom contract data. No lorem ipsum, no marketing homepage, no stock photos of people, no purple gradients.

Same app shell and the same form layout as the create screen, now editing an existing pending contract.

Banner at the top of the card, pale gold: “Masih menunggu. Perubahan dikunci setelah Supervisor menyetujui atau menolak.”
Title “Edit kontrak”.
Prefilled:
- Nomor kontrak, locked, INV/10/04
- Perihal: Sewa kapasitas VSAT site Balikpapan
- Tanggal aktivasi: 12 Oktober 2026
- Dibuat oleh: Raka Putra · Staff, read-only
- Dokumen: file chip “Kontrak_VSAT_Balikpapan.pdf” with remove icon, plus the same dropzone to add another PDF or image.
Buttons: “Batal” and gold “Simpan perubahan”.
Do not show approval fields as editable.
```

### Layar 5 — Daftar kontrak, akun Supervisor

```text
Continue the same Skyreach Contracts desktop web app, 1440px, light theme.
Same left sidebar, same top bar, same components.
Colors: navy #023C8E, blue #2A5CAA, gold #EFA646, teal #2F989D, canvas #F4F7FB, ink #0E1A2B, hairline #E4EAF2.
Fonts: Plus Jakarta Sans for UI, IBM Plex Mono for contract numbers.
Indonesian interface copy. High-fidelity production UI. Realistic Indonesian telecom contract data. No lorem ipsum, no marketing homepage, no stock photos of people, no purple gradients.

Same contract list screen, now for Supervisor Sinta Wijaya, email supervisor@mail.com. Sidebar user card updates to her name and role “Supervisor”.

Remove “Buat kontrak”. She cannot create or edit.
Page subtitle: “Tinjau, setujui, tolak, atau hapus kontrak.”
Add a small segmented control above the table: Semua · Menunggu 2 · Disetujui · Ditolak. “Menunggu” looks selected-capable but “Semua” is the active segment.

Same five rows. Action column:
- Menunggu rows: Detail, Setujui (teal text), Tolak (rose text), Hapus (quiet)
- Disetujui and Ditolak rows: Detail and Hapus only. No Edit.

Keep sort headers, filters, pagination, and the live teal dot “Diperbarui baru saja”.
Do not open a dialog yet. This screen is the Supervisor list only.
```

### Layar 6 — Tolak kontrak, dialog di atas daftar

```text
Continue the same Skyreach Contracts desktop web app, 1440px, light theme.
Same left sidebar, same top bar, same components.
Colors: navy #023C8E, blue #2A5CAA, gold #EFA646, teal #2F989D, canvas #F4F7FB, ink #0E1A2B, hairline #E4EAF2.
Fonts: Plus Jakarta Sans for UI, IBM Plex Mono for contract numbers.
Indonesian interface copy. High-fidelity production UI. Realistic Indonesian telecom contract data. No lorem ipsum, no marketing homepage, no stock photos of people, no purple gradients.

Same Supervisor list, with a centered confirmation dialog over a dimmed table. No new page.

Dialog, 440px, white, radius 16px.
Eyebrow in rose: Tolak kontrak
Title in mono: INV/10/04
Body: “Sewa kapasitas VSAT site Balikpapan akan ditandai Ditolak. Staff tidak bisa mengeditnya lagi.”
A required textarea, label “Alasan”, placeholder “Contoh: Lampiran NPWP buram, mohon unggah ulang.”
Buttons: “Batal” text button, and a solid rose button “Tolak kontrak”.
Show the INV/10/04 row highlighted behind the scrim.
```

### Layar 7 — Login

```text
Design a desktop login for Skyreach Contracts, 1440px, split layout.

Left panel, 46% width, navy #023C8E. Wordmark SKYREACH in white, small gold arc. Tagline “Everywhere Excellence”. One sentence: “Manajemen kontrak internal untuk jaringan VSAT di seluruh Nusantara.” A very subtle constellation of thin gold lines in the lower left, geometric, not an illustration of a satellite dish. No people.

Right panel, canvas #F4F7FB, the form vertically centered, max width 400px.
Title “Masuk”. Subtitle “Gunakan akun Staff atau Supervisor.”
Fields: Email, Kata sandi, with a show-password icon. Gold full-width button “Masuk”.
Under the form, a quiet bordered card titled “Akses demo”:
- Supervisor · supervisor@mail.com · supervisor
- Staff · staff@mail.com · staff123
Each row has a small copy icon. This card is secondary, not louder than the login button.

Error state is not visible. The form is empty and calm.
```

### Layar 8 — Dasbor, nilai plus

```text
Continue the same Skyreach Contracts desktop web app, 1440px, light theme.
Same left sidebar, same top bar, same components.
Colors: navy #023C8E, blue #2A5CAA, gold #EFA646, teal #2F989D, canvas #F4F7FB, ink #0E1A2B, hairline #E4EAF2.
Fonts: Plus Jakarta Sans for UI, IBM Plex Mono for contract numbers.
Indonesian interface copy. High-fidelity production UI. Realistic Indonesian telecom contract data. No lorem ipsum, no marketing homepage, no stock photos of people, no purple gradients.

Design a dashboard as the home of Skyreach Contracts, same shell, Dasbor active in the sidebar. Logged in as Sinta Wijaya, Supervisor.

Title “Dasbor”. Subtitle “Ringkasan kontrak bulan Oktober 2026.”
Four stat cards in one row:
- Menunggu tinjauan, 8, gold accent
- Disetujui bulan ini, 14, teal
- Ditolak bulan ini, 3, rose
- Dokumen masuk hari ini, 5, navy

Below, two columns.
Left, wider: “Perlu tindakan”, a short list of three pending contracts, INV/10/04, INV/10/03, INV/10/01, each with perihal, relative time, and a “Tinjau” button.
Right: a slim monthly bar, “Kontrak menurut status”, three labeled counts, not a rainbow chart. Under it, “Aktivitas terakhir”: Sinta menyetujui INV/10/02, Raka mengunggah lampiran INV/10/04, sistem membuat nomor INV/10/05.

A live line at the top right of the content: teal dot, “Data langsung · baru saja”.
Keep it operational and sparse. No illustration hero.
```

## Catatan untuk agent berikutnya

- Mulai dari backend Laravel 8 dan frontend React yang meniru gambar di `.stitch-ref`, bukan dari website marketing Skyreach.
- Seed data contoh boleh mengikuti lima baris di layar 1, plus akun demo.
- Sidebar punya item Dokumen. Kalau belum ada spesifikasi terpisah, halaman itu boleh daftar berkas dari kontrak yang sudah diunggah, bukan modul baru yang mengubah RBAC.
- Jangan mengubah `~/.zshrc` atau menghapus PHP 8.5 kecuali pengguna memintanya.
