<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class BookMetadataService
{
    public function findByIsbn(string $isbn): ?array
    {
        $isbn = $this->normalizeIsbn($isbn);

        if (!$isbn) {
            return null;
        }

        $google = $this->fromGoogleBooks($isbn);
        if ($google) {
            return $google;
        }

        return $this->fromOpenLibrary($isbn);
    }

    public function normalizeIsbn(string $isbn): ?string
    {
        $isbn = strtoupper(preg_replace('/[^0-9X]/', '', $isbn));

        return preg_match('/^(?:\d{10}|\d{13})$/', $isbn) ? $isbn : null;
    }

    private function fromGoogleBooks(string $isbn): ?array
    {
        $candidates = [$isbn];
        if (strlen($isbn) === 13 && in_array(substr($isbn, 0, 3), ['978', '979'], true)) {
            $isbn10 = $this->convertIsbn13ToIsbn10($isbn);
            if ($isbn10) {
                $candidates[] = $isbn10;
            }
        }

        $paramsBase = ['maxResults' => 10];
        $apiKey = config('services.google_books.key');
        if ($apiKey) {
            $paramsBase['key'] = $apiKey;
        }

        foreach (array_unique($candidates) as $candidate) {
            foreach (['isbn:' . $candidate, $candidate] as $query) {
                try {
                    $response = Http::timeout(8)
                        ->acceptJson()
                        ->get('https://www.googleapis.com/books/v1/volumes', array_merge($paramsBase, [
                            'q' => $query,
                        ]));

                    if (!$response->successful()) {
                        continue;
                    }

                    foreach ($response->json('items', []) as $item) {
                        $volume = $item['volumeInfo'] ?? [];
                        $identifiers = collect($volume['industryIdentifiers'] ?? [])
                            ->pluck('identifier')
                            ->map(fn ($value) => $this->normalizeIsbn((string) $value))
                            ->filter()
                            ->values()
                            ->all();

                        if (!in_array($isbn, $identifiers, true) && !in_array($candidate, $identifiers, true)) {
                            continue;
                        }

                        return $this->mapMetadata(
                            isbn: $isbn,
                            title: $volume['title'] ?? null,
                            author: collect($volume['authors'] ?? [])->filter()->implode(', ') ?: null,
                            publisher: $volume['publisher'] ?? null,
                            publicationYear: $this->extractYear($volume['publishedDate'] ?? null),
                            pages: isset($volume['pageCount']) ? (int) $volume['pageCount'] : null,
                            language: $volume['language'] ?? null,
                            description: $volume['description'] ?? null,
                            categories: collect($volume['categories'] ?? [])->implode(', ') ?: null,
                            cover: $this->secureUrl($volume['imageLinks']['thumbnail'] ?? $volume['imageLinks']['smallThumbnail'] ?? null),
                            source: 'Google Books',
                            sourceUrl: $item['selfLink'] ?? null,
                        );
                    }
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        }

        return null;
    }

    private function fromOpenLibrary(string $isbn): ?array
    {
        try {
            $response = Http::timeout(8)
                ->acceptJson()
                ->get('https://openlibrary.org/search.json', [
                    'isbn' => $isbn,
                    'limit' => 1,
                    'fields' => 'title,author_name,publisher,first_publish_year,number_of_pages_median,cover_i,language,subject,edition_key',
                ]);

            if (!$response->successful()) {
                return null;
            }

            $doc = $response->json('docs.0');
            if (!$doc) {
                return null;
            }

            $cover = null;
            if (!empty($doc['cover_i'])) {
                $cover = 'https://covers.openlibrary.org/b/id/' . (int) $doc['cover_i'] . '-L.jpg';
            }

            return $this->mapMetadata(
                isbn: $isbn,
                title: $doc['title'] ?? null,
                author: collect($doc['author_name'] ?? [])->filter()->implode(', ') ?: null,
                publisher: collect($doc['publisher'] ?? [])->filter()->first(),
                publicationYear: isset($doc['first_publish_year']) ? (int) $doc['first_publish_year'] : null,
                pages: isset($doc['number_of_pages_median']) ? (int) $doc['number_of_pages_median'] : null,
                language: collect($doc['language'] ?? [])->filter()->first(),
                description: null,
                categories: collect($doc['subject'] ?? [])->take(5)->implode(', ') ?: null,
                cover: $cover,
                source: 'Open Library',
                sourceUrl: !empty($doc['edition_key'][0]) ? 'https://openlibrary.org/books/' . $doc['edition_key'][0] : 'https://openlibrary.org/search?isbn=' . urlencode($isbn),
            );
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }

    private function mapMetadata(
        string $isbn,
        ?string $title,
        ?string $author,
        ?string $publisher,
        ?int $publicationYear,
        ?int $pages,
        ?string $language,
        ?string $description,
        ?string $categories,
        ?string $cover,
        string $source,
        ?string $sourceUrl,
    ): array {
        return [
            'isbn' => $isbn,
            'title' => $title ? trim($title) : null,
            'author' => $author ? trim($author) : null,
            'publisher' => $publisher ? trim($publisher) : null,
            'publication_year' => $publicationYear,
            'pages' => $pages,
            'language' => $language ? trim($language) : null,
            'description' => $description ? trim(strip_tags($description)) : null,
            'categories' => $categories ? trim($categories) : null,
            'cover' => $cover,
            'source' => $source,
            'source_url' => $sourceUrl,
        ];
    }

    private function extractYear(?string $date): ?int
    {
        if (!$date) {
            return null;
        }

        return preg_match('/\b(18|19|20)\d{2}\b/', $date, $matches) ? (int) $matches[0] : null;
    }

    private function secureUrl(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        return Str::startsWith($url, 'http://') ? Str::replaceFirst('http://', 'https://', $url) : $url;
    }

    private function convertIsbn13ToIsbn10(string $isbn13): ?string
    {
        if (strlen($isbn13) !== 13 || !in_array(substr($isbn13, 0, 3), ['978', '979'], true)) {
            return null;
        }

        $digits = substr($isbn13, 3, 9);
        if (!ctype_digit($digits)) {
            return null;
        }

        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int) $digits[$i] * (10 - $i);
        }

        $remainder = 11 - ($sum % 11);
        $check = $remainder === 10 ? 'X' : ($remainder === 11 ? '0' : (string) $remainder);

        return $digits . $check;
    }
}