<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Collection;

class RecommendationService
{
    /**
     * Entry point utama rekomendasi.
     *
     * User yang sudah punya interaksi:
     * -> personalized recommendation
     *
     * User baru / belum punya interaksi:
     * -> cold start recommendation
     */
    public function getForUser(?User $user, int $limit = 8): Collection
    {
        if (!$user) {
            return $this->getColdStartRecommendations($limit);
        }

        $interactionData = $this->getUserInteractionData($user);

        $hasInteraction =
            $interactionData['book_ids']->isNotEmpty() ||
            $interactionData['category_ids']->isNotEmpty() ||
            $interactionData['subcategory_ids']->isNotEmpty();

        if (!$hasInteraction) {
            return $this->getColdStartRecommendations($limit);
        }

        return $this->getPersonalizedRecommendations(
            $interactionData,
            $limit
        );
    }

    /**
     * Mengambil data interaksi user.
     *
     * Sumber interaksi:
     * - buku yang pernah dipinjam
     * - buku yang pernah direservasi
     * - buku favorit dari session
     */
    protected function getUserInteractionData(User $user): array
    {
        /*
         * Buku yang pernah dipinjam user.
         */
        $borrowedBookIds = Borrowing::query()
            ->where('user_id', $user->id)
            ->whereNotNull('book_id')
            ->pluck('book_id');

        /*
         * Buku yang pernah direservasi user.
         */
        $reservedBookIds = Reservation::query()
            ->where('user_id', $user->id)
            ->whereNotNull('book_id')
            ->pluck('book_id');

        /*
         * Favorit saat ini masih menggunakan session
         * pada branch Pandu.
         */
        $favoriteBookIds = collect(
            session('user_favorites', [])
        );

        /*
         * Gabungkan seluruh interaksi.
         */
        $bookIds = $borrowedBookIds
            ->merge($reservedBookIds)
            ->merge($favoriteBookIds)
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        /*
         * User belum mempunyai interaksi.
         */
        if ($bookIds->isEmpty()) {
            return [
                'book_ids' => collect(),
                'category_ids' => collect(),
                'subcategory_ids' => collect(),
                'author_names' => collect(),
                'education_levels' => collect(),
            ];
        }

        /*
         * Ambil karakteristik buku yang pernah
         * berinteraksi dengan user.
         */
        $books = Book::query()
            ->whereIn('id', $bookIds)
            ->get([
                'id',
                'category_id',
                'subcategory_id',
                'penulis',
                'education_level',
            ]);

        return [
            'book_ids' => $bookIds,

            'category_ids' => $books
                ->pluck('category_id')
                ->filter()
                ->unique()
                ->values(),

            'subcategory_ids' => $books
                ->pluck('subcategory_id')
                ->filter()
                ->unique()
                ->values(),

            'author_names' => $books
                ->pluck('penulis')
                ->filter()
                ->unique()
                ->values(),

            'education_levels' => $books
                ->pluck('education_level')
                ->filter()
                ->unique()
                ->values(),
        ];
    }

