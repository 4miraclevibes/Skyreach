# Panduan pemakaian Skyreach Contracts

Versi layar ada di menu **Panduan** setelah login, alamat `/guides`.

Ini aplikasi internal untuk mencatat kontrak VSAT Skyreach. Bukan situs publik skyreach.id.

Buka di peramban: http://127.0.0.1:8000

Kalau halaman tidak muncul, minta tim IT menyalakan aplikasinya dulu.

## Login

Di halaman **Login** ada dua akun percobaan. Klik barisnya, formulir terisi sendiri, lalu tekan **Login**.

| Siapa | Email | Password | Nama di layar |
| --- | --- | --- | --- |
| Staff | staff@mail.com | staff123 | Raka Putra |
| Supervisor | supervisor@mail.com | supervisor | Sinta Wijaya |

Tombol **Copy** hanya menyalin password. Tombol **Logout** ada di kartu nama, pojok kiri bawah.

Ada satu akun Staff lain di data contoh, Dimas Aji (`dimas@mail.com` / `staff123`). Dia tidak ditampilkan di kartu demo. Dipakai supaya ada kontrak yang pembuatnya bukan Raka.

## Dua peran, dua pekerjaan

**Staff** membuat kontrak dan memperbaiki yang masih menunggu.

**Supervisor** memeriksa. Bisa menyetujui, menolak, atau menghapus. Tidak bisa membuat kontrak baru dan tidak bisa mengubah isinya.

Kalau sebuah tombol tidak ada di layar Anda, itu memang bukan bagian tugas peran itu.

## Arti status

- **Menunggu** — baru masuk, masih bisa diubah Staff.
- **Disetujui** — Supervisor sudah setuju. Isinya terkunci.
- **Ditolak** — Supervisor menolak dan menulis alasan. Isinya juga terkunci. Staff tidak bisa mengedit lagi; kalau perlu diperbaiki, buat kontrak baru.

## Melihat daftar kontrak

Menu **Kontrak**.

- Kotak cari di atas mencari nomor atau perihal.
- Filter: status, rentang tanggal aktivasi, dan pembuat. Tekan **Apply**. **Reset** mengembalikan daftar penuh.
- Judul kolom bisa diklik untuk mengurutkan.
- Satu halaman menampilkan 5 kontrak.

Supervisor punya tombol cepat di atas tabel: Semua, Menunggu, Disetujui, Ditolak. Angkanya adalah jumlah seluruh kontrak dengan status itu, bukan hanya yang kelihatan di halaman ini.

### Staff, kolom Aksi

- Menunggu: **Detail** dan **Edit**
- Sudah disetujui atau ditolak: **Detail** saja

### Supervisor, kolom Aksi

- Menunggu: **Detail**, **Setujui**, **Tolak**, **Hapus**
- Sudah diputuskan: **Detail** dan **Hapus**

## Membaca detail

**Detail** membuka panel dari kanan. Daftar di belakangnya tetap ada, hanya diredupkan. Ini bukan halaman baru.

Di panel itu ada nomor, status, tanggal, siapa yang membuat, siapa yang memutuskan, alur singkat, dan lampiran. **Preview** menampilkan berkas di panel. **Download** menyimpan berkas ke komputer.

Tutup dengan tanda silang, atau klik area gelap di luar panel.

## Membuat kontrak (Staff)

Tombol emas **Buat kontrak**.

Yang diisi:

- **Perihal**, misalnya “Sewa kapasitas VSAT site Pontianak”.
- **Tanggal aktivasi**.
- **Dokumen**, kalau ada. PDF, JPG, atau PNG. Boleh lebih dari satu. Satu berkas paling besar 10 MB. Boleh juga disimpan tanpa lampiran.

Nomor tidak diketik. Bentuknya `INV/bulan/urutan`, contoh `INV/10/27`. Angka bulan adalah bulan saat kontrak disimpan, bukan bulan tanggal aktivasi. Urutan dua digit, mulai dari 01 tiap bulan kalender, paling tinggi 99.

**Dibuat oleh** otomatis mengikuti akun yang sedang masuk.

Tekan **Simpan kontrak**. Status langsung **Menunggu**.

## Mengubah kontrak (Staff)

**Edit** hanya ada selagi status Menunggu. Bisa mengubah perihal, tanggal aktivasi, menambah lampiran, atau melepas lampiran yang sudah ada.

Nomor tidak berubah.

Setelah disetujui atau ditolak, formulir edit tidak bisa dipakai lagi.

Staff boleh mengedit kontrak Menunggu milik Staff lain. Supervisor tetap tidak bisa mengedit.

## Menyetujui (Supervisor)

**Setujui**, baca ringkasannya, lalu **Setujui kontrak**.

Nama Supervisor dan tanggal keputusan tercatat. Staff tidak bisa mengubah kontrak itu lagi.

## Menolak (Supervisor)

**Tolak**. Alasan wajib diisi. Tanpa alasan, penolakan tidak tersimpan.

Contoh alasan: “Lampiran NPWP buram, mohon unggah ulang.”

Alasan itu terlihat di panel detail. Kontrak yang ditolak juga terkunci.

## Menghapus (Supervisor)

**Hapus** bisa untuk status apa pun. Kontrak dan lampirannya ikut terhapus. Tidak bisa dikembalikan.

## Dashboard

Menu **Dashboard** adalah halaman awal setelah login.

Empat angka di atas:

- menunggu tinjauan, semua yang belum diputuskan
- disetujui bulan ini
- ditolak bulan ini
- dokumen yang masuk hari ini

**Perlu tindakan** memuat tiga kontrak Menunggu yang paling baru. **Tinjau** membuka detailnya.

**Kontrak menurut status** menghitung semua kontrak, tidak hanya bulan ini.

**Aktivitas terakhir** mencatat buat, unggah, ubah, setujui, tolak, dan hapus. **Lihat semua** membuka daftar yang lebih panjang.

## Dokumen

Menu **Dokumen** adalah daftar berkas yang menempel pada kontrak. Bukan tempat arsip terpisah. Dari sini bisa preview, download, atau lompat ke kontrak pemilik berkas.

## Tanda “Diperbarui baru saja”

Aplikasi memeriksa perubahan kira-kira setiap 15 detik. Kalau orang lain menyimpan, menyetujui, menolak, atau menghapus sesuatu, muncul tombol **Reload**.

Daftar tidak berubah sendiri. Tekan **Reload** supaya yang baru ikut tampil. Panel detail yang sedang terbuka tidak akan tertutup paksa.

## Kalau sesuatu terasa salah

- Tidak melihat **Buat kontrak**: Anda login sebagai Supervisor.
- Tidak melihat **Setujui**: Anda login sebagai Staff.
- **Edit** hilang: kontrak sudah disetujui atau ditolak.
- Tidak bisa menolak: alasan masih kosong.
- Nomor di formulir berbeda dengan nomor setelah disimpan: ada orang lain yang membuat kontrak lebih dulu. Nomor yang tersimpan yang berlaku.
- Halaman menolak membuka: peran Anda tidak berhak. Kembali ke dashboard.
