<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | BUKU PENDIDIKAN - SD
        |--------------------------------------------------------------------------
        */

        $definitions = [
            'Buku Pendidikan' => [
                'SD' => [
                    ['Matematika Ceria Kelas 1 SD', 'Budi Santoso, M.Pd.', 'Erlangga Edukasi', 2022, '372.7 BUD m'],
                    ['Matematika Ceria Kelas 2 SD', 'Budi Santoso, M.Pd.', 'Erlangga Edukasi', 2023, '372.7 BUD m'],
                    ['Matematika Ceria Kelas 3 SD', 'Budi Santoso, M.Pd.', 'Erlangga Edukasi', 2024, '372.7 BUD m'],
                    ['Matematika Ceria Kelas 4 SD', 'Budi Santoso, M.Pd.', 'Erlangga Edukasi', 2022, '372.7 BUD m'],
                    ['Matematika Ceria Kelas 5 SD', 'Budi Santoso, M.Pd.', 'Erlangga Edukasi', 2023, '372.7 BUD m'],
                    ['Matematika Ceria Kelas 6 SD', 'Budi Santoso, M.Pd.', 'Erlangga Edukasi', 2024, '372.7 BUD m'],

                    ['IPAS Eksplorasi Alam Kelas 1', 'Siti Aminah, S.Pd.', 'Yudhistira', 2022, '372.3 SIT i'],
                    ['IPAS Eksplorasi Alam Kelas 2', 'Siti Aminah, S.Pd.', 'Yudhistira', 2023, '372.3 SIT i'],
                    ['IPAS Eksplorasi Alam Kelas 3', 'Siti Aminah, S.Pd.', 'Yudhistira', 2024, '372.3 SIT i'],
                    ['IPAS Eksplorasi Alam Kelas 4', 'Siti Aminah, S.Pd.', 'Yudhistira', 2022, '372.3 SIT i'],
                    ['IPAS Eksplorasi Alam Kelas 5', 'Siti Aminah, S.Pd.', 'Yudhistira', 2023, '372.3 SIT i'],
                    ['IPAS Eksplorasi Alam Kelas 6', 'Siti Aminah, S.Pd.', 'Yudhistira', 2024, '372.3 SIT i'],

                    ['Bahasa Indonesia Pintar Kelas 1', 'Hendra Setiawan', 'Balai Pustaka', 2021, '372.6 HEN b'],
                    ['Bahasa Indonesia Pintar Kelas 2', 'Hendra Setiawan', 'Balai Pustaka', 2022, '372.6 HEN b'],
                    ['Bahasa Indonesia Pintar Kelas 3', 'Hendra Setiawan', 'Balai Pustaka', 2023, '372.6 HEN b'],
                    ['Bahasa Indonesia Pintar Kelas 4', 'Hendra Setiawan', 'Balai Pustaka', 2024, '372.6 HEN b'],
                    ['Bahasa Indonesia Pintar Kelas 5', 'Hendra Setiawan', 'Balai Pustaka', 2022, '372.6 HEN b'],
                    ['Bahasa Indonesia Pintar Kelas 6', 'Hendra Setiawan', 'Balai Pustaka', 2023, '372.6 HEN b'],

                    ['Bahasa Inggris Ceria SD Kelas 1', 'Dewi Anggraini, S.Pd.', 'Intan Pariwara', 2022, '372.65 DEW b'],
                    ['Bahasa Inggris Ceria SD Kelas 2', 'Dewi Anggraini, S.Pd.', 'Intan Pariwara', 2023, '372.65 DEW b'],
                    ['Bahasa Inggris Ceria SD Kelas 3', 'Dewi Anggraini, S.Pd.', 'Intan Pariwara', 2024, '372.65 DEW b'],
                    ['Bahasa Inggris Ceria SD Kelas 4', 'Dewi Anggraini, S.Pd.', 'Intan Pariwara', 2022, '372.65 DEW b'],
                    ['Bahasa Inggris Ceria SD Kelas 5', 'Dewi Anggraini, S.Pd.', 'Intan Pariwara', 2023, '372.65 DEW b'],
                    ['Bahasa Inggris Ceria SD Kelas 6', 'Dewi Anggraini, S.Pd.', 'Intan Pariwara', 2024, '372.65 DEW b'],

                    ['Pendidikan Pancasila SD Kelas 1', 'Rudi Hartono, M.Pd.', 'Grafindo', 2022, '323 RUD p'],
                    ['Pendidikan Pancasila SD Kelas 2', 'Rudi Hartono, M.Pd.', 'Grafindo', 2023, '323 RUD p'],
                    ['Pendidikan Pancasila SD Kelas 3', 'Rudi Hartono, M.Pd.', 'Grafindo', 2024, '323 RUD p'],
                    ['Pendidikan Pancasila SD Kelas 4', 'Rudi Hartono, M.Pd.', 'Grafindo', 2022, '323 RUD p'],
                    ['Pendidikan Pancasila SD Kelas 5', 'Rudi Hartono, M.Pd.', 'Grafindo', 2023, '323 RUD p'],
                    ['Pendidikan Pancasila SD Kelas 6', 'Rudi Hartono, M.Pd.', 'Grafindo', 2024, '323 RUD p'],

                    ['Sains Menyenangkan untuk SD', 'Maya Sari, S.Pd.', 'Tiga Serangkai', 2022, '372.35 MAY s'],
                    ['Eksperimen Sains Sederhana', 'Maya Sari, S.Pd.', 'Tiga Serangkai', 2023, '372.35 MAY e'],
                    ['Ilmu Pengetahuan Alam Dasar', 'Maya Sari, S.Pd.', 'Tiga Serangkai', 2024, '372.35 MAY i'],
                    ['Belajar Sains dari Lingkungan', 'Maya Sari, S.Pd.', 'Tiga Serangkai', 2022, '372.35 MAY b'],
                    ['Sains dan Kehidupan Sehari-hari', 'Maya Sari, S.Pd.', 'Tiga Serangkai', 2023, '372.35 MAY s'],
                    ['Mengenal Alam Sekitar', 'Maya Sari, S.Pd.', 'Tiga Serangkai', 2024, '372.35 MAY m'],

                    ['Tematik Kreatif SD Kelas 1', 'Andi Wijaya, M.Pd.', 'Bumi Aksara', 2022, '372 AND t'],
                    ['Tematik Kreatif SD Kelas 2', 'Andi Wijaya, M.Pd.', 'Bumi Aksara', 2023, '372 AND t'],
                    ['Tematik Kreatif SD Kelas 3', 'Andi Wijaya, M.Pd.', 'Bumi Aksara', 2024, '372 AND t'],
                    ['Tematik Kreatif SD Kelas 4', 'Andi Wijaya, M.Pd.', 'Bumi Aksara', 2022, '372 AND t'],
                    ['Tematik Kreatif SD Kelas 5', 'Andi Wijaya, M.Pd.', 'Bumi Aksara', 2023, '372 AND t'],
                    ['Tematik Kreatif SD Kelas 6', 'Andi Wijaya, M.Pd.', 'Bumi Aksara', 2024, '372 AND t'],

                    ['Membaca dan Menulis untuk SD', 'Lina Marlina, S.Pd.', 'Pustaka Pelajar', 2022, '372.4 LIN m'],
                    ['Menulis Kreatif Anak SD', 'Lina Marlina, S.Pd.', 'Pustaka Pelajar', 2023, '372.4 LIN m'],
                    ['Keterampilan Membaca SD', 'Lina Marlina, S.Pd.', 'Pustaka Pelajar', 2024, '372.4 LIN k'],
                    ['Cerita dan Bahasa Indonesia SD', 'Lina Marlina, S.Pd.', 'Pustaka Pelajar', 2022, '372.4 LIN c'],
                    ['Latihan Bahasa Indonesia SD', 'Lina Marlina, S.Pd.', 'Pustaka Pelajar', 2023, '372.4 LIN l'],
                    ['Bahasa Indonesia Aktif SD', 'Lina Marlina, S.Pd.', 'Pustaka Pelajar', 2024, '372.4 LIN b'],
                ],

                /*
                |--------------------------------------------------------------------------
                | BUKU PENDIDIKAN - SMP
                |--------------------------------------------------------------------------
                */

                'SMP' => [
                    ['Matematika Terpadu SMP Kelas VII', 'Marsigit', 'Quadra', 2022, '510 MAR m'],
                    ['Matematika Terpadu SMP Kelas VIII', 'Marsigit', 'Quadra', 2023, '510 MAR m'],
                    ['Matematika Terpadu SMP Kelas IX', 'Marsigit', 'Quadra', 2024, '510 MAR m'],
                    ['Matematika Kontekstual SMP VII', 'Marsigit', 'Quadra', 2022, '510 MAR m'],
                    ['Matematika Kontekstual SMP VIII', 'Marsigit', 'Quadra', 2023, '510 MAR m'],
                    ['Matematika Kontekstual SMP IX', 'Marsigit', 'Quadra', 2024, '510 MAR m'],

                    ['IPA Terpadu SMP Kelas VII', 'Supriyadi, M.Si.', 'Erlangga', 2022, '500 SUP i'],
                    ['IPA Terpadu SMP Kelas VIII', 'Supriyadi, M.Si.', 'Erlangga', 2023, '500 SUP i'],
                    ['IPA Terpadu SMP Kelas IX', 'Supriyadi, M.Si.', 'Erlangga', 2024, '500 SUP i'],
                    ['Eksperimen IPA SMP', 'Supriyadi, M.Si.', 'Erlangga', 2022, '500 SUP e'],
                    ['Laboratorium IPA SMP', 'Supriyadi, M.Si.', 'Erlangga', 2023, '500 SUP l'],
                    ['Sains Terapan SMP', 'Supriyadi, M.Si.', 'Erlangga', 2024, '500 SUP s'],

                    ['Bahasa Indonesia SMP Kelas VII', 'Dewi Lestari, S.Pd.', 'Intan Pariwara', 2022, '410 DEW b'],
                    ['Bahasa Indonesia SMP Kelas VIII', 'Dewi Lestari, S.Pd.', 'Intan Pariwara', 2023, '410 DEW b'],
                    ['Bahasa Indonesia SMP Kelas IX', 'Dewi Lestari, S.Pd.', 'Intan Pariwara', 2024, '410 DEW b'],
                    ['Menulis Kreatif SMP', 'Dewi Lestari, S.Pd.', 'Intan Pariwara', 2022, '410 DEW m'],
                    ['Membaca Kritis SMP', 'Dewi Lestari, S.Pd.', 'Intan Pariwara', 2023, '410 DEW m'],
                    ['Literasi Bahasa Indonesia SMP', 'Dewi Lestari, S.Pd.', 'Intan Pariwara', 2024, '410 DEW l'],

                    ['Bahasa Inggris SMP Kelas VII', 'Rina Kurnia, M.Pd.', 'Yudhistira', 2022, '420 RIN b'],
                    ['Bahasa Inggris SMP Kelas VIII', 'Rina Kurnia, M.Pd.', 'Yudhistira', 2023, '420 RIN b'],
                    ['Bahasa Inggris SMP Kelas IX', 'Rina Kurnia, M.Pd.', 'Yudhistira', 2024, '420 RIN b'],
                    ['English Conversation SMP', 'Rina Kurnia, M.Pd.', 'Yudhistira', 2022, '420 RIN e'],
                    ['English Grammar SMP', 'Rina Kurnia, M.Pd.', 'Yudhistira', 2023, '420 RIN e'],
                    ['English Reading SMP', 'Rina Kurnia, M.Pd.', 'Yudhistira', 2024, '420 RIN e'],

                    ['IPS Terpadu SMP Kelas VII', 'Agus Setiawan, M.Pd.', 'Grafindo', 2022, '300 AGU i'],
                    ['IPS Terpadu SMP Kelas VIII', 'Agus Setiawan, M.Pd.', 'Grafindo', 2023, '300 AGU i'],
                    ['IPS Terpadu SMP Kelas IX', 'Agus Setiawan, M.Pd.', 'Grafindo', 2024, '300 AGU i'],
                    ['Geografi Dasar SMP', 'Agus Setiawan, M.Pd.', 'Grafindo', 2022, '910 AGU g'],
                    ['Sejarah Indonesia SMP', 'Agus Setiawan, M.Pd.', 'Grafindo', 2023, '959 AGU s'],
                    ['Ekonomi Dasar SMP', 'Agus Setiawan, M.Pd.', 'Grafindo', 2024, '330 AGU e'],

                    ['Pendidikan Pancasila SMP VII', 'Dimas Pratama, M.Pd.', 'Bumi Aksara', 2022, '323 DIM p'],
                    ['Pendidikan Pancasila SMP VIII', 'Dimas Pratama, M.Pd.', 'Bumi Aksara', 2023, '323 DIM p'],
                    ['Pendidikan Pancasila SMP IX', 'Dimas Pratama, M.Pd.', 'Bumi Aksara', 2024, '323 DIM p'],
                    ['Kewarganegaraan SMP', 'Dimas Pratama, M.Pd.', 'Bumi Aksara', 2022, '323 DIM k'],
                    ['Demokrasi dan Pelajar', 'Dimas Pratama, M.Pd.', 'Bumi Aksara', 2023, '323 DIM d'],
                    ['Pendidikan Karakter SMP', 'Dimas Pratama, M.Pd.', 'Bumi Aksara', 2024, '370 DIM p'],

                    ['Informatika SMP Kelas VII', 'Rizky Maulana, S.Kom.', 'Informatika', 2022, '004 RIZ i'],
                    ['Informatika SMP Kelas VIII', 'Rizky Maulana, S.Kom.', 'Informatika', 2023, '004 RIZ i'],
                    ['Informatika SMP Kelas IX', 'Rizky Maulana, S.Kom.', 'Informatika', 2024, '004 RIZ i'],
                    ['Pemrograman Dasar SMP', 'Rizky Maulana, S.Kom.', 'Informatika', 2022, '005 RIZ p'],
                    ['Literasi Digital SMP', 'Rizky Maulana, S.Kom.', 'Informatika', 2023, '302 RIZ l'],
                    ['Keamanan Digital untuk Pelajar', 'Rizky Maulana, S.Kom.', 'Informatika', 2024, '005 RIZ k'],

                    ['Seni Budaya SMP VII', 'Nadia Putri, M.Pd.', 'Erlangga', 2022, '700 NAD s'],
                    ['Seni Budaya SMP VIII', 'Nadia Putri, M.Pd.', 'Erlangga', 2023, '700 NAD s'],
                    ['Seni Budaya SMP IX', 'Nadia Putri, M.Pd.', 'Erlangga', 2024, '700 NAD s'],
                    ['Apresiasi Seni untuk Pelajar', 'Nadia Putri, M.Pd.', 'Erlangga', 2022, '700 NAD a'],
                    ['Kesenian Nusantara SMP', 'Nadia Putri, M.Pd.', 'Erlangga', 2023, '700 NAD k'],
                    ['Musik dan Seni Pertunjukan', 'Nadia Putri, M.Pd.', 'Erlangga', 2024, '780 NAD m'],

                    ['Pendidikan Jasmani SMP VII', 'Fajar Hidayat, S.Pd.', 'Yudhistira', 2022, '796 FAJ p'],
                    ['Pendidikan Jasmani SMP VIII', 'Fajar Hidayat, S.Pd.', 'Yudhistira', 2023, '796 FAJ p'],
                    ['Pendidikan Jasmani SMP IX', 'Fajar Hidayat, S.Pd.', 'Yudhistira', 2024, '796 FAJ p'],
                    ['Olahraga dan Kesehatan Remaja', 'Fajar Hidayat, S.Pd.', 'Yudhistira', 2022, '796 FAJ o'],
                    ['Dasar Permainan Bola', 'Fajar Hidayat, S.Pd.', 'Yudhistira', 2023, '796 FAJ d'],
                    ['Kebugaran Jasmani Pelajar', 'Fajar Hidayat, S.Pd.', 'Yudhistira', 2024, '796 FAJ k'],
                ],

                /*
                |--------------------------------------------------------------------------
                | BUKU PENDIDIKAN - SMA
                |--------------------------------------------------------------------------
                */

                'SMA' => [
                    ['Matematika Wajib SMA Kelas X', 'Andi Pratama, M.Pd.', 'Grafindo', 2022, '510 AND m'],
                    ['Matematika Wajib SMA Kelas XI', 'Andi Pratama, M.Pd.', 'Grafindo', 2023, '510 AND m'],
                    ['Matematika Wajib SMA Kelas XII', 'Andi Pratama, M.Pd.', 'Grafindo', 2024, '510 AND m'],
                    ['Matematika Peminatan SMA', 'Andi Pratama, M.Pd.', 'Grafindo', 2022, '510 AND m'],
                    ['Matematika dan Logika', 'Andi Pratama, M.Pd.', 'Grafindo', 2023, '510 AND m'],
                    ['Persiapan Matematika UTBK', 'Andi Pratama, M.Pd.', 'Grafindo', 2024, '510 AND p'],

                    ['Fisika Dasar SMA Kelas X', 'Rina Kurnia, M.Si.', 'Erlangga', 2022, '530 RIN f'],
                    ['Fisika Dasar SMA Kelas XI', 'Rina Kurnia, M.Si.', 'Erlangga', 2023, '530 RIN f'],
                    ['Fisika Dasar SMA Kelas XII', 'Rina Kurnia, M.Si.', 'Erlangga', 2024, '530 RIN f'],
                    ['Fisika dan Eksperimen', 'Rina Kurnia, M.Si.', 'Erlangga', 2022, '530 RIN f'],
                    ['Fisika Mekanika SMA', 'Rina Kurnia, M.Si.', 'Erlangga', 2023, '531 RIN f'],
                    ['Fisika Modern untuk Pelajar', 'Rina Kurnia, M.Si.', 'Erlangga', 2024, '539 RIN f'],

                    ['Bahasa Indonesia SMA Kelas X', 'Nadia Putri, M.Pd.', 'Yrama Widya', 2022, '410 NAD b'],
                    ['Bahasa Indonesia SMA Kelas XI', 'Nadia Putri, M.Pd.', 'Yrama Widya', 2023, '410 NAD b'],
                    ['Bahasa Indonesia SMA Kelas XII', 'Nadia Putri, M.Pd.', 'Yrama Widya', 2024, '410 NAD b'],
                    ['Menulis Esai untuk Pelajar', 'Nadia Putri, M.Pd.', 'Yrama Widya', 2022, '410 NAD m'],
                    ['Sastra Indonesia SMA', 'Nadia Putri, M.Pd.', 'Yrama Widya', 2023, '810 NAD s'],
                    ['Persiapan Literasi Bahasa', 'Nadia Putri, M.Pd.', 'Yrama Widya', 2024, '410 NAD p'],

                    ['Kimia Dasar SMA Kelas X', 'Bambang Suryadi, M.Si.', 'Intan Pariwara', 2022, '540 BAM k'],
                    ['Kimia Dasar SMA Kelas XI', 'Bambang Suryadi, M.Si.', 'Intan Pariwara', 2023, '540 BAM k'],
                    ['Kimia Dasar SMA Kelas XII', 'Bambang Suryadi, M.Si.', 'Intan Pariwara', 2024, '540 BAM k'],
                    ['Eksperimen Kimia SMA', 'Bambang Suryadi, M.Si.', 'Intan Pariwara', 2022, '540 BAM e'],
                    ['Kimia Organik Dasar', 'Bambang Suryadi, M.Si.', 'Intan Pariwara', 2023, '547 BAM k'],
                    ['Kimia dalam Kehidupan', 'Bambang Suryadi, M.Si.', 'Intan Pariwara', 2024, '540 BAM k'],

                    ['Biologi SMA Kelas X', 'Citra Lestari, M.Si.', 'Erlangga', 2022, '570 CIT b'],
                    ['Biologi SMA Kelas XI', 'Citra Lestari, M.Si.', 'Erlangga', 2023, '570 CIT b'],
                    ['Biologi SMA Kelas XII', 'Citra Lestari, M.Si.', 'Erlangga', 2024, '570 CIT b'],
                    ['Ekologi untuk Pelajar', 'Citra Lestari, M.Si.', 'Erlangga', 2022, '577 CIT e'],
                    ['Genetika Dasar SMA', 'Citra Lestari, M.Si.', 'Erlangga', 2023, '576 CIT g'],
                    ['Biologi dan Lingkungan', 'Citra Lestari, M.Si.', 'Erlangga', 2024, '570 CIT b'],

                    ['Sejarah Indonesia SMA X', 'Agus Setiawan, M.Pd.', 'Yudhistira', 2022, '959 AGU s'],
                    ['Sejarah Indonesia SMA XI', 'Agus Setiawan, M.Pd.', 'Yudhistira', 2023, '959 AGU s'],
                    ['Sejarah Indonesia SMA XII', 'Agus Setiawan, M.Pd.', 'Yudhistira', 2024, '959 AGU s'],
                    ['Sejarah Dunia untuk Pelajar', 'Agus Setiawan, M.Pd.', 'Yudhistira', 2022, '909 AGU s'],
                    ['Sejarah Pergerakan Nasional', 'Agus Setiawan, M.Pd.', 'Yudhistira', 2023, '959 AGU s'],
                    ['Sejarah Kota-Kota Indonesia', 'Agus Setiawan, M.Pd.', 'Yudhistira', 2024, '959 AGU s'],

                    ['Ekonomi SMA Kelas X', 'Hendra Wijaya, M.E.', 'Salemba Empat', 2022, '330 HEN e'],
                    ['Ekonomi SMA Kelas XI', 'Hendra Wijaya, M.E.', 'Salemba Empat', 2023, '330 HEN e'],
                    ['Ekonomi SMA Kelas XII', 'Hendra Wijaya, M.E.', 'Salemba Empat', 2024, '330 HEN e'],
                    ['Ekonomi Mikro Dasar', 'Hendra Wijaya, M.E.', 'Salemba Empat', 2022, '338 HEN e'],
                    ['Ekonomi Makro Dasar', 'Hendra Wijaya, M.E.', 'Salemba Empat', 2023, '339 HEN e'],
                    ['Kewirausahaan untuk Pelajar', 'Hendra Wijaya, M.E.', 'Salemba Empat', 2024, '338 HEN k'],

                    ['Informatika SMA Kelas X', 'Rizky Maulana, S.Kom.', 'Informatika', 2022, '004 RIZ i'],
                    ['Informatika SMA Kelas XI', 'Rizky Maulana, S.Kom.', 'Informatika', 2023, '004 RIZ i'],
                    ['Informatika SMA Kelas XII', 'Rizky Maulana, S.Kom.', 'Informatika', 2024, '004 RIZ i'],
                    ['Algoritma dan Pemrograman', 'Rizky Maulana, S.Kom.', 'Informatika', 2022, '005 RIZ a'],
                    ['Basis Data untuk Pelajar', 'Rizky Maulana, S.Kom.', 'Informatika', 2023, '005 RIZ b'],
                    ['Jaringan Komputer Dasar', 'Rizky Maulana, S.Kom.', 'Informatika', 2024, '004 RIZ j'],

                    ['Geografi SMA Kelas X', 'Fajar Hidayat, M.Pd.', 'Grafindo', 2022, '910 FAJ g'],
                    ['Geografi SMA Kelas XI', 'Fajar Hidayat, M.Pd.', 'Grafindo', 2023, '910 FAJ g'],
                    ['Geografi SMA Kelas XII', 'Fajar Hidayat, M.Pd.', 'Grafindo', 2024, '910 FAJ g'],
                    ['Geografi Indonesia', 'Fajar Hidayat, M.Pd.', 'Grafindo', 2022, '915 FAJ g'],
                    ['Geografi Lingkungan', 'Fajar Hidayat, M.Pd.', 'Grafindo', 2023, '910 FAJ g'],
                    ['Peta dan Analisis Wilayah', 'Fajar Hidayat, M.Pd.', 'Grafindo', 2024, '912 FAJ p'],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | ANAK
            |--------------------------------------------------------------------------
            */

            'Anak' => [
                'Fiksi' => [
                    ['Petualangan di Hutan Ajaib', 'Lala Permata', 'Mizan', 2022, '813 LAL p'],
                    ['Rahasia Rumah Pohon', 'Dina Maharani', 'Gramedia Pustaka Utama', 2023, '813 DIN r'],
                    ['Kapal Kertas di Sungai Biru', 'Raka Aditya', 'Noura', 2021, '813 RAK k'],
                    ['Nara dan Kucing Ajaib', 'Maya Putri', 'Mizan', 2022, '813 MAY n'],
                    ['Petualangan di Pulau Pelangi', 'Raka Aditya', 'Noura', 2023, '813 RAK p'],
                    ['Pangeran Kecil dari Desa Awan', 'Dina Maharani', 'Gramedia Pustaka Utama', 2024, '813 DIN p'],
                    ['Misteri Jam Tua', 'Lala Permata', 'Mizan', 2022, '813 LAL m'],
                    ['Kelinci yang Ingin Terbang', 'Maya Putri', 'Noura', 2023, '813 MAY k'],
                    ['Rahasia Kebun Belakang', 'Raka Aditya', 'Noura', 2024, '813 RAK r'],
                    ['Petualangan di Negeri Buku', 'Dina Maharani', 'Gramedia Pustaka Utama', 2022, '813 DIN p'],
                    ['Sepeda Ajaib Bimo', 'Lala Permata', 'Mizan', 2023, '813 LAL s'],
                    ['Luna dan Bintang Jatuh', 'Maya Putri', 'Noura', 2024, '813 MAY l'],
                    ['Kota Kecil di Atas Awan', 'Raka Aditya', 'Noura', 2022, '813 RAK k'],
                    ['Harta Karun di Pantai Selatan', 'Dina Maharani', 'Gramedia Pustaka Utama', 2023, '813 DIN h'],
                    ['Kisah Persahabatan Tiga Sahabat', 'Lala Permata', 'Mizan', 2024, '813 LAL k'],
                    ['Misteri Rumah Kosong', 'Maya Putri', 'Noura', 2022, '813 MAY m'],
                    ['Dino dan Dunia Prasejarah', 'Raka Aditya', 'Noura', 2023, '813 RAK d'],
                    ['Festival Lampion Desa', 'Dina Maharani', 'Gramedia Pustaka Utama', 2024, '813 DIN f'],
                    ['Kisah Kupu-Kupu Biru', 'Lala Permata', 'Mizan', 2022, '813 LAL k'],
                    ['Rahasia Danau Biru', 'Maya Putri', 'Noura', 2023, '813 MAY r'],
                    ['Tono dan Peta Rahasia', 'Raka Aditya', 'Noura', 2024, '813 RAK t'],
                    ['Kereta Api Tengah Malam', 'Dina Maharani', 'Gramedia Pustaka Utama', 2022, '813 DIN k'],
                    ['Bintang Kecil Penjaga Hutan', 'Lala Permata', 'Mizan', 2023, '813 LAL b'],
                    ['Petualangan di Kota Mainan', 'Maya Putri', 'Noura', 2024, '813 MAY p'],
                    ['Kado Misterius untuk Rani', 'Raka Aditya', 'Noura', 2023, '813 RAK k'],
                ],

                'Non Fiksi' => [
                    ['Ensiklopedia Hewan untuk Anak', 'Tim Edukasi Nusantara', 'Bhuana Ilmu Populer', 2022, '590 TIM e'],
                    ['Aku Belajar Sains', 'Maya Sari', 'Elex Media Komputindo', 2023, '500 MAY a'],
                    ['Atlas Dunia Anak', 'Tim Pustaka Cerdas', 'Tiga Serangkai', 2024, '912 TIM a'],
                    ['Mengenal Planet Tata Surya', 'Tim Edukasi Nusantara', 'Bhuana Ilmu Populer', 2022, '523 TIM m'],
                    ['Ensiklopedia Serangga', 'Maya Sari', 'Elex Media Komputindo', 2023, '595 MAY e'],
                    ['Dunia Laut untuk Anak', 'Tim Pustaka Cerdas', 'Tiga Serangkai', 2024, '551 TIM d'],
                    ['Mengenal Tubuh Manusia', 'Tim Edukasi Nusantara', 'Bhuana Ilmu Populer', 2022, '612 TIM m'],
                    ['Atlas Hewan Indonesia', 'Maya Sari', 'Elex Media Komputindo', 2023, '590 MAY a'],
                    ['Ensiklopedia Tumbuhan', 'Tim Pustaka Cerdas', 'Tiga Serangkai', 2024, '580 TIM e'],
                    ['Belajar tentang Cuaca', 'Tim Edukasi Nusantara', 'Bhuana Ilmu Populer', 2022, '551 TIM b'],
                    ['Mengenal Profesi', 'Maya Sari', 'Elex Media Komputindo', 2023, '331 MAY m'],
                    ['Ensiklopedia Kendaraan', 'Tim Pustaka Cerdas', 'Tiga Serangkai', 2024, '629 TIM e'],
                    ['Belajar Geografi untuk Anak', 'Tim Edukasi Nusantara', 'Bhuana Ilmu Populer', 2022, '910 TIM b'],
                    ['Mengenal Budaya Indonesia', 'Maya Sari', 'Elex Media Komputindo', 2023, '306 MAY m'],
                    ['Ensiklopedia Sejarah Dunia', 'Tim Pustaka Cerdas', 'Tiga Serangkai', 2024, '909 TIM e'],
                    ['Belajar Energi dan Lingkungan', 'Tim Edukasi Nusantara', 'Bhuana Ilmu Populer', 2022, '333 TIM b'],
                    ['Mengenal Laut dan Pantai', 'Maya Sari', 'Elex Media Komputindo', 2023, '551 MAY m'],
                    ['Ensiklopedia Dinosaurus', 'Tim Pustaka Cerdas', 'Tiga Serangkai', 2024, '567 TIM e'],
                    ['Aku Belajar Astronomi', 'Tim Edukasi Nusantara', 'Bhuana Ilmu Populer', 2022, '520 TIM a'],
                    ['Mengenal Dunia Serangga', 'Maya Sari', 'Elex Media Komputindo', 2023, '595 MAY m'],
                    ['Atlas Provinsi Indonesia', 'Tim Pustaka Cerdas', 'Tiga Serangkai', 2024, '912 TIM a'],
                    ['Belajar Hidup Sehat', 'Tim Edukasi Nusantara', 'Bhuana Ilmu Populer', 2022, '613 TIM b'],
                    ['Ensiklopedia Laut', 'Maya Sari', 'Elex Media Komputindo', 2023, '551 MAY e'],
                    ['Mengenal Teknologi', 'Tim Pustaka Cerdas', 'Tiga Serangkai', 2024, '600 TIM m'],
                    ['Ensiklopedia Pengetahuan Anak', 'Tim Edukasi Nusantara', 'Bhuana Ilmu Populer', 2023, '001 TIM e'],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | REMAJA
            |--------------------------------------------------------------------------
            */

            'Remaja' => [
                'Fiksi' => [
                    ['Langit Setelah Hujan', 'Nadya Prameswari', 'Gramedia Pustaka Utama', 2022, '813 NAD l'],
                    ['Jejak di Kota Senja', 'Fajar Ramadhan', 'Bentang Pustaka', 2023, '813 FAJ j'],
                    ['Surat yang Tak Pernah Terkirim', 'Alya Kirana', 'GagasMedia', 2021, '813 ALY s'],
                    ['Musim yang Berubah', 'Nadya Prameswari', 'Gramedia Pustaka Utama', 2023, '813 NAD m'],
                    ['Cerita dari Stasiun Lama', 'Fajar Ramadhan', 'Bentang Pustaka', 2022, '813 FAJ c'],
                    ['Kita dan Senja', 'Alya Kirana', 'GagasMedia', 2024, '813 ALY k'],
                    ['Rumah di Ujung Jalan', 'Nadya Prameswari', 'Gramedia Pustaka Utama', 2022, '813 NAD r'],
                    ['Perjalanan Menuju Utara', 'Fajar Ramadhan', 'Bentang Pustaka', 2023, '813 FAJ p'],
                    ['Pesan di Balik Foto', 'Alya Kirana', 'GagasMedia', 2024, '813 ALY p'],
                    ['Langkah Pertama', 'Nadya Prameswari', 'Gramedia Pustaka Utama', 2022, '813 NAD l'],
                    ['Hari-Hari di Kota Baru', 'Fajar Ramadhan', 'Bentang Pustaka', 2023, '813 FAJ h'],
                    ['Mimpi di Balik Jendela', 'Alya Kirana', 'GagasMedia', 2024, '813 ALY m'],
                    ['Sahabat di Musim Hujan', 'Nadya Prameswari', 'Gramedia Pustaka Utama', 2022, '813 NAD s'],
                    ['Kisah Kita di Perpustakaan', 'Fajar Ramadhan', 'Bentang Pustaka', 2023, '813 FAJ k'],
                    ['Rahasia Surat Biru', 'Alya Kirana', 'GagasMedia', 2024, '813 ALY r'],
                    ['Pulang Setelah Lama Pergi', 'Nadya Prameswari', 'Gramedia Pustaka Utama', 2022, '813 NAD p'],
                    ['Kota dan Kenangan', 'Fajar Ramadhan', 'Bentang Pustaka', 2023, '813 FAJ k'],
                    ['Buku Harian Seorang Pelajar', 'Alya Kirana', 'GagasMedia', 2024, '813 ALY b'],
                    ['Hujan di Bulan Desember', 'Nadya Prameswari', 'Gramedia Pustaka Utama', 2022, '813 NAD h'],
                    ['Perjalanan Tanpa Peta', 'Fajar Ramadhan', 'Bentang Pustaka', 2023, '813 FAJ p'],
                    ['Malam yang Berbeda', 'Alya Kirana', 'GagasMedia', 2024, '813 ALY m'],
                    ['Satu Tahun Bersama', 'Nadya Prameswari', 'Gramedia Pustaka Utama', 2022, '813 NAD s'],
                    ['Jejak Sepatu di Jalan Basah', 'Fajar Ramadhan', 'Bentang Pustaka', 2023, '813 FAJ j'],
                    ['Cerita yang Belum Selesai', 'Alya Kirana', 'GagasMedia', 2024, '813 ALY c'],
                    ['Tempat Kita Bertemu', 'Nadya Prameswari', 'Gramedia Pustaka Utama', 2023, '813 NAD t'],
                ],

                'Non Fiksi' => [
                    ['Panduan Belajar Efektif untuk Remaja', 'Dimas Pratama', 'Erlangga', 2023, '371 DIM p'],
                    ['Psikologi Remaja dan Perkembangan Diri', 'Citra Lestari', 'Prenada', 2022, '155 CIT p'],
                    ['Literasi Digital untuk Pelajar', 'Tim Literasi Indonesia', 'Andi Publisher', 2024, '302 TIM l'],
                    ['Manajemen Waktu untuk Pelajar', 'Dimas Pratama', 'Erlangga', 2022, '371 DIM m'],
                    ['Cara Belajar yang Menyenangkan', 'Citra Lestari', 'Prenada', 2023, '371 CIT c'],
                    ['Etika Digital untuk Remaja', 'Tim Literasi Indonesia', 'Andi Publisher', 2024, '302 TIM e'],
                    ['Mengenal Potensi Diri', 'Dimas Pratama', 'Erlangga', 2022, '155 DIM m'],
                    ['Komunikasi Efektif bagi Pelajar', 'Citra Lestari', 'Prenada', 2023, '302 CIT k'],
                    ['Media Sosial dan Remaja', 'Tim Literasi Indonesia', 'Andi Publisher', 2024, '302 TIM m'],
                    ['Persiapan Kuliah untuk Pelajar', 'Dimas Pratama', 'Erlangga', 2022, '378 DIM p'],
                    ['Keterampilan Presentasi', 'Citra Lestari', 'Prenada', 2023, '371 CIT k'],
                    ['Keamanan Internet untuk Remaja', 'Tim Literasi Indonesia', 'Andi Publisher', 2024, '005 TIM k'],
                    ['Belajar Mandiri di Era Digital', 'Dimas Pratama', 'Erlangga', 2022, '371 DIM b'],
                    ['Psikologi Belajar Remaja', 'Citra Lestari', 'Prenada', 2023, '370 CIT p'],
                    ['Mengenal Dunia Kerja', 'Tim Literasi Indonesia', 'Andi Publisher', 2024, '331 TIM m'],
                    ['Kreativitas dan Inovasi Pelajar', 'Dimas Pratama', 'Erlangga', 2022, '370 DIM k'],
                    ['Kesehatan Mental Remaja', 'Citra Lestari', 'Prenada', 2023, '155 CIT k'],
                    ['Teknologi dan Masa Depan', 'Tim Literasi Indonesia', 'Andi Publisher', 2024, '600 TIM t'],
                    ['Cara Membuat Target Belajar', 'Dimas Pratama', 'Erlangga', 2022, '371 DIM c'],
                    ['Kepemimpinan bagi Pelajar', 'Citra Lestari', 'Prenada', 2023, '303 CIT k'],
                    ['Literasi Informasi Remaja', 'Tim Literasi Indonesia', 'Andi Publisher', 2024, '302 TIM l'],
                    ['Berpikir Kritis untuk Pelajar', 'Dimas Pratama', 'Erlangga', 2022, '160 DIM b'],
                    ['Pendidikan Karakter Remaja', 'Citra Lestari', 'Prenada', 2023, '370 CIT p'],
                    ['Mengenal Profesi Masa Depan', 'Tim Literasi Indonesia', 'Andi Publisher', 2024, '331 TIM m'],
                    ['Panduan Organisasi Pelajar', 'Dimas Pratama', 'Erlangga', 2023, '371 DIM p'],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | DEWASA
            |--------------------------------------------------------------------------
            */

            'Dewasa' => [
                'Fiksi' => [
                    ['Pulang ke Kota Lama', 'Ayu Utami', 'Kepustakaan Populer Gramedia', 2022, '813 AYU p'],
                    ['Perjalanan Musim Hujan', 'Bambang Suryadi', 'Bentang Pustaka', 2021, '813 BAM p'],
                    ['Malam di Stasiun Tua', 'Seno Gumira Ajidarma', 'Gramedia Pustaka Utama', 2023, '813 SEN m'],
                    ['Kota yang Selalu Dirindukan', 'Ayu Utami', 'KPG', 2022, '813 AYU k'],
                    ['Jalan Pulang', 'Bambang Suryadi', 'Bentang Pustaka', 2023, '813 BAM j'],
                    ['Cerita dari Selatan', 'Seno Gumira Ajidarma', 'Gramedia Pustaka Utama', 2024, '813 SEN c'],
                    ['Rumah dan Kenangan', 'Ayu Utami', 'KPG', 2022, '813 AYU r'],
                    ['Musim yang Berubah', 'Bambang Suryadi', 'Bentang Pustaka', 2023, '813 BAM m'],
                    ['Malam Panjang di Kota Tua', 'Seno Gumira Ajidarma', 'Gramedia Pustaka Utama', 2024, '813 SEN m'],
                    ['Jejak Perjalanan', 'Ayu Utami', 'KPG', 2022, '813 AYU j'],
                    ['Hujan di Ujung Jalan', 'Bambang Suryadi', 'Bentang Pustaka', 2023, '813 BAM h'],
                    ['Kisah dari Sebuah Kota', 'Seno Gumira Ajidarma', 'Gramedia Pustaka Utama', 2024, '813 SEN k'],
                    ['Pagi Setelah Hujan', 'Ayu Utami', 'KPG', 2022, '813 AYU p'],
                    ['Surat dari Masa Lalu', 'Bambang Suryadi', 'Bentang Pustaka', 2023, '813 BAM s'],
                    ['Misteri Rumah Tua', 'Seno Gumira Ajidarma', 'Gramedia Pustaka Utama', 2024, '813 SEN m'],
                    ['Perjalanan Tanpa Akhir', 'Ayu Utami', 'KPG', 2022, '813 AYU p'],
                    ['Kota dan Waktu', 'Bambang Suryadi', 'Bentang Pustaka', 2023, '813 BAM k'],
                    ['Langit di Atas Kota', 'Seno Gumira Ajidarma', 'Gramedia Pustaka Utama', 2024, '813 SEN l'],
                    ['Hari-Hari yang Hilang', 'Ayu Utami', 'KPG', 2022, '813 AYU h'],
                    ['Jendela Kota', 'Bambang Suryadi', 'Bentang Pustaka', 2023, '813 BAM j'],
                    ['Catatan Tengah Malam', 'Seno Gumira Ajidarma', 'Gramedia Pustaka Utama', 2024, '813 SEN c'],
                    ['Musim di Kota Lama', 'Ayu Utami', 'KPG', 2022, '813 AYU m'],
                    ['Pulang Saat Senja', 'Bambang Suryadi', 'Bentang Pustaka', 2023, '813 BAM p'],
                    ['Kisah yang Tertinggal', 'Seno Gumira Ajidarma', 'Gramedia Pustaka Utama', 2024, '813 SEN k'],
                    ['Akhir Sebuah Perjalanan', 'Ayu Utami', 'KPG', 2023, '813 AYU a'],
                ],

                'Non Fiksi' => [
                    ['Pengantar Manajemen Modern', 'Hendra Wijaya', 'Salemba Empat', 2023, '658 HEN p'],
                    ['Dasar-Dasar Teknologi Informasi', 'Rizky Maulana', 'Informatika', 2024, '004 RIZ d'],
                    ['Sejarah Kota dan Masyarakat Indonesia', 'Nadia Kusuma', 'Kompas', 2022, '959 NAD s'],
                    ['Manajemen Organisasi Modern', 'Hendra Wijaya', 'Salemba Empat', 2022, '658 HEN m'],
                    ['Pemrograman Web Dasar', 'Rizky Maulana', 'Informatika', 2023, '005 RIZ p'],
                    ['Sejarah Perkotaan Indonesia', 'Nadia Kusuma', 'Kompas', 2024, '959 NAD s'],
                    ['Manajemen Sumber Daya Manusia', 'Hendra Wijaya', 'Salemba Empat', 2023, '658 HEN m'],
                    ['Basis Data dan Sistem Informasi', 'Rizky Maulana', 'Informatika', 2024, '005 RIZ b'],
                    ['Sejarah Indonesia Modern', 'Nadia Kusuma', 'Kompas', 2022, '959 NAD s'],
                    ['Strategi Bisnis dan Manajemen', 'Hendra Wijaya', 'Salemba Empat', 2023, '658 HEN s'],
                    ['Jaringan Komputer Praktis', 'Rizky Maulana', 'Informatika', 2024, '004 RIZ j'],
                    ['Masyarakat dan Perubahan Sosial', 'Nadia Kusuma', 'Kompas', 2022, '303 NAD m'],
                    ['Pengantar Ekonomi Bisnis', 'Hendra Wijaya', 'Salemba Empat', 2023, '330 HEN p'],
                    ['Keamanan Siber Dasar', 'Rizky Maulana', 'Informatika', 2024, '005 RIZ k'],
                    ['Sejarah Kebudayaan Indonesia', 'Nadia Kusuma', 'Kompas', 2022, '959 NAD s'],
                    ['Kepemimpinan Organisasi', 'Hendra Wijaya', 'Salemba Empat', 2023, '658 HEN k'],
                    ['Kecerdasan Buatan untuk Pemula', 'Rizky Maulana', 'Informatika', 2024, '006 RIZ k'],
                    ['Kota dan Masyarakat Modern', 'Nadia Kusuma', 'Kompas', 2022, '307 NAD k'],
                    ['Pengantar Akuntansi', 'Hendra Wijaya', 'Salemba Empat', 2023, '657 HEN p'],
                    ['Algoritma dan Struktur Data', 'Rizky Maulana', 'Informatika', 2024, '005 RIZ a'],
                    ['Sejarah Politik Indonesia', 'Nadia Kusuma', 'Kompas', 2022, '959 NAD s'],
                    ['Perencanaan Bisnis', 'Hendra Wijaya', 'Salemba Empat', 2023, '658 HEN p'],
                    ['Pengembangan Aplikasi Modern', 'Rizky Maulana', 'Informatika', 2024, '005 RIZ p'],
                    ['Sejarah dan Identitas Nusantara', 'Nadia Kusuma', 'Kompas', 2022, '959 NAD s'],
                    ['Dasar-Dasar Kewirausahaan', 'Hendra Wijaya', 'Salemba Empat', 2023, '338 HEN d'],
                ],
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | RAK BUKU
        |--------------------------------------------------------------------------
        */

        $rackByCategory = [
            'Buku Pendidikan' => 'B2',
            'Anak' => 'A1',
            'Remaja' => 'A2',
            'Dewasa' => 'C1',
        ];

        /*
        |--------------------------------------------------------------------------
        | BATAS JUMLAH BUKU PER SUBKATEGORI
        |--------------------------------------------------------------------------
        |
        | Total = 170 judul.
        |
        | Buku Pendidikan:
        |   SD         = 20
        |   SMP        = 20
        |   SMA        = 20
        |
        | Anak:
        |   Fiksi      = 20
        |   Non Fiksi  = 20
        |
        | Remaja:
        |   Fiksi      = 20
        |   Non Fiksi  = 20
        |
        | Dewasa:
        |   Fiksi      = 15
        |   Non Fiksi  = 15
        |
        */

        $bookLimitBySubcategory = [
            'Buku Pendidikan' => [
                'SD' => 20,
                'SMP' => 20,
                'SMA' => 20,
            ],

            'Anak' => [
                'Fiksi' => 20,
                'Non Fiksi' => 20,
            ],

            'Remaja' => [
                'Fiksi' => 20,
                'Non Fiksi' => 20,
            ],

            'Dewasa' => [
                'Fiksi' => 15,
                'Non Fiksi' => 15,
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | INSERT / UPDATE BOOKS
        |--------------------------------------------------------------------------
        */

        $counter = 1;

        foreach ($definitions as $categoryName => $subcategories) {
            $category = Category::where('name', $categoryName)->firstOrFail();

            foreach ($subcategories as $subcategoryName => $books) {
                $subcategory = Subcategory::where('category_id', $category->id)
                    ->where('name', $subcategoryName)
                    ->firstOrFail();

                $limit = $bookLimitBySubcategory[$categoryName][$subcategoryName] ?? 0;

                foreach (array_slice($books, 0, $limit) as $book) {
                    [$title, $author, $publisher, $year, $callNumber] = $book;

                    $isbn = $this->demoIsbn($counter);

                    $sku = 'BK-' . str_pad(
                        $counter,
                        5,
                        '0',
                        STR_PAD_LEFT
                    );

                    Book::updateOrCreate(
                        ['sku' => $sku],
                        [
                            'judul_buku' => $title,
                            'penulis' => $author,
                            'isbn' => $isbn,
                            'publisher' => $publisher,
                            'publication_year' => $year,
                            'edition' => 'Cetakan ke-1',
                            'call_number' => $callNumber,

                            // Mengambil bagian angka dari call number.
                            // Contoh: "372.7 BUD m" -> "372.7"
                            'ddc' => preg_replace('/\s.*$/', '', $callNumber),

                            'description' => "Deskripsi demo untuk {$title}.",
                            'cover' => null,

                            'category_id' => $category->id,
                            'subcategory_id' => $subcategory->id,

                            'main_category' => $categoryName,
                            'sub_category' => $subcategoryName,

                            'education_level' =>
                                $categoryName === 'Buku Pendidikan'
                                    ? $subcategoryName
                                    : null,

                            'stok' => 5,
                            'status' => 'Tersedia',

                            'no_iventaris' =>
                                'INV/' .
                                date('Y') .
                                '/' .
                                str_pad(
                                    $counter,
                                    5,
                                    '0',
                                    STR_PAD_LEFT
                                ),

                            'kode_buku' =>
                                'KB-' .
                                str_pad(
                                    $counter,
                                    5,
                                    '0',
                                    STR_PAD_LEFT
                                ),

                            'rak' => $rackByCategory[$categoryName],
                        ]
                    );

                    $counter++;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CEK JUMLAH DATA
        |--------------------------------------------------------------------------
        */

        $total = $counter - 1;

        $this->command->info(
            "BookSeeder selesai. Total buku diproses: {$total} judul."
        );

        $this->command->info(
            "Target: 170 judul yang mencakup seluruh kategori dan subkategori."
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DEMO ISBN
    |--------------------------------------------------------------------------
    */

    private function demoIsbn(int $number): string
    {
        $base =
            '97800000' .
            str_pad(
                (string) $number,
                4,
                '0',
                STR_PAD_LEFT
            );

        $sum = 0;

        for ($i = 0; $i < 12; $i++) {
            $sum +=
                ((int) $base[$i]) *
                ($i % 2 === 0 ? 1 : 3);
        }

        $check = (10 - ($sum % 10)) % 10;

        return $base . $check;
    }
}