    /**
     * Personalized recommendation.
     *
     * Score:
     * Subkategori       = +40
     * Kategori          = +25
     * Penulis           = +20
     * Education level   = +10
     */
    protected function getPersonalizedRecommendations(
        array $data,
        int $limit = 8
    ): Collection {
        $query = Book::query()
            ->where('stok', '>', 0)
            ->whereNotIn('id', $data['book_ids']);

        $scoreParts = [];
        $bindings = [];

        /*
         * ---------------------------------------------------------
         * SUBKATEGORI
         * ---------------------------------------------------------
         */

        if ($data['subcategory_ids']->isNotEmpty()) {
            $placeholders = implode(
                ',',
                array_fill(
                    0,
                    $data['subcategory_ids']->count(),
                    '?'
                )
            );

            $scoreParts[] = "
                CASE
                    WHEN subcategory_id IN ($placeholders)
                    THEN 40
                    ELSE 0
                END
            ";

            $bindings = array_merge(
                $bindings,
                $data['subcategory_ids']->all()
            );
        }

        /*
         * ---------------------------------------------------------
         * KATEGORI
         * ---------------------------------------------------------
         */

        if ($data['category_ids']->isNotEmpty()) {
            $placeholders = implode(
                ',',
                array_fill(
                    0,
                    $data['category_ids']->count(),
                    '?'
                )
            );

            $scoreParts[] = "
                CASE
                    WHEN category_id IN ($placeholders)
                    THEN 25
                    ELSE 0
                END
            ";

            $bindings = array_merge(
                $bindings,
                $data['category_ids']->all()
            );
        }

        /*
         * ---------------------------------------------------------
         * PENULIS
         * ---------------------------------------------------------
         */

        if ($data['author_names']->isNotEmpty()) {
            $placeholders = implode(
                ',',
                array_fill(
                    0,
                    $data['author_names']->count(),
                    '?'
                )
            );

            $scoreParts[] = "
                CASE
                    WHEN penulis IN ($placeholders)
                    THEN 20
                    ELSE 0
                END
            ";

            $bindings = array_merge(
                $bindings,
                $data['author_names']->all()
            );
        }

        /*
         * ---------------------------------------------------------
         * EDUCATION LEVEL
         * ---------------------------------------------------------
         */

        if ($data['education_levels']->isNotEmpty()) {
            $placeholders = implode(
                ',',
                array_fill(
                    0,
                    $data['education_levels']->count(),
                    '?'
                )
            );

            $scoreParts[] = "
                CASE
                    WHEN education_level IN ($placeholders)
                    THEN 10
                    ELSE 0
                END
            ";

            $bindings = array_merge(
                $bindings,
                $data['education_levels']->all()
            );
        }

        /*
         * Tidak ada komponen scoring.
         */
        if (empty($scoreParts)) {
            return $this->getColdStartRecommendations($limit);
        }

        $scoreSql = implode(' + ', $scoreParts);

        $query
            ->select('books.*')
            ->selectRaw(
                "($scoreSql) AS recommendation_score",
                $bindings
            )
            ->orderByDesc('recommendation_score')
            ->orderByDesc('stok')
            ->orderByDesc('created_at')
            ->limit($limit);

        $recommendations = $query->get();

        /*
         * Jika personalized tidak mendapatkan
         * jumlah buku yang cukup, gunakan cold start
         * sebagai fallback.
         */
        if ($recommendations->count() < $limit) {

            $existingIds = $recommendations
                ->pluck('id')
                ->merge($data['book_ids'])
                ->unique()
                ->values();

            $fallback = $this->getColdStartRecommendations(
                $limit - $recommendations->count(),
                $existingIds
            );

            $recommendations = $recommendations
                ->concat($fallback)
                ->unique('id')
                ->values();
        }

        return $recommendations;
    }

    /**
     * Cold Start Recommendation.
     *
     * Digunakan untuk:
     * - user baru
     * - user belum pernah meminjam
     * - user belum pernah reservasi
     * - user belum mempunyai favorit
     *
     * Ranking:
     * 1. Jumlah peminjaman
     * 2. Stok tersedia
     * 3. Buku terbaru
     */
    protected function getColdStartRecommendations(
        int $limit = 8,
        ?Collection $excludeIds = null
    ): Collection {
        $excludeIds = $excludeIds ?? collect();

        $query = Book::query()
            ->where('stok', '>', 0);

        if ($excludeIds->isNotEmpty()) {
            $query->whereNotIn('id', $excludeIds);
        }

        /*
         * Book.php Pandu sudah mempunyai:
         *
         * public function borrowings()
         * {
         *     return $this->hasMany(Borrowing::class, 'book_id');
         * }
         *
         * Jadi kita bisa langsung menggunakan withCount().
         */
        return $query
            ->withCount('borrowings')
            ->orderByDesc('borrowings_count')
            ->orderByDesc('stok')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}