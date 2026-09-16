<?php

namespace App\Http\Controllers;

class UserAnnouncementController extends Controller
{
    public function index()
    {
        // Pengumuman statis yang realistis untuk perpustakaan perusahaan
        $announcements = collect([
            [
                'id'       => 1,
                'type'     => 'info',
                'category' => 'Pengumuman Perpustakaan',
                'title'    => 'Jam Operasional Perpustakaan',
                'content'  => 'Perpustakaan PT Tiga Serangkai Inti Corpora beroperasi setiap hari Senin – Jumat pukul 07.30 – 17.00 WIB. Pada hari Sabtu, Minggu, dan Libur Nasional perpustakaan tutup.',
                'date'     => '2026-09-01',
                'pinned'   => true,
                'author'   => 'Petugas Perpustakaan',
            ],
            [
                'id'       => 2,
                'type'     => 'event',
                'category' => 'Workshop',
                'title'    => 'Workshop: Produktivitas dan Manajemen Waktu',
                'content'  => 'Perpustakaan mengundang seluruh karyawan untuk mengikuti workshop "Produktivitas dan Manajemen Waktu" yang akan diselenggarakan pada Rabu, 10 September 2026 pukul 09.00 – 12.00 WIB di Ruang Pelatihan Lantai 3.',
                'date'     => '2026-09-03',
                'pinned'   => true,
                'author'   => 'Tim HRD',
            ],
            [
                'id'       => 3,
                'type'     => 'update',
                'category' => 'Koleksi Baru',
                'title'    => 'Penambahan Koleksi Buku Bulan September 2026',
                'content'  => 'Perpustakaan telah menerima 87 judul buku baru untuk bulan September 2026, meliputi kategori Teknologi, Pengembangan Diri, dan Buku Pendidikan. Silakan kunjungi Katalog untuk melihat koleksi terbaru.',
                'date'     => '2026-09-02',
                'pinned'   => false,
                'author'   => 'Petugas Perpustakaan',
            ],
            [
                'id'       => 4,
                'type'     => 'reminder',
                'category' => 'Pengingat',
                'title'    => 'Batas Maksimal Peminjaman Buku',
                'content'  => 'Setiap karyawan dapat meminjam maksimal 3 (tiga) buku dalam satu waktu dengan durasi peminjaman 14 (empat belas) hari. Harap kembalikan buku tepat waktu agar karyawan lain dapat menggunakannya.',
                'date'     => '2026-08-28',
                'pinned'   => false,
                'author'   => 'Petugas Perpustakaan',
            ],
            [
                'id'       => 5,
                'type'     => 'event',
                'category' => 'Seminar',
                'title'    => 'Seminar: Literasi Digital untuk Profesional',
                'content'  => 'Dalam rangka HUT PT Tiga Serangkai Inti Corpora, perpustakaan bekerja sama dengan Divisi IT menyelenggarakan seminar Literasi Digital. Pendaftaran dapat dilakukan melalui portal internal perusahaan.',
                'date'     => '2026-08-25',
                'pinned'   => false,
                'author'   => 'Divisi IT & Perpustakaan',
            ],
            [
                'id'       => 6,
                'type'     => 'info',
                'category' => 'Sistem',
                'title'    => 'Pembaruan Sistem Web Library',
                'content'  => 'Sistem Web Library telah diperbarui dengan fitur-fitur baru: Favorit Buku, Manajemen Reservasi, dan Riwayat Peminjaman yang lebih lengkap. Silakan eksplorasi fitur baru yang tersedia.',
                'date'     => '2026-09-04',
                'pinned'   => true,
                'author'   => 'Tim IT',
            ],
        ]);

        return view('user.announcements', compact('announcements'));
    }
}
