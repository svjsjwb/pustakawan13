<?php

ini_set('memory_limit', '1024M');
set_time_limit(0);

$pdo = new PDO('mysql:host=127.0.0.1;dbname=pustakawan13', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

echo "Starting import process...\n";

// Disable foreign key checks for clean truncation
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
echo "Clearing book data and related records...\n";
$pdo->exec("TRUNCATE TABLE borrowing_details;");
$pdo->exec("TRUNCATE TABLE borrowings;");
$pdo->exec("TRUNCATE TABLE reservations;");
$pdo->exec("TRUNCATE TABLE book_copies;");
$pdo->exec("TRUNCATE TABLE books;");
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

echo "Dropping unique constraint on books.isbn...\n";
try {
    $pdo->exec("ALTER TABLE books DROP INDEX books_isbn_unique;");
} catch (\Exception $e) {
    // index might not exist
}

try {
    $pdo->exec("ALTER TABLE books MODIFY isbn VARCHAR(40) NULL;");
} catch (\Exception $e) {
    // column already modified
}

try {
    $pdo->exec("ALTER TABLE books ADD INDEX books_isbn_index (isbn);");
} catch (\Exception $e) {
    // index might already exist
}

echo "Tables prepared successfully.\n";

function mapBook($kelompok, $jenis, $title, $ddc) {
    $kUpper = trim(strtoupper($kelompok ?? ''));
    $jUpper = trim(strtoupper($jenis ?? ''));
    $tUpper = trim(strtoupper($title ?? ''));
    $d = trim($ddc ?? '');

    // 1. BUKU PELAJARAN (Cat ID 5)
    if ($kUpper === 'BUKU PELAJARAN' || str_contains($jUpper, 'PENDIDIKAN') || str_contains($jUpper, 'BUKU TK') || str_contains($jUpper, 'KUMPULAN SOAL')) {
        $catId = 5;
        $mainCat = 'Buku Pelajaran';
        $rak = 'B2';

        // TK (Sub ID 11)
        if (str_contains($jUpper, 'TK') || str_contains($jUpper, 'PRESCHOOL') || str_contains($tUpper, ' TK') || str_contains($tUpper, 'PAUD') || str_starts_with($tUpper, 'TK ')) {
            return [$catId, 11, $mainCat, 'TK', 'TK', $rak];
        }

        // Referensi (Sub ID 15)
        if (str_contains($jUpper, 'REFERENSI') || str_contains($jUpper, 'KATALOG') || str_contains($jUpper, 'DEWEY') || str_contains($jUpper, 'KAMUS') || str_contains($jUpper, 'ENSIKLOPEDI')) {
            return [$catId, 15, $mainCat, 'Referensi', 'Referensi', 'B1'];
        }

        // SMA/SMK/MA (Sub ID 14)
        if (
            str_contains($jUpper, 'SMA') || str_contains($jUpper, 'SMK') || str_contains($jUpper, 'MA ') ||
            str_contains($tUpper, 'SMA') || str_contains($tUpper, 'SMK') || str_contains($tUpper, 'SMU') ||
            str_contains($tUpper, ' MA ') || str_ends_with($tUpper, ' MA') || str_contains($tUpper, 'MA/') ||
            str_contains($tUpper, 'KELAS X') || str_contains($tUpper, 'KELAS XI') || str_contains($tUpper, 'KELAS XII') ||
            str_contains($tUpper, 'KLAS X') || str_contains($tUpper, 'KLAS XI') || str_contains($tUpper, 'KLAS XII') ||
            str_contains($tUpper, 'KMA 183')
        ) {
            return [$catId, 14, $mainCat, 'SMA/SMK/MA/SMAK/MAK', 'SMA/SMK/MA/SMAK/MAK', $rak];
        }

        // SMP/MTs (Sub ID 13)
        if (
            str_contains($jUpper, 'SMP') || str_contains($jUpper, 'MTS') ||
            str_contains($tUpper, 'SMP') || str_contains($tUpper, 'MTS') ||
            str_contains($tUpper, 'KELAS VII') || str_contains($tUpper, 'KELAS VIII') || str_contains($tUpper, 'KELAS IX') ||
            str_contains($tUpper, 'KLAS VII') || str_contains($tUpper, 'KLAS VIII') || str_contains($tUpper, 'KLAS IX')
        ) {
            return [$catId, 13, $mainCat, 'SMP/MTs', 'SMP/MTs', $rak];
        }

        // Pendidikan Menengah fallback
        if (str_contains($jUpper, 'MENENGAH')) {
            if (preg_match('/\b(10|11|12|X|XI|XII)\b/i', $title)) {
                return [$catId, 14, $mainCat, 'SMA/SMK/MA/SMAK/MAK', 'SMA/SMK/MA/SMAK/MAK', $rak];
            }
            if (preg_match('/\b(7|8|9|VII|VIII|IX)\b/i', $title)) {
                return [$catId, 13, $mainCat, 'SMP/MTs', 'SMP/MTs', $rak];
            }
            return [$catId, 14, $mainCat, 'SMA/SMK/MA/SMAK/MAK', 'SMA/SMK/MA/SMAK/MAK', $rak];
        }

        // SD / MI (Sub ID 12)
        return [$catId, 12, $mainCat, 'SD/MI', 'SD/MI', $rak];
    }

    // 2. BUKU UMUM (Cat ID 6)
    $catId = 6;
    $mainCat = 'Buku Umum';

    // Agama (Sub ID 16)
    if ($kUpper === 'AGAMA' || str_contains($jUpper, 'AGAMA') || str_contains($jUpper, 'AL-QURAN') || str_contains($jUpper, 'HADIS') || str_starts_with($d, '200') || str_starts_with($d, '297')) {
        return [$catId, 16, $mainCat, 'Agama', null, 'C2'];
    }

    // Majalah (Sub ID 17)
    if ($kUpper === 'MAJALAH' || str_contains($jUpper, 'MAJALAH') || str_contains($jUpper, 'TERBITAN BERSERI') || $d === 'MAJALAH') {
        return [$catId, 17, $mainCat, 'Majalah', null, 'C2'];
    }

    // Peta (Sub ID 18)
    if ($kUpper === 'PETA' || str_contains($jUpper, 'ATLAS') || str_contains($jUpper, 'PETA') || $d === 'ATLAS') {
        return [$catId, 18, $mainCat, 'Peta', null, 'C2'];
    }

    // Referensi (Sub ID 19)
    if ($kUpper === 'REFERENSI' || str_contains($jUpper, 'ENSIKLOPEDI') || str_contains($jUpper, 'KAMUS') || str_contains($jUpper, 'KATALOG') || str_contains($jUpper, 'REFERENSI') || str_starts_with($d, '000') || str_starts_with($d, '030')) {
        return [$catId, 19, $mainCat, 'Referensi', null, 'B1'];
    }

    // Buku Anak (Sub ID 20)
    if ($kUpper === 'BUKU ANAK' || str_contains($jUpper, 'CERITA BERGAMBAR') || str_contains($jUpper, 'KOMIK')) {
        return [$catId, 20, $mainCat, 'Buku Anak', null, 'A1'];
    }

    // Buku Remaja (Sub ID 21)
    if ($kUpper === 'BUKU REMAJA' || str_contains($jUpper, 'REMAJA')) {
        return [$catId, 21, $mainCat, 'Buku Remaja', null, 'A2'];
    }

    // Buku Dewasa (Sub ID 22)
    if ($kUpper === 'BUKU DEWASA' || str_contains($jUpper, 'DEWASA') || str_contains($jUpper, 'KEMAUAN') || str_contains($jUpper, 'ILHAM') || str_contains($jUpper, 'BIOGRAFI')) {
        return [$catId, 22, $mainCat, 'Buku Dewasa', null, 'C1'];
    }

    // Ilmu Sosial (Sub ID 23)
    if ($kUpper === 'ILMU SOSIAL' || str_contains($jUpper, 'SOSIAL') || str_contains($jUpper, 'HUKUM') || str_contains($jUpper, 'POLITIK') || str_contains($jUpper, 'EKONOMI') || str_starts_with($d, '300')) {
        return [$catId, 23, $mainCat, 'Ilmu Sosial', null, 'C2'];
    }

    // Ilmu Murni / Sains (Sub ID 24)
    if ($kUpper === 'ILMU MURNI / SAINS' || str_contains($jUpper, 'SAINS') || str_contains($jUpper, 'MATEMATIKA') || str_contains($jUpper, 'FISIKA') || str_contains($jUpper, 'KIMIA') || str_contains($jUpper, 'BIOLOGI') || str_starts_with($d, '500')) {
        return [$catId, 24, $mainCat, 'Ilmu Murni/Sains', null, 'C2'];
    }

    // Bahasa / Sastra (Sub ID 25)
    if ($kUpper === 'BAHASA / SASTRA' || str_contains($jUpper, 'BAHASA') || str_contains($jUpper, 'SASTRA') || str_contains($jUpper, 'FIKSI') || str_starts_with($d, '400') || str_starts_with($d, '800')) {
        return [$catId, 25, $mainCat, 'Bahasa/Sastra', null, 'C2'];
    }

    // Ilmu Terapan (Sub ID 26)
    if ($kUpper === 'ILMU TERAPAN' || str_contains($jUpper, 'TERAPAN') || str_contains($jUpper, 'MANAJEMEN') || str_contains($jUpper, 'KESEHATAN') || str_contains($jUpper, 'KEDOKTERAN') || str_contains($jUpper, 'TEKNIK') || str_contains($jUpper, 'MASAK') || str_starts_with($d, '600')) {
        return [$catId, 26, $mainCat, 'Ilmu Terapan', null, 'C2'];
    }

    // Fallback: Buku Dewasa (Sub ID 22)
    return [$catId, 22, $mainCat, 'Buku Dewasa', null, 'C1'];
}

function parseVal($val) {
    $v = trim($val);
    if ($v === 'NULL' || $v === '') return null;
    if (str_starts_with($v, "'") && str_ends_with($v, "'")) {
        $v = substr($v, 1, -1);
        // unescape binlog escapes: \' -> '
        $v = str_replace(["\\'", "\\\\"], ["'", "\\"], $v);
        return $v;
    }
    return $v;
}

$dumpFile = 'C:/laragon/binlog_dump.sql';
if (!file_exists($dumpFile)) {
    die("Dump file not found: $dumpFile\n");
}

$f = fopen($dumpFile, 'r');
$batchSize = 1000;
$booksBatch = [];
$copiesBatch = [];
$totalImported = 0;
$now = date('Y-m-d H:i:s');

function flushBatch(PDO $pdo, array &$booksBatch, array &$copiesBatch) {
    if (empty($booksBatch)) return;

    $pdo->beginTransaction();

    // Insert books
    $bookCols = [
        'id', 'judul_buku', 'sku', 'penulis', 'category_id', 'main_category',
        'sub_category', 'education_level', 'subcategory_id', 'stok', 'status',
        'no_iventaris', 'kode_buku', 'ddc', 'rak', 'edition', 'created_at',
        'updated_at', 'isbn', 'publisher', 'publication_year', 'call_number',
        'description', 'cover'
    ];
    $colList = implode(', ', array_map(fn($c) => "`$c`", $bookCols));
    $rowPlaceholder = '(' . implode(', ', array_fill(0, count($bookCols), '?')) . ')';
    $placeholders = implode(', ', array_fill(0, count($booksBatch), $rowPlaceholder));

    $sql = "INSERT INTO books ($colList) VALUES $placeholders";
    $stmt = $pdo->prepare($sql);
    $params = [];
    foreach ($booksBatch as $row) {
        foreach ($bookCols as $col) {
            $params[] = $row[$col];
        }
    }
    $stmt->execute($params);

    // Insert copies
    $copyCols = [
        'book_id', 'shelf_id', 'section', 'side', 'row', 'column',
        'barcode', 'status', 'condition', 'created_at', 'updated_at', 'copy'
    ];
    $copyColList = implode(', ', array_map(fn($c) => "`$c`", $copyCols));
    $copyRowPlaceholder = '(' . implode(', ', array_fill(0, count($copyCols), '?')) . ')';
    $copyPlaceholders = implode(', ', array_fill(0, count($copiesBatch), $copyRowPlaceholder));

    $copySql = "INSERT INTO book_copies ($copyColList) VALUES $copyPlaceholders";
    $copyStmt = $pdo->prepare($copySql);
    $copyParams = [];
    foreach ($copiesBatch as $cRow) {
        foreach ($copyCols as $cCol) {
            $copyParams[] = $cRow[$cCol];
        }
    }
    $copyStmt->execute($copyParams);

    $pdo->commit();

    $booksBatch = [];
    $copiesBatch = [];
}

echo "Reading and importing books from dump...\n";

while (!feof($f)) {
    $line = fgets($f);
    if (str_contains($line, 'INSERT INTO `pustakawan13`.`books`')) {
        $raw = [];
        for ($i = 0; $i < 30; $i++) {
            $l = fgets($f);
            if (preg_match('/###\s+@(\d+)=(.*)/', $l, $m)) {
                $idx = (int)$m[1];
                $raw[$idx] = parseVal($m[2]);
            }
            if (str_contains($l, '###   @24=')) break;
        }

        $id = (int)$raw[1];
        $judul = $raw[2] ?? '-';
        $penulis = $raw[3] ?? '-';
        $isbn = $raw[4] ?? null;
        $publisher = $raw[5] ?? null;
        $year = !empty($raw[6]) ? (int)$raw[6] : null;
        $edition = $raw[7] ?? null;
        $callNumber = $raw[8] ?? null;
        $ddc = $raw[9] ?? null;
        $desc = $raw[10] ?? null;
        $sku = $raw[12] ?? ('BK-' . str_pad($id, 5, '0', STR_PAD_LEFT));
        $noIventaris = $raw[13] ?? ('INV/' . ($year ?? date('Y')) . '/' . str_pad($id, 5, '0', STR_PAD_LEFT));
        $kodeBuku = $raw[14] ?? ('KB-' . str_pad($id, 5, '0', STR_PAD_LEFT));
        $stok = max(1, (int)($raw[15] ?? 1));
        $kelompok = $raw[18] ?? '';
        $jenis = $raw[19] ?? '';

        [$catId, $subcatId, $mainCat, $subCat, $eduLevel, $rak] = mapBook($kelompok, $jenis, $judul, $ddc);

        $booksBatch[] = [
            'id' => $id,
            'judul_buku' => mb_substr($judul, 0, 255),
            'sku' => mb_substr($sku, 0, 100),
            'penulis' => mb_substr($penulis, 0, 255),
            'category_id' => $catId,
            'main_category' => $mainCat,
            'sub_category' => $subCat,
            'education_level' => $eduLevel,
            'subcategory_id' => $subcatId,
            'stok' => $stok,
            'status' => 'Tersedia',
            'no_iventaris' => mb_substr($noIventaris, 0, 50),
            'kode_buku' => mb_substr($kodeBuku, 0, 50),
            'ddc' => mb_substr($ddc ?? '', 0, 30),
            'rak' => $rak,
            'edition' => mb_substr($edition ?? '', 0, 50),
            'created_at' => $now,
            'updated_at' => $now,
            'isbn' => mb_substr($isbn ?? '', 0, 40),
            'publisher' => mb_substr($publisher ?? '', 0, 255),
            'publication_year' => $year,
            'call_number' => mb_substr($callNumber ?? '', 0, 100),
            'description' => $desc,
            'cover' => null,
        ];

        $copiesBatch[] = [
            'book_id' => $id,
            'shelf_id' => 1,
            'section' => 1,
            'side' => 'front',
            'row' => 1,
            'column' => 1,
            'barcode' => $sku . '-C1',
            'status' => 'available',
            'condition' => 'baik',
            'created_at' => $now,
            'updated_at' => $now,
            'copy' => '1',
        ];

        $totalImported++;

        if (count($booksBatch) >= $batchSize) {
            flushBatch($pdo, $booksBatch, $copiesBatch);
            echo "Imported $totalImported books...\n";
        }
    }
}
fclose($f);

if (!empty($booksBatch)) {
    flushBatch($pdo, $booksBatch, $copiesBatch);
}

echo "Successfully imported $totalImported books with copies!\n";
