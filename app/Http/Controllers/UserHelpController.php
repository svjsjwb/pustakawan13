<?php

namespace App\Http\Controllers;

class UserHelpController extends Controller
{
    public function index()
    {
        $faqs = [
            [
                'q' => 'Berapa banyak buku yang dapat saya pinjam sekaligus?',
                'a' => 'Setiap karyawan dapat meminjam maksimal 3 (tiga) buku dalam satu waktu.',
            ],
            [
                'q' => 'Berapa lama durasi peminjaman buku?',
                'a' => 'Durasi peminjaman buku adalah 14 (empat belas) hari kalender sejak tanggal peminjaman.',
            ],
            [
                'q' => 'Apakah saya dapat memperpanjang masa peminjaman?',
                'a' => 'Ya, Anda dapat memperpanjang peminjaman dengan menghubungi petugas perpustakaan secara langsung atau melalui fitur Peminjaman di sistem ini.',
            ],
            [
                'q' => 'Bagaimana cara mereservasi buku yang sedang dipinjam?',
                'a' => 'Buka halaman Katalog, klik buku yang ingin direservasi, lalu klik tombol "Reservasi Buku". Anda akan mendapatkan nomor antrian dan notifikasi ketika buku tersedia.',
            ],
            [
                'q' => 'Bagaimana cara menambahkan buku ke daftar Favorit?',
                'a' => 'Klik ikon ❤️ pada kartu buku di Katalog, atau klik tombol "Tambah Favorit" di modal detail buku. Buku yang difavoritkan dapat diakses melalui menu Favorit.',
            ],
            [
                'q' => 'Bagaimana jika saya lupa mengembalikan buku tepat waktu?',
                'a' => 'Sistem akan mengirimkan notifikasi ketika batas pengembalian buku semakin dekat. Jika terlambat, harap segera hubungi petugas perpustakaan.',
            ],
            [
                'q' => 'Bagaimana cara mengubah kata sandi akun saya?',
                'a' => 'Buka halaman Profil, kemudian klik tab "Keamanan". Masukkan kata sandi lama dan kata sandi baru, lalu klik "Simpan Perubahan".',
            ],
        ];

        return view('user.help', compact('faqs'));
    }
}
