<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FetchBookCovers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:fetch-covers 
                            {--limit=100 : Batasi jumlah buku yang diperiksa (0 untuk memproses semua)}
                            {--target=0 : Target jumlah cover yang BERHASIL diunduh (berhenti saat jumlah ini tercapai)}
                            {--start-id=0 : Mulai dari ID buku tertentu untuk melompati buku awal}
                            {--only-isbn : Hanya proses buku yang memiliki ISBN resmi}
                            {--delay=200 : Jeda waktu antar request dalam milidetik (mencegah rate limit)}
                            {--force : Paksa unduh ulang meskipun buku sudah memiliki cover}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menarik HANYA gambar cover buku melalui multi-sumber (Google Books, Open Library, Indonesia OneSearch, Katalog Penerbit) tanpa mengubah data lain.';

    /**
     * Known empty placeholder MD5 hashes to ignore (e.g. VuFind / OneSearch).
     */
    private const EMPTY_PLACEHOLDER_HASHES = [
        '5ca5cd11373b3dfb1fb0e5adc796a1d8', // Indonesia OneSearch generic "No Cover"
        'a3e02d2ccbf657ab0e035bb0261c6859', // 1x1 transparent gif
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $targetSuccess = (int) $this->option('target');
        $onlyIsbn = (bool) $this->option('only-isbn');
        $startId = (int) $this->option('start-id');
        $delayMs = (int) $this->option('delay');
        $force = (bool) $this->option('force');

        $query = Book::query();

        if (!$force) {
            $query->where(function ($q) {
                $q->whereNull('cover')
                  ->orWhere('cover', '');
            });
        }

        if ($startId > 0) {
            $query->where('id', '>=', $startId);
        }

        if ($onlyIsbn) {
            $query->whereNotNull('isbn')
                  ->where('isbn', '!=', '')
                  ->whereRaw("LENGTH(REPLACE(REPLACE(isbn, '-', ''), ' ', '')) IN (10, 13)");
        }

        // Prioritaskan buku dengan ISBN valid, lalu ID terkecil
        $query->orderByRaw("CASE WHEN isbn IS NOT NULL AND isbn != '' THEN 0 ELSE 1 END, id ASC");

        $totalAvailable = (clone $query)->count();

        if ($totalAvailable === 0) {
            $this->info('Semua buku pada kriteria ini sudah memiliki cover.');
            return Command::SUCCESS;
        }

        $this->info("================================================================================");
        $this->info(" MEMULAI PENARIKAN COVER BUKU MULTI-SUMBER (WATERFALL STRATEGY)");
        $this->info("================================================================================");
        $this->line("Sumber 1 : Google Books API (ISBN / Judul + Penulis)");
        $this->line("Sumber 2 : Open Library Covers & Search API");
        $this->line("Sumber 3 : Indonesia OneSearch (IOS / Perpusnas)");
        $this->line("Sumber 4 : Katalog Penerbit Indonesia (Gramedia, Mizan, Elex, Tiga Serangkai)");
        $this->newLine();
        $this->line("Buku dalam antrean  : <comment>{$totalAvailable}</comment>");
        if ($targetSuccess > 0) {
            $this->line("Target cover sukses : <comment>{$targetSuccess}</comment> cover berhasil didapat");
        } else {
            $this->line("Batas buku diperiksa: <comment>" . ($limit > 0 ? $limit : 'Semua') . "</comment> buku");
        }
        $this->line("Filter hanya ISBN   : <comment>" . ($onlyIsbn ? 'YA' : 'TIDAK (ISBN + Judul & Penulis)') . "</comment>");
        $this->line("Jeda per request    : <comment>{$delayMs} ms</comment>");
        $this->newLine();

        $maxSteps = $targetSuccess > 0 ? $targetSuccess : ($limit > 0 ? min($limit, $totalAvailable) : $totalAvailable);
        $bar = $this->output->createProgressBar($maxSteps);
        $bar->start();

        $processed = 0;
        $success = 0;
        $notFound = 0;
        $failed = 0;

        $sourcesStats = [
            'Google Books' => 0,
            'Open Library' => 0,
            'Indonesia OneSearch' => 0,
            'Penerbit Indonesia' => 0,
        ];

        $apiKey = config('services.google_books.key');
        $booksCursor = $query->cursor();

        foreach ($booksCursor as $book) {
            if ($targetSuccess > 0 && $success >= $targetSuccess) {
                break;
            }

            if ($targetSuccess <= 0 && $limit > 0 && $processed >= $limit) {
                break;
            }

            try {
                $coverResult = $this->lookupCoverMultiSource($book, $apiKey, $onlyIsbn);

                if ($coverResult && !empty($coverResult['url'])) {
                    $savedPath = $this->downloadAndSaveCover($coverResult['url'], $book, $coverResult['bytes'] ?? null);

                    if ($savedPath) {
                        // ATURAN KETAT: HANYA perbarui kolom cover pada tabel books
                        Book::where('id', $book->id)->update([
                            'cover' => $savedPath,
                        ]);

                        $success++;
                        $sourceName = $coverResult['source'] ?? 'External';
                        $sourcesStats[$sourceName] = ($sourcesStats[$sourceName] ?? 0) + 1;

                        $this->line("\n <info>[✓] Cover berhasil ({$sourceName}):</info> [ID: {$book->id}] {$book->judul_buku}");
                        if ($targetSuccess > 0) {
                            $bar->advance();
                        }
                    } else {
                        $failed++;
                    }
                } else {
                    $notFound++;
                }
            } catch (\Throwable $e) {
                $failed++;
            }

            $processed++;
            if ($targetSuccess <= 0) {
                $bar->advance();
            }

            if ($delayMs > 0) {
                usleep($delayMs * 1000);
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("================================================================================");
        $this->info(" HASIL PENARIKAN COVER BUKU");
        $this->info("================================================================================");
        $this->table(
            ['Keterangan', 'Jumlah'],
            [
                ['Total Buku Diperiksa', $processed],
                ['Berhasil Mendapat Cover', $success],
                [' - Sumber: Google Books', $sourcesStats['Google Books']],
                [' - Sumber: Open Library', $sourcesStats['Open Library']],
                [' - Sumber: Indonesia OneSearch', $sourcesStats['Indonesia OneSearch']],
                [' - Sumber: Penerbit Indonesia', $sourcesStats['Penerbit Indonesia']],
                ['Cover Tidak Ditemukan', $notFound],
                ['Gagal Download / Error', $failed],
            ]
        );

        return Command::SUCCESS;
    }

    /**
     * Mencari cover secara bertahap (Waterfall Strategy) ke berbagai sumber:
     * 1. Google Books API
     * 2. Open Library API
     * 3. Indonesia OneSearch (IOS / Perpusnas)
     * 4. Katalog Penerbit Indonesia (Gramedia, Mizan, Elex, Tiga Serangkai)
     *
     * @return array{url: string, source: string, bytes: ?string}|null
     */
    private function lookupCoverMultiSource(Book $book, ?string $apiKey, bool $onlyIsbn): ?array
    {
        $cleanIsbn = preg_replace('/[^0-9Xx]/', '', (string) $book->isbn);
        $hasValidIsbn = !empty($cleanIsbn) && (strlen($cleanIsbn) === 10 || strlen($cleanIsbn) === 13);

        $rawTitle = (string) $book->judul_buku;
        $cleanTitle = trim(preg_replace('/^[:\(\[\s\.\-]+|[\)\]\s\.\-]+$/', '', $rawTitle));

        $rawAuthor = (string) $book->penulis;
        $cleanAuthor = trim(preg_replace('/,\s*(dkk|et al\.|dsb).*$/i', '', $rawAuthor));
        if ($cleanAuthor === '-' || strtolower($cleanAuthor) === 'penulis') {
            $cleanAuthor = '';
        }

        // =====================================================================
        // SUMBER 1: GOOGLE BOOKS API
        // =====================================================================
        if ($hasValidIsbn) {
            $url = $this->fetchFromGoogle('isbn:' . $cleanIsbn, $apiKey);
            if ($url) {
                return ['url' => $url, 'source' => 'Google Books', 'bytes' => null];
            }
        }

        if (!$onlyIsbn && !empty($cleanTitle)) {
            // Coba judul + penulis
            if (!empty($cleanAuthor)) {
                $url = $this->fetchFromGoogle('intitle:' . $cleanTitle . '+inauthor:' . $cleanAuthor, $apiKey);
                if ($url) {
                    return ['url' => $url, 'source' => 'Google Books', 'bytes' => null];
                }
            }

            // Coba judul saja
            $url = $this->fetchFromGoogle('intitle:' . $cleanTitle, $apiKey);
            if ($url) {
                return ['url' => $url, 'source' => 'Google Books', 'bytes' => null];
            }
        }

        // =====================================================================
        // SUMBER 2: OPEN LIBRARY API
        // =====================================================================
        if ($hasValidIsbn) {
            $olUrl = "https://covers.openlibrary.org/b/isbn/{$cleanIsbn}-L.jpg?default=false";
            if ($this->isImageUrlValid($olUrl)) {
                return ['url' => $olUrl, 'source' => 'Open Library', 'bytes' => null];
            }
        }

        if (!$onlyIsbn && !empty($cleanTitle)) {
            $olSearchUrl = $this->fetchFromOpenLibrarySearch($cleanTitle, $cleanAuthor);
            if ($olSearchUrl) {
                return ['url' => $olSearchUrl, 'source' => 'Open Library', 'bytes' => null];
            }
        }

        // =====================================================================
        // SUMBER 3: INDONESIA ONESEARCH (IOS / PERPUSNAS)
        // =====================================================================
        if ($hasValidIsbn) {
            $iosResult = $this->fetchFromIndonesiaOneSearch($cleanIsbn);
            if ($iosResult) {
                return $iosResult;
            }
        }

        if (!$onlyIsbn && !empty($cleanTitle)) {
            $iosTitleResult = $this->fetchFromIndonesiaOneSearchByTitle($cleanTitle);
            if ($iosTitleResult) {
                return $iosTitleResult;
            }
        }

        // =====================================================================
        // SUMBER 4: KATALOG PENERBIT INDONESIA (GRAMEDIA, MIZAN, ELEX, TIGA SERANGKAI)
        // =====================================================================
        if (!$onlyIsbn && !empty($cleanTitle)) {
            $publisherResult = $this->fetchFromIndonesianPublishers($cleanTitle, $apiKey);
            if ($publisherResult) {
                return $publisherResult;
            }
        }

        return null;
    }

    /**
     * Request ke Google Books API volume endpoint.
     */
    private function fetchFromGoogle(string $q, ?string $apiKey): ?string
    {
        $params = [
            'q' => $q,
            'maxResults' => 1,
            'printType' => 'books',
        ];

        if (!empty($apiKey)) {
            $params['key'] = $apiKey;
        }

        try {
            $response = Http::timeout(3)
                ->acceptJson()
                ->get('https://www.googleapis.com/books/v1/volumes', $params);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();
            $items = $data['items'] ?? [];

            if (empty($items)) {
                return null;
            }

            $imageLinks = $items[0]['volumeInfo']['imageLinks'] ?? [];

            $imageUrl = $imageLinks['thumbnail'] 
                ?? $imageLinks['smallThumbnail'] 
                ?? $imageLinks['medium'] 
                ?? $imageLinks['large'] 
                ?? null;

            if ($imageUrl) {
                $imageUrl = preg_replace('/^http:/i', 'https:', $imageUrl);
                $imageUrl = str_replace('&edge=curl', '', $imageUrl);
                return $imageUrl;
            }
        } catch (\Throwable $e) {
            return null;
        }

        return null;
    }

    /**
     * Cari cover via Open Library Search API berdasarkan Judul & Penulis.
     */
    private function fetchFromOpenLibrarySearch(string $title, string $author): ?string
    {
        try {
            $params = [
                'title' => $title,
                'limit' => 1,
            ];
            if (!empty($author)) {
                $params['author'] = $author;
            }

            $response = Http::timeout(3)->get('https://openlibrary.org/search.json', $params);
            if (!$response->successful()) {
                return null;
            }

            $docs = $response->json()['docs'] ?? [];
            if (!empty($docs)) {
                $coverId = $docs[0]['cover_i'] ?? null;
                if ($coverId) {
                    $url = "https://covers.openlibrary.org/b/id/{$coverId}-L.jpg?default=false";
                    if ($this->isImageUrlValid($url)) {
                        return $url;
                    }
                }
            }
        } catch (\Throwable $e) {
            return null;
        }

        return null;
    }

    /**
     * Ambil cover dari Indonesia OneSearch (IOS / Perpusnas) berdasarkan ISBN.
     */
    private function fetchFromIndonesiaOneSearch(string $cleanIsbn): ?array
    {
        try {
            $url = "https://onesearch.id/Cover/Show?isn={$cleanIsbn}&size=large";
            $response = Http::timeout(4)->get($url);

            if (!$response->successful()) {
                return null;
            }

            $body = $response->body();
            $contentType = strtolower($response->header('Content-Type', ''));

            if (strlen($body) < 1024 || str_contains($contentType, 'gif')) {
                return null;
            }

            if (in_array(md5($body), self::EMPTY_PLACEHOLDER_HASHES, true)) {
                return null;
            }

            return [
                'url' => $url,
                'source' => 'Indonesia OneSearch',
                'bytes' => $body,
            ];
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Cari cover di Indonesia OneSearch via RSS Feed berdasarkan Judul Buku.
     */
    private function fetchFromIndonesiaOneSearchByTitle(string $title): ?array
    {
        try {
            $url = "https://onesearch.id/Search/Results?lookfor=" . urlencode($title) . "&view=rss";
            $response = Http::timeout(4)->get($url);

            if (!$response->successful() || !str_contains($response->body(), '<rss')) {
                return null;
            }

            $xml = @simplexml_load_string($response->body());
            if (!$xml || empty($xml->channel->item)) {
                return null;
            }

            $checked = 0;
            foreach ($xml->channel->item as $item) {
                if (++$checked > 2) {
                    break;
                }
                $link = (string) $item->link;
                if (preg_match('/Record\/([^\/\?]+)/', $link, $matches)) {
                    $recordId = $matches[1];
                    $coverUrl = "https://onesearch.id/Cover/Show?id=" . urlencode($recordId) . "&size=large";
                    $imgRes = Http::timeout(2.5)->get($coverUrl);

                    if ($imgRes->successful()) {
                        $body = $imgRes->body();
                        $contentType = strtolower($imgRes->header('Content-Type', ''));
                        if (strlen($body) >= 1024 && !str_contains($contentType, 'gif') && !in_array(md5($body), self::EMPTY_PLACEHOLDER_HASHES, true)) {
                            return [
                                'url' => $coverUrl,
                                'source' => 'Indonesia OneSearch',
                                'bytes' => $body,
                            ];
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            return null;
        }

        return null;
    }

    /**
     * Cari cover di katalog penerbit Indonesia terindeks (Gramedia, Mizan, Elex Media, Tiga Serangkai).
     */
    private function fetchFromIndonesianPublishers(string $title, ?string $apiKey): ?array
    {
        $publishers = ['Gramedia', 'Mizan', 'Elex'];

        foreach ($publishers as $pub) {
            $query = 'intitle:' . $title . '+inpublisher:' . $pub;
            $url = $this->fetchFromGoogle($query, $apiKey);
            if ($url) {
                return [
                    'url' => $url,
                    'source' => 'Penerbit Indonesia',
                    'bytes' => null,
                ];
            }
        }

        return null;
    }

    /**
     * Memeriksa apakah URL cover publik mengembalikan gambar valid (status 200).
     */
    private function isImageUrlValid(string $url): bool
    {
        try {
            $res = Http::timeout(3)->head($url);
            return $res->successful();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Unduh file gambar dan simpan ke storage disk public/covers.
     */
    private function downloadAndSaveCover(string $imageUrl, Book $book, ?string $prefetchedBytes = null): ?string
    {
        try {
            $body = $prefetchedBytes;
            $contentType = '';

            if ($body === null) {
                $response = Http::timeout(5)->get($imageUrl);

                if (!$response->successful() || strlen($response->body()) < 1000) {
                    return null;
                }

                $body = $response->body();
                $contentType = strtolower($response->header('Content-Type', ''));
            }

            // Validasi bukan placeholder VuFind
            if (in_array(md5($body), self::EMPTY_PLACEHOLDER_HASHES, true)) {
                return null;
            }

            $extension = match (true) {
                str_contains($contentType, 'png') => 'png',
                str_contains($contentType, 'webp') => 'webp',
                default => 'jpg',
            };

            $filename = !empty($book->sku) 
                ? Str::slug($book->sku) . '.' . $extension
                : (!empty($book->isbn) ? preg_replace('/[^0-9Xx]/', '', $book->isbn) . '.' . $extension : 'book-' . $book->id . '-' . Str::random(6) . '.' . $extension);

            $relativeDirectory = 'covers';
            $relativePath = $relativeDirectory . '/' . $filename;

            // Simpan ke storage public disk (storage/app/public/covers/)
            Storage::disk('public')->put($relativePath, $body);

            return $relativePath;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
