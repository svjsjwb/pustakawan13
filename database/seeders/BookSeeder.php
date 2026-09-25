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
        $definitions = [
            'Buku Pendidikan' => [
                'SD' => [
                    ['Matematika Ceria Kelas 4 SD', 'Budi Santoso, M.Pd.', 'Erlangga Edukasi', 2022, '372.7 BUD m'],
                    ['IPAS untuk Sekolah Dasar', 'Siti Aminah, S.Pd.', 'Yudhistira', 2023, '372.3 SIT i'],
                    ['Bahasa Indonesia Pintar SD', 'Hendra Setiawan', 'Balai Pustaka', 2021, '372.6 HEN b'],
                ],
                'SMP' => [
                    ['Matematika Terpadu SMP Kelas VII', 'Marsigit', 'Quadra', 2022, '510 MAR m'],
                    ['IPA Terpadu SMP', 'Supriyadi, M.Si.', 'Erlangga', 2023, '500 SUP i'],
                    ['Bahasa Indonesia SMP Kelas VIII', 'Dewi Lestari, S.Pd.', 'Intan Pariwara', 2022, '410 DEW b'],
                ],
                'SMA' => [
                    ['Matematika Wajib SMA', 'Andi Pratama, M.Pd.', 'Grafindo', 2023, '510 AND m'],
                    ['Fisika Dasar SMA', 'Rina Kurnia, M.Si.', 'Erlangga', 2022, '530 RIN f'],
                    ['Bahasa Indonesia SMA', 'Nadia Putri, M.Pd.', 'Yrama Widya', 2024, '410 NAD b'],
                ],
            ],
            'Anak' => [
                'Fiksi' => [
                    ['Petualangan di Hutan Ajaib', 'Lala Permata', 'Mizan', 2022, '813 LAL p'],
                    ['Rahasia Rumah Pohon', 'Dina Maharani', 'Gramedia Pustaka Utama', 2023, '813 DIN r'],
                    ['Kapal Kertas di Sungai Biru', 'Raka Aditya', 'Noura', 2021, '813 RAK k'],
                ],
                'Non Fiksi' => [
                    ['Ensiklopedia Hewan untuk Anak', 'Tim Edukasi Nusantara', 'Bhuana Ilmu Populer', 2022, '590 TIM e'],
                    ['Aku Belajar Sains', 'Maya Sari', 'Elex Media Komputindo', 2023, '500 MAY a'],
                    ['Atlas Dunia Anak', 'Tim Pustaka Cerdas', 'Tiga Serangkai', 2024, '912 TIM a'],
                ],
            ],
            'Remaja' => [
                'Fiksi' => [
                    ['Langit Setelah Hujan', 'Nadya Prameswari', 'Gramedia Pustaka Utama', 2022, '813 NAD l'],
                    ['Jejak di Kota Senja', 'Fajar Ramadhan', 'Bentang Pustaka', 2023, '813 FAJ j'],
                    ['Surat yang Tak Pernah Terkirim', 'Alya Kirana', 'GagasMedia', 2021, '813 ALY s'],
                ],
                'Non Fiksi' => [
                    ['Panduan Belajar Efektif untuk Remaja', 'Dimas Pratama', 'Erlangga', 2023, '371 DIM p'],
                    ['Psikologi Remaja dan Perkembangan Diri', 'Citra Lestari', 'Prenada', 2022, '155 CIT p'],
                    ['Literasi Digital untuk Pelajar', 'Tim Literasi Indonesia', 'Andi Publisher', 2024, '302 TIM l'],
                ],
            ],
            'Dewasa' => [
                'Fiksi' => [
                    ['Pulang ke Kota Lama', 'Ayu Utami', 'Kepustakaan Populer Gramedia', 2022, '813 AYU p'],
                    ['Perjalanan Musim Hujan', 'Bambang Suryadi', 'Bentang Pustaka', 2021, '813 BAM p'],
                    ['Malam di Stasiun Tua', 'Seno Gumira Ajidarma', 'Gramedia Pustaka Utama', 2023, '813 SEN m'],
                ],
                'Non Fiksi' => [
                    ['Pengantar Manajemen Modern', 'Hendra Wijaya', 'Salemba Empat', 2023, '658 HEN p'],
                    ['Dasar-Dasar Teknologi Informasi', 'Rizky Maulana', 'Informatika', 2024, '004 RIZ d'],
                    ['Sejarah Kota dan Masyarakat Indonesia', 'Nadia Kusuma', 'Kompas', 2022, '959 NAD s'],
                ],
            ],
        ];

        $rackByCategory = [
            'Buku Pendidikan' => 'B2',
            'Anak' => 'A1',
            'Remaja' => 'A2',
            'Dewasa' => 'C1',
        ];

        $counter = 1;

        foreach ($definitions as $categoryName => $subcategories) {
            $category = Category::where('name', $categoryName)->firstOrFail();

            foreach ($subcategories as $subcategoryName => $books) {
                $subcategory = Subcategory::where('category_id', $category->id)
                    ->where('name', $subcategoryName)
                    ->firstOrFail();

                foreach ($books as $book) {
                    [$title, $author, $publisher, $year, $callNumber] = $book;
                    $isbn = $this->demoIsbn($counter);
                    $sku = 'BK-' . str_pad($counter, 5, '0', STR_PAD_LEFT);

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
                            'ddc' => preg_replace('/\s.*$/', '', $callNumber),
                            'description' => "Deskripsi demo untuk {$title}.",
                            'cover' => null,
                            'category_id' => $category->id,
                            'subcategory_id' => $subcategory->id,
                            'main_category' => $categoryName,
                            'sub_category' => $subcategoryName,
                            'education_level' => $categoryName === 'Buku Pendidikan' ? $subcategoryName : null,
                            'stok' => 5,
                            'status' => 'Tersedia',
                            'no_iventaris' => 'INV/' . date('Y') . '/' . str_pad($counter, 5, '0', STR_PAD_LEFT),
                            'kode_buku' => 'KB-' . str_pad($counter, 5, '0', STR_PAD_LEFT),
                            'rak' => $rackByCategory[$categoryName],
                        ]
                    );

                    $counter++;
                }
            }
        }
    }

    private function demoIsbn(int $number): string
    {
        $base = '97800000' . str_pad((string) $number, 4, '0', STR_PAD_LEFT);
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += ((int) $base[$i]) * ($i % 2 === 0 ? 1 : 3);
        }
        $check = (10 - ($sum % 10)) % 10;
        return $base . $check;
    }
}
