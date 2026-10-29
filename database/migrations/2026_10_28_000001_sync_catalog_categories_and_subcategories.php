<?php

use App\Models\Book;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $hierarchy = [
            'Buku Pelajaran' => [
                'TK',
                'SD/MI',
                'SMP/MTs',
                'SMA/SMK/MA/SMAK/MAK',
                'Referensi',
            ],
            'Buku Umum' => [
                'Agama',
                'Majalah',
                'Peta',
                'Referensi',
                'Buku Anak',
                'Buku Remaja',
                'Buku Dewasa',
                'Ilmu Sosial',
                'Ilmu Murni/Sains',
                'Bahasa/Sastra',
                'Ilmu Terapan',
            ],
        ];

        $subModelMap = [];

        foreach ($hierarchy as $mainName => $subNames) {
            $cat = Category::firstOrCreate(
                ['name' => $mainName],
                ['level' => 1]
            );

            foreach ($subNames as $subName) {
                $sub = Subcategory::firstOrCreate(
                    [
                        'category_id' => $cat->id,
                        'name' => $subName,
                    ]
                );

                $subModelMap[$mainName . '::' . $subName] = [
                    'category_id' => $cat->id,
                    'subcategory_id' => $sub->id,
                ];
            }
        }

        // Map existing books to their proper main category and subcategory
        foreach (Book::all() as $book) {
            $main = $book->main_category;
            $sub = $book->sub_category ?: $book->education_level;

            if ($main === 'Buku Pendidikan' || $main === 'Pendidikan') {
                $targetMain = 'Buku Pelajaran';
                $targetSub = match ($sub) {
                    'TK' => 'TK',
                    'SD' => 'SD/MI',
                    'SMP' => 'SMP/MTs',
                    'SMA' => 'SMA/SMK/MA/SMAK/MAK',
                    'Referensi' => 'Referensi',
                    default => $sub ?: 'SD/MI',
                };
                $eduLevel = $targetSub;
            } elseif ($main === 'Anak' || $main === 'Anak-Anak') {
                $targetMain = 'Buku Umum';
                $targetSub = 'Buku Anak';
                $eduLevel = null;
            } elseif ($main === 'Remaja') {
                $targetMain = 'Buku Umum';
                $targetSub = 'Buku Remaja';
                $eduLevel = null;
            } elseif ($main === 'Dewasa') {
                $targetMain = 'Buku Umum';
                $targetSub = 'Buku Dewasa';
                $eduLevel = null;
            } else {
                continue;
            }

            $key = $targetMain . '::' . $targetSub;
            if (isset($subModelMap[$key])) {
                $book->category_id = $subModelMap[$key]['category_id'];
                $book->subcategory_id = $subModelMap[$key]['subcategory_id'];
            }
            $book->main_category = $targetMain;
            $book->sub_category = $targetSub;
            $book->education_level = $eduLevel;
            $book->saveQuietly();
        }
    }

    public function down(): void
    {
        // Reversible if necessary
    }
};
