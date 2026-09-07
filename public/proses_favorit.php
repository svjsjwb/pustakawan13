<?php
/**
 * Script: proses_favorit.php
 * Deskripsi: Menambah atau menghapus buku dari daftar favorit (Toggle Favorit)
 * Response: JSON {"status": "success|error", "action": "added|removed", "message": "..."}
 */

// 1. Set Header JSON
header('Content-Type: application/json; charset=utf-8');

// 2. Hanya terima request POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Metode HTTP tidak diizinkan. Gunakan POST.'
    ]);
    exit;
}

// 3. Konfigurasi Database (Sesuaikan dengan kredensial database Anda)
$host     = '127.0.0.1';
$db_name  = 'pustakawan13'; // Sesuaikan nama database
$username = 'root';
$password = '';             // Password default Laragon biasanya kosong
$charset  = 'utf8mb4';

// 4. Koneksi Database menggunakan PDO
try {
    $dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Koneksi database gagal: ' . $e->getMessage()
    ]);
    exit;
}

// 5. Ambil data book_id dan user_id (Mendukung Form-Data, x-www-form-urlencoded, dan Raw JSON)
$book_id = $_POST['book_id'] ?? null;
$user_id = $_POST['user_id'] ?? null;

// Jika dikirim dalam format JSON payload (fetch body JSON)
if ($book_id === null || $user_id === null) {
    $raw_input = file_get_contents('php://input');
    $json_data = json_decode($raw_input, true);
    if (is_array($json_data)) {
        $book_id = $json_data['book_id'] ?? null;
        $user_id = $json_data['user_id'] ?? null;
    }
}

// 6. Validasi Input
if (empty($book_id) || empty($user_id)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Parameter book_id dan user_id wajib diisi!'
    ]);
    exit;
}

// Konversi tipe data ke integer untuk keamanan tambahan
$book_id = (int)$book_id;
$user_id = (int)$user_id;

try {
    // 7. Cek apakah buku sudah difavoritkan oleh user di tabel user_favorit
    $check_stmt = $pdo->prepare("SELECT id FROM user_favorit WHERE user_id = :user_id AND book_id = :book_id LIMIT 1");
    $check_stmt->execute([
        ':user_id' => $user_id,
        ':book_id' => $book_id
    ]);
    $favorit = $check_stmt->fetch();

    if ($favorit) {
        // 8. Jika SUDAH ADA -> Hapus dari favorit (DELETE)
        $delete_stmt = $pdo->prepare("DELETE FROM user_favorit WHERE user_id = :user_id AND book_id = :book_id");
        $delete_stmt->execute([
            ':user_id' => $user_id,
            ':book_id' => $book_id
        ]);

        echo json_encode([
            'status'  => 'success',
            'action'  => 'removed',
            'message' => 'Buku berhasil dihapus dari favorit.'
        ]);
    } else {
        // 9. Jika BELUM ADA -> Tambahkan ke favorit (INSERT)
        $insert_stmt = $pdo->prepare("INSERT INTO user_favorit (user_id, book_id, created_at) VALUES (:user_id, :book_id, NOW())");
        $insert_stmt->execute([
            ':user_id' => $user_id,
            ':book_id' => $book_id
        ]);

        echo json_encode([
            'status'  => 'success',
            'action'  => 'added',
            'message' => 'Buku berhasil ditambahkan ke favorit.'
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Terjadi kesalahan query: ' . $e->getMessage()
    ]);
}
