@extends('layouts.app')

@section('title', 'Panduan pemakaian · Skyreach')
@section('crumb', 'Beranda / Panduan')

@section('content')
    <div class="page-head">
        <div>
            <h1>Panduan pemakaian</h1>
            <p class="lede">Cara memakai Skyreach Contracts sehari-hari. Bukan situs publik skyreach.id.</p>
        </div>
    </div>

    @include('partials.guide-switch')

    <div class="guide-layout">
        <article class="guide">
            <h2 id="login">Login</h2>
            <p>Di halaman Login ada dua akun percobaan. Klik barisnya, formulir terisi sendiri, lalu tekan <strong>Login</strong>.</p>
            <table>
                <thead>
                    <tr><th>Siapa</th><th>Email</th><th>Password</th><th>Nama di layar</th></tr>
                </thead>
                <tbody>
                    <tr><td>Staff</td><td class="mono">staff@mail.com</td><td class="mono">staff123</td><td>Raka Putra</td></tr>
                    <tr><td>Supervisor</td><td class="mono">supervisor@mail.com</td><td class="mono">supervisor</td><td>Sinta Wijaya</td></tr>
                </tbody>
            </table>
            <p><strong>Copy</strong> hanya menyalin password. <strong>Logout</strong> ada di kartu nama, pojok kiri bawah.</p>
            <p>Ada satu akun Staff lain di data contoh, Dimas Aji (<span class="mono">dimas@mail.com</span> / <span class="mono">staff123</span>). Dia tidak ditampilkan di kartu demo. Dipakai supaya ada kontrak yang pembuatnya bukan Raka.</p>

            <h2 id="peran">Dua peran, dua pekerjaan</h2>
            <div class="role-grid">
                <div class="role-card">
                    <strong>Staff</strong>
                    <p>Membuat kontrak dan memperbaiki yang masih menunggu.</p>
                </div>
                <div class="role-card">
                    <strong>Supervisor</strong>
                    <p>Memeriksa. Bisa menyetujui, menolak, atau menghapus. Tidak bisa membuat atau mengubah isi kontrak.</p>
                </div>
            </div>
            <p>Kalau sebuah tombol tidak ada di layar Anda, itu memang bukan bagian tugas peran itu.</p>

            <h2 id="status">Arti status</h2>
            <div class="status-row">
                <div class="note-card"><span class="pill pill-pending">Menunggu</span><p>Baru masuk. Masih bisa diubah Staff.</p></div>
                <div class="note-card"><span class="pill pill-approved">Disetujui</span><p>Supervisor sudah setuju. Isinya terkunci.</p></div>
                <div class="note-card"><span class="pill pill-rejected">Ditolak</span><p>Ada alasan penolakan. Isinya terkunci. Perbaikan berarti kontrak baru.</p></div>
            </div>

            <h2 id="daftar">Melihat daftar kontrak</h2>
            <p>Menu <strong>Kontrak</strong>.</p>
            <ul>
                <li>Kotak cari di atas mencari nomor atau perihal.</li>
                <li>Filter: status, rentang tanggal aktivasi, dan pembuat. Tekan <strong>Apply</strong>. <strong>Reset</strong> mengembalikan daftar penuh.</li>
                <li>Judul kolom bisa diklik untuk mengurutkan.</li>
                <li>Satu halaman menampilkan 5 kontrak.</li>
            </ul>
            <p>Supervisor punya tombol cepat di atas tabel: Semua, Menunggu, Disetujui, Ditolak. Angkanya jumlah seluruh kontrak dengan status itu, bukan hanya yang kelihatan di halaman ini.</p>
            <h3>Staff, kolom Aksi</h3>
            <ul>
                <li>Menunggu: <strong>Detail</strong> dan <strong>Edit</strong></li>
                <li>Sudah disetujui atau ditolak: <strong>Detail</strong> saja</li>
            </ul>
            <h3>Supervisor, kolom Aksi</h3>
            <ul>
                <li>Menunggu: <strong>Detail</strong>, <strong>Setujui</strong>, <strong>Tolak</strong>, <strong>Hapus</strong></li>
                <li>Sudah diputuskan: <strong>Detail</strong> dan <strong>Hapus</strong></li>
            </ul>

            <h2 id="detail">Membaca detail</h2>
            <p><strong>Detail</strong> membuka panel dari kanan. Daftar di belakangnya tetap ada, hanya diredupkan. Ini bukan halaman baru.</p>
            <p>Di panel itu ada nomor, status, tanggal, siapa yang membuat, siapa yang memutuskan, alur singkat, dan lampiran. <strong>Preview</strong> menampilkan berkas di panel. <strong>Tab baru</strong> membuka PDF di tab peramban. <strong>Download</strong> menyimpan berkas ke komputer.</p>
            <p>Tutup dengan tanda silang, atau klik area gelap di luar panel.</p>

            <h2 id="buat">Membuat kontrak</h2>
            <p>Khusus Staff. Tombol emas <strong>Buat kontrak</strong>.</p>
            <ul>
                <li><strong>Perihal</strong>, misalnya “Sewa kapasitas VSAT site Pontianak”.</li>
                <li><strong>Tanggal aktivasi</strong>.</li>
                <li><strong>Dokumen</strong>, kalau ada. PDF, JPG, atau PNG. Boleh lebih dari satu. Satu berkas paling besar 10 MB. Boleh juga disimpan tanpa lampiran.</li>
            </ul>
            <p>Nomor tidak diketik. Bentuknya <span class="mono">INV/bulan/urutan</span>, contoh <span class="mono">INV/10/27</span>. Angka bulan adalah bulan saat kontrak disimpan, bukan bulan tanggal aktivasi. Urutan dua digit, mulai dari 01 tiap bulan, paling tinggi 99.</p>
            <p><strong>Dibuat oleh</strong> mengikuti akun yang sedang login. Tekan <strong>Simpan kontrak</strong>. Status langsung Menunggu.</p>

            <h2 id="ubah">Mengubah kontrak</h2>
            <p><strong>Edit</strong> hanya ada selagi status Menunggu. Bisa mengubah perihal, tanggal aktivasi, menambah lampiran, atau melepas lampiran yang sudah ada. Nomor tidak berubah.</p>
            <p>Setelah disetujui atau ditolak, formulir edit tidak bisa dipakai lagi. Staff boleh mengedit kontrak Menunggu milik Staff lain. Supervisor tetap tidak bisa mengedit.</p>

            <h2 id="setujui">Menyetujui</h2>
            <p>Khusus Supervisor. <strong>Setujui</strong>, baca ringkasannya, lalu <strong>Setujui kontrak</strong>. Nama Supervisor dan tanggal keputusan tercatat. Staff tidak bisa mengubah kontrak itu lagi.</p>

            <h2 id="tolak">Menolak</h2>
            <p><strong>Tolak</strong>. Alasan wajib diisi. Tanpa alasan, penolakan tidak tersimpan. Contoh: “Lampiran NPWP buram, mohon unggah ulang.” Alasan itu terlihat di panel detail. Kontrak yang ditolak juga terkunci.</p>

            <h2 id="hapus">Menghapus</h2>
            <p><strong>Hapus</strong> bisa untuk status apa pun. Kontrak dan lampirannya ikut terhapus. Tidak bisa dikembalikan.</p>

            <h2 id="dashboard">Dashboard</h2>
            <p>Halaman awal setelah login. Empat angka di atas:</p>
            <ul>
                <li>menunggu tinjauan, semua yang belum diputuskan</li>
                <li>disetujui bulan ini</li>
                <li>ditolak bulan ini</li>
                <li>dokumen yang masuk hari ini</li>
            </ul>
            <p><strong>Perlu tindakan</strong> memuat tiga kontrak Menunggu yang paling baru. <strong>Tinjau</strong> membuka detailnya. <strong>Kontrak menurut status</strong> menghitung semua kontrak, tidak hanya bulan ini. <strong>Aktivitas terakhir</strong> mencatat buat, unggah, ubah, setujui, tolak, dan hapus.</p>

            <h2 id="dokumen">Dokumen</h2>
            <p>Menu <strong>Dokumen</strong> adalah daftar berkas yang menempel pada kontrak. Bukan tempat arsip terpisah. Dari sini bisa preview, download, atau lompat ke kontrak pemilik berkas.</p>

            <h2 id="langsung">Tanda “Diperbarui baru saja”</h2>
            <p>Aplikasi memeriksa perubahan kira-kira setiap 15 detik. Kalau orang lain menyimpan, menyetujui, menolak, atau menghapus sesuatu, muncul tombol <strong>Reload</strong>.</p>
            <p>Daftar tidak berubah sendiri. Tekan <strong>Reload</strong> supaya yang baru ikut tampil. Panel detail yang sedang terbuka tidak akan tertutup paksa.</p>

            <h2 id="masalah">Kalau sesuatu terasa salah</h2>
            <div class="callout">
                <ul>
                    <li>Tidak melihat <strong>Buat kontrak</strong>: Anda login sebagai Supervisor.</li>
                    <li>Tidak melihat <strong>Setujui</strong>: Anda login sebagai Staff.</li>
                    <li><strong>Edit</strong> hilang: kontrak sudah disetujui atau ditolak.</li>
                    <li>Tidak bisa menolak: alasan masih kosong.</li>
                    <li>Nomor di formulir berbeda setelah disimpan: ada orang lain yang membuat kontrak lebih dulu. Nomor yang tersimpan yang berlaku.</li>
                    <li>Halaman menolak membuka: peran Anda tidak berhak. Kembali ke dashboard.</li>
                </ul>
            </div>
        </article>

        <aside class="guide-toc">
            <p>Isi halaman</p>
            <a href="#login">Login</a>
            <a href="#peran">Dua peran</a>
            <a href="#status">Status</a>
            <a href="#daftar">Daftar kontrak</a>
            <a href="#detail">Detail</a>
            <a href="#buat">Membuat</a>
            <a href="#ubah">Mengubah</a>
            <a href="#setujui">Menyetujui</a>
            <a href="#tolak">Menolak</a>
            <a href="#hapus">Menghapus</a>
            <a href="#dashboard">Dashboard</a>
            <a href="#dokumen">Dokumen</a>
            <a href="#langsung">Data langsung</a>
            <a href="#masalah">Kalau macet</a>
        </aside>
    </div>
@endsection
