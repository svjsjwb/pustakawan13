<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Mapping Rak
        |--------------------------------------------------------------------------
        */
        $rackByCategory = [
            'Buku Pelajaran' => 'B2',
            'Buku Umum'      => 'A1',
        ];

        /*
        |--------------------------------------------------------------------------
        | Data Buku
        |--------------------------------------------------------------------------
        |
        | Total tetap 90 judul.
        |
        | Struktur disesuaikan dengan CategorySeeder + SubcategorySeeder:
        |
        | Buku Pelajaran
        | ├── SD/MI
        | ├── SMP/MTs
        | └── SMA/SMK/MA/SMAK/MAK
        |
        | Buku Umum
        | ├── Buku Anak
        | ├── Buku Remaja
        | └── Buku Dewasa
        |
        |--------------------------------------------------------------------------
        */

        $definitions = [

            /*
            |--------------------------------------------------------------------------
            | BUKU PELAJARAN
            |--------------------------------------------------------------------------
            */

            'Buku Pelajaran' => [

                'SD/MI' => [
                    ['Matematika untuk SD/MI Kelas 1', 'Meita Fitrianawati'],
                    ["Charlotte's Web", 'E. B. White'],
                    ['Where the Wild Things Are', 'Maurice Sendak'],
                    ['Matematika untuk SD/MI Kelas 4', 'Pusat Perbukuan'],
                    ['Matematika untuk SD/MI Kelas 5', 'Meita Fitrianawati'],
                    ['Matematika untuk SD/MI Kelas 6', 'Pusat Perbukuan'],
                    ['Wonder', 'R. J. Palacio'],
                    ['Bahasa Indonesia: Kawan Seiring untuk SD/MI Kelas 3', 'Anna Farida K., Helva Nurhidayah'],
                    ['The Little Prince', 'Antoine de Saint-Exupéry'],
                    ['The Diary of a Young Girl', 'Anne Frank'],
                ],

                'SMP/MTs' => [
                    ['The Giver', 'Lois Lowry'],
                    ['Coraline', 'Neil Gaiman'],
                    ['The Maze Runner', 'James Dashner'],
                    ['Wonder', 'R. J. Palacio'],
                    ['Percy Jackson and the Olympians: The Lightning Thief', 'Rick Riordan'],
                    ["Harry Potter and the Philosopher's Stone", 'J. K. Rowling'],
                    ['The Hobbit', 'J. R. R. Tolkien'],
                    ['The Chronicles of Narnia', 'C. S. Lewis'],
                    ['Anne of Green Gables', 'L. M. Montgomery'],
                    ['The Secret Garden', 'Frances Hodgson Burnett'],
                ],

                'SMA/SMK/MA/SMAK/MAK' => [
                    ['The Great Gatsby', 'F. Scott Fitzgerald'],
                    ['To Kill a Mockingbird', 'Harper Lee'],
                    ['1984', 'George Orwell'],
                    ['Animal Farm', 'George Orwell'],
                    ['The Alchemist', 'Paulo Coelho'],
                    ['The Little Prince', 'Antoine de Saint-Exupéry'],
                    ['The Alchemist', 'Paulo Coelho'],
                    ['The Giving Tree', 'Shel Silverstein'],
                    ['Pride and Prejudice', 'Jane Austen'],
                    ['The 7 Habits of Highly Effective People', 'Stephen R. Covey'],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | BUKU UMUM - BUKU ANAK
            |--------------------------------------------------------------------------
            */

            'Buku Umum' => [

                'Buku Anak' => [
                    ['Charlie and the Chocolate Factory', 'Roald Dahl'],
                    ['Matilda', 'Roald Dahl'],
                    ['The BFG', 'Roald Dahl'],
                    ['The Witches', 'Roald Dahl'],
                    ['James and the Giant Peach', 'Roald Dahl'],
                    ['Charlotte’s Web', 'E. B. White'],
                    ['Stuart Little', 'E. B. White'],
                    ['The Tale of Peter Rabbit', 'Beatrix Potter'],
                    ['Alice’s Adventures in Wonderland', 'Lewis Carroll'],
                    ['The Little Prince', 'Antoine de Saint-Exupéry'],

                    ['National Geographic Kids: Weird But True!', 'National Geographic Kids'],
                    ['National Geographic Kids: Everything Money', 'National Geographic Kids'],
                    ['National Geographic Kids: Everything Space', 'National Geographic Kids'],
                    ['National Geographic Kids: Everything Dinosaurs', 'National Geographic Kids'],
                    ['Usborne First Encyclopedia of Science', 'Jane Elliott'],
                    ['The Usborne Book of Animals', 'James Maclaine'],
                    ['The Usborne Book of Planet Earth', 'Megan Cullis, Matthew Oldham'],
                    ['The Usborne Book of Famous Artists', 'Rosie Dickens'],
                    ['DK Eyewitness: Dinosaur', 'David Burnie'],
                    ['DK Eyewitness: Space', 'David Baker'],
                ],

                /*
                |--------------------------------------------------------------------------
                | BUKU UMUM - BUKU REMAJA
                |--------------------------------------------------------------------------
                */

                'Buku Remaja' => [
                    ['Harry Potter and the Philosopher’s Stone', 'J. K. Rowling'],
                    ['Harry Potter and the Chamber of Secrets', 'J. K. Rowling'],
                    ['Harry Potter and the Prisoner of Azkaban', 'J. K. Rowling'],
                    ['The Hunger Games', 'Suzanne Collins'],
                    ['Catching Fire', 'Suzanne Collins'],
                    ['Divergent', 'Veronica Roth'],
                    ['The Fault in Our Stars', 'John Green'],
                    ['Looking for Alaska', 'John Green'],
                    ['The Perks of Being a Wallflower', 'Stephen Chbosky'],
                    ['Ten Years Challenge', 'Mutiarini'],

                    ['Atomic Habits', 'James Clear'],
                    ['The 7 Habits of Highly Effective Teens', 'Sean Covey'],
                    ['The Power of Habit', 'Charles Duhigg'],
                    ['Mindset', 'Carol S. Dweck'],
                    ['How to Win Friends and Influence People', 'Dale Carnegie'],
                    ['The Psychology of Money', 'Morgan Housel'],
                    ['The Subtle Art of Not Giving a F*ck', 'Mark Manson'],
                    ['Ikigai', 'Héctor García, Francesc Miralles'],
                    ['Think Like a Monk', 'Jay Shetty'],
                    ['The 5 AM Club', 'Robin Sharma'],
                ],

                /*
                |--------------------------------------------------------------------------
                | BUKU UMUM - BUKU DEWASA
                |--------------------------------------------------------------------------
                */

                'Buku Dewasa' => [
                    ['Maria Beetle', 'Kotaro Isaka'],
                    ['Laskar Pelangi', 'Andrea Hirata'],
                    ['Bumi Manusia', 'Pramoedya Ananta Toer'],
                    ['Pulang', 'Leila S. Chudori'],
                    ['Laut Bercerita', 'Leila S. Chudori'],
                    ['Pulang', 'Leila S. Chudori'],
                    ['Supernova: Ksatria, Puteri dan Bintang Jatuh', 'Dee Lestari'],
                    ['Norwegian Wood', 'Haruki Murakami'],
                    ['The Alchemist', 'Paulo Coelho'],
                    ['Pride and Prejudice', 'Jane Austen'],

                    ['Sapiens: A Brief History of Humankind', 'Yuval Noah Harari'],
                    ['Homo Deus', 'Yuval Noah Harari'],
                    ['Educated', 'Tara Westover'],
                    ['Becoming', 'Michelle Obama'],
                    ['Steve Jobs', 'Walter Isaacson'],
                    ['A Brief History of Time', 'Stephen Hawking'],
                    ['Thinking, Fast and Slow', 'Daniel Kahneman'],
                    ['Outliers', 'Malcolm Gladwell'],
                    ['The Intelligent Investor', 'Benjamin Graham'],
                    ['Filosofi Teras', 'Henry Manampiring'],
                ],
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Counter & ISBN
        |--------------------------------------------------------------------------
        */

        $counter = 1;

        $isbnMap = $this->isbnMap();

        /*
        |--------------------------------------------------------------------------
        | Proses Seeder
        |--------------------------------------------------------------------------
        */

        foreach ($definitions as $categoryName => $subcategories) {

            /*
            |--------------------------------------------------------------------------
            | Cari kategori
            |--------------------------------------------------------------------------
            */

            $category = Category::where('name', $categoryName)->first();

            if (!$category) {
                $this->command->error(
                    "Kategori '{$categoryName}' tidak ditemukan."
                );

                continue;
            }

            foreach ($subcategories as $subcategoryName => $books) {

                /*
                |--------------------------------------------------------------------------
                | Cari subkategori
                |--------------------------------------------------------------------------
                */

                $subcategory = Subcategory::where('category_id', $category->id)
                    ->where('name', $subcategoryName)
                    ->first();

                if (!$subcategory) {
                    $this->command->error(
                        "Subkategori '{$subcategoryName}' pada kategori '{$categoryName}' tidak ditemukan."
                    );

                    continue;
                }

                foreach ($books as [$title, $author]) {

                    /*
                    |--------------------------------------------------------------------------
                    | ISBN
                    |--------------------------------------------------------------------------
                    */

                    $isbn = $isbnMap[$counter - 1] ?? null;

                    /*
                    |--------------------------------------------------------------------------
                    | Cover
                    |--------------------------------------------------------------------------
                    */

                    $coverFile = str_pad(
                        $counter,
                        3,
                        '0',
                        STR_PAD_LEFT
                    ) . '.jpg';

                    $cover = $this->ensureCover(
                        $coverFile,
                        $isbn
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Metadata Dasar
                    |--------------------------------------------------------------------------
                    */

                    $publisher = 'Penerbit';

                    $year = date('Y');

                    /*
                    |--------------------------------------------------------------------------
                    | Description / Sinopsis
                    |--------------------------------------------------------------------------
                    */

                    $description = $this->fetchDescription(
                        $isbn,
                        $title,
                        $author
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | DDC
                    |--------------------------------------------------------------------------
                    */

                    $ddc = $this->makeDdc(
                        $categoryName,
                        $subcategoryName,
                        $counter
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | SKU
                    |--------------------------------------------------------------------------
                    */

                    $sku = 'BK-' . str_pad(
                        $counter,
                        5,
                        '0',
                        STR_PAD_LEFT
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Book
                    |--------------------------------------------------------------------------
                    */

                    Book::updateOrCreate(
                        [
                            'sku' => $sku,
                        ],
                        [
                            'judul_buku' => $title,
                            'penulis' => $author,

                            'isbn' => $isbn,

                            'publisher' => $publisher,

                            'publication_year' => $year,

                            'edition' => 'Cetakan ke-1',

                            'ddc' => $ddc,

                            'description' => $description,

                            'cover' => $cover,

                            /*
                            |--------------------------------------------------------------------------
                            | Category
                            |--------------------------------------------------------------------------
                            */

                            'category_id' => $category->id,

                            'subcategory_id' => $subcategory->id,

                            'main_category' => $categoryName,

                            'sub_category' => $subcategoryName,

                            /*
                            |--------------------------------------------------------------------------
                            | Education Level
                            |--------------------------------------------------------------------------
                            */

                            'education_level' =>
                                $categoryName === 'Buku Pelajaran'
                                    ? $subcategoryName
                                    : null,

                            /*
                            |--------------------------------------------------------------------------
                            | Stock
                            |--------------------------------------------------------------------------
                            */

                            'stok' => 5,

                            'status' => 'Tersedia',

                            /*
                            |--------------------------------------------------------------------------
                            | Inventory
                            |--------------------------------------------------------------------------
                            */

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

                            /*
                            |--------------------------------------------------------------------------
                            | Rack
                            |--------------------------------------------------------------------------
                            */

                            'rak' => $rackByCategory[$categoryName] ?? null,
                        ]
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Output
                    |--------------------------------------------------------------------------
                    */

                    $this->command->line(
                        "[{$counter}/90] {$title}" .
                        (
                            $isbn
                                ? " | ISBN: {$isbn}"
                                : " | ISBN: tidak ditemukan"
                        )
                    );

                    $counter++;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Selesai
        |--------------------------------------------------------------------------
        */

        $this->command->info(
            '========================================'
        );

        $this->command->info(
            'BookSeeder selesai.'
        );

        $this->command->info(
            'Total target: 90 judul.'
        );

        $this->command->info(
            'Setiap kelompok utama memiliki data dummy.'
        );

        $this->command->info(
            '========================================'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ISBN MAP
    |--------------------------------------------------------------------------
    */

    private function isbnMap(): array
    {
        return [
            "9786022448778",
            "9780060254926",
            "9786024279363",
            "9786022449089",
            "9786024279165",
            "9786024279172",
            "9780593378172",
            "9786022446316",
            "9780156013987",
            "9780553296983",

            "9780440228929",
            "9780380807345",
            "9780385737944",
            "9780375869020",
            "9780786856299",
            "9780439554930",
            "9780547928227",
            "9780064471190",
            "9780553213133",
            "9780064401883",

            "9780743273565",
            "9780061120084",
            "9780451524935",
            "9780451526342",
            "9780062355300",
            "9780060256654",
            "9780140430721",
            "9780316769488",
            "9786238368174",
            "9781982137274",

            "9780142410318",
            "9780142410370",
            "9780142410387",
            "9780142410110",
            "9780142410363",
            "9780064400558",
            "9780064400565",
            "9780723247708",
            "9780141439761",
            "9780156012195",

            "9781426329317",
            "9781426317076",
            "9781426320601",
            "9781426332741",
            "9781803708438",
            "9781805310217",
            "9781474936620",
            "9781805071101",
            "9780241562710",
            "9780756607661",

            "9780747558194",
            "9780439064873",
            "9780747546290",
            "9780439023481",
            "9780439023498",
            "9780062024039",
            "9780062208128",
            "9780062208112",
            "9781451696196",
            "9786020642772",

            "9780735211292",
            "9780684856094",
            "9780812981605",
            "9780345472328",
            "9780671027032",
            "9780857197689",
            "9780062457714",
            "9781786330895",
            "9781982134488",
            "9781443456623",

            "9786020660578",
            "9789793062792",
            "9789799731233",
            "9786024242756",
            "9786024246945",
            "9789791227555",
            "9789791227005",
            "9780375704024",
            "9780062315007",
            "9780141439518",

            "9780062316097",
            "9780062464316",
            "9780399590504",
            "9781524763138",
            "9781451648539",
            "9780553380163",
            "9780374533557",
            "9780316017930",
            "9780060555665",
            "9786024125189",
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | FETCH DESCRIPTION
    |--------------------------------------------------------------------------
    */

    private function fetchDescription(
        ?string $isbn,
        string $title,
        string $author
    ): string {
        $fallback =
            "Sinopsis belum tersedia untuk {$title} karya {$author}.";

        if (!$isbn) {
            return $fallback;
        }

        try {

            $response = Http::withHeaders([
                'User-Agent' =>
                    'PustakaTigaSerangkai/1.0 Library Demo',

                'Accept' => 'application/json',
            ])
                ->retry(2, 500)
                ->timeout(15)
                ->get(
                    "https://openlibrary.org/isbn/{$isbn}.json"
                );

            if (!$response->successful()) {

                $this->command->warn(
                    "Sinopsis {$isbn}: metadata edition tidak ditemukan."
                );

                return $fallback;
            }

            $edition = $response->json();

            $description = $this->normalizeDescription(
                $edition['description'] ?? null
            );

            if ($description) {

                $this->command->line(
                    "Sinopsis {$isbn}: berhasil diambil."
                );

                return $description;
            }

            /*
            |--------------------------------------------------------------------------
            | Work Endpoint
            |--------------------------------------------------------------------------
            */

            $workKey =
                $edition['works'][0]['key'] ?? null;

            if ($workKey) {

                $workResponse = Http::withHeaders([
                    'User-Agent' =>
                        'PustakaTigaSerangkai/1.0 Library Demo',

                    'Accept' => 'application/json',
                ])
                    ->retry(2, 500)
                    ->timeout(15)
                    ->get(
                        'https://openlibrary.org' .
                        $workKey .
                        '.json'
                    );

                if ($workResponse->successful()) {

                    $work = $workResponse->json();

                    $description =
                        $this->normalizeDescription(
                            $work['description'] ?? null
                        );

                    if ($description) {

                        $this->command->line(
                            "Sinopsis {$isbn}: berhasil diambil dari work."
                        );

                        return $description;
                    }
                }
            }

        } catch (\Throwable $e) {

            $this->command->warn(
                "Sinopsis {$isbn} gagal diambil: " .
                $e->getMessage()
            );
        }

        $this->command->warn(
            "Sinopsis {$isbn}: tidak tersedia, memakai fallback."
        );

        return $fallback;
    }

    /*
    |--------------------------------------------------------------------------
    | NORMALIZE DESCRIPTION
    |--------------------------------------------------------------------------
    */

    private function normalizeDescription(
        mixed $description
    ): ?string {

        if (is_string($description)) {

            $description =
                trim(strip_tags($description));

            return $description !== ''
                ? $description
                : null;
        }

        if (
            is_array($description) &&
            isset($description['value'])
        ) {

            $description =
                trim(
                    strip_tags(
                        (string) $description['value']
                    )
                );

            return $description !== ''
                ? $description
                : null;
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | ENSURE COVER
    |--------------------------------------------------------------------------
    */

    private function ensureCover(
        string $filename,
        ?string $isbn
    ): ?string {

        $path = 'covers/' . $filename;

        /*
        |--------------------------------------------------------------------------
        | Cover sudah ada
        |--------------------------------------------------------------------------
        */

        if (Storage::disk('public')->exists($path)) {

            $this->command->line(
                "Cover {$filename} sudah ada - skip download."
            );

            return $path;
        }

        /*
        |--------------------------------------------------------------------------
        | ISBN kosong
        |--------------------------------------------------------------------------
        */

        if (!$isbn) {

            $this->command->warn(
                "Cover {$filename}: ISBN kosong, tidak bisa download."
            );

            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | URL Open Library
        |--------------------------------------------------------------------------
        */

        $urls = [
            "https://covers.openlibrary.org/b/isbn/{$isbn}-L.jpg?default=false",
            "https://covers.openlibrary.org/b/isbn/{$isbn}-M.jpg?default=false",
        ];

        foreach ($urls as $url) {

            try {

                $response = Http::withHeaders([
                    'User-Agent' =>
                        'PustakaTigaSerangkai/1.0 Library Demo',

                    'Accept' =>
                        'image/jpeg,image/*;q=0.8,*/*;q=0.5',
                ])
                    ->timeout(20)
                    ->get($url);

                $contentType =
                    strtolower(
                        $response->header(
                            'Content-Type',
                            ''
                        )
                    );

                if (
                    $response->successful() &&
                    str_starts_with(
                        $contentType,
                        'image/'
                    )
                ) {

                    Storage::disk('public')->put(
                        $path,
                        $response->body()
                    );

                    $this->command->info(
                        "Cover {$filename} berhasil di-download."
                    );

                    return $path;
                }

            } catch (\Throwable $e) {

                $this->command->warn(
                    "Cover {$filename} gagal di-download: " .
                    $e->getMessage()
                );
            }
        }

        $this->command->warn(
            "Cover {$filename} tidak ditemukan untuk ISBN {$isbn}."
        );

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | DDC
    |--------------------------------------------------------------------------
    |
    | Nomor DDC sederhana untuk kebutuhan katalog/demo.
    | Bukan klaim bahwa seluruh nomor di bawah adalah DDC resmi.
    |
    |--------------------------------------------------------------------------
    */

    private function makeDdc(
        string $category,
        string $subcategory,
        int $counter
    ): string {

        $base = match ($category) {

            /*
            |--------------------------------------------------------------------------
            | Buku Pelajaran
            |--------------------------------------------------------------------------
            */

            'Buku Pelajaran' => match ($subcategory) {

                'SD/MI' =>
                    '372',

                'SMP/MTs' =>
                    '373',

                'SMA/SMK/MA/SMAK/MAK' =>
                    '375',

                'TK' =>
                    '372',

                'Referensi' =>
                    '370',

                default =>
                    '370',
            },

            /*
            |--------------------------------------------------------------------------
            | Buku Umum
            |--------------------------------------------------------------------------
            */

            'Buku Umum' => match ($subcategory) {

                'Buku Anak' =>
                    '028',

                'Buku Remaja' =>
                    '158',

                'Buku Dewasa' =>
                    '813',

                'Agama' =>
                    '200',

                'Majalah' =>
                    '050',

                'Peta' =>
                    '912',

                'Referensi' =>
                    '001',

                'Ilmu Sosial' =>
                    '300',

                'Ilmu Murni/Sains' =>
                    '500',

                'Bahasa/Sastra' =>
                    '800',

                'Ilmu Terapan' =>
                    '600',

                default =>
                    '000',
            },

            default =>
                '000',
        };

        return $base . '.' . $counter;
    }
}