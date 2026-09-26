# PRD - Filter Kategori Single Active Selection

## 1. Ringkasan

Perbaiki filter kategori pada katalog buku agar hanya satu panel dropdown kategori dapat terbuka pada satu waktu dan hanya satu pilihan kategori yang aktif pada satu waktu. Pilihan aktif dapat berupa `Semua Kategori` atau satu kombinasi kategori utama dan subkategori, misalnya `Buku Pendidikan: SD`.

Dokumen ini menjadi acuan sebelum perubahan kode dilakukan.

## 2. Latar Belakang Masalah

Pada tampilan katalog, lebih dari satu filter kategori dapat terlihat aktif secara bersamaan, contohnya:

- `Buku Pendidikan: SD`
- `Anak: Fiksi`

Kondisi tersebut tidak sesuai dengan perilaku filter yang diharapkan. Parameter filter hanya boleh mewakili satu pasangan kategori utama dan subkategori.

Audit awal menemukan perbedaan nama grup antara server dan JavaScript:

- Hierarki server memakai `Buku Pendidikan`, `Anak`, `Remaja`, dan `Dewasa`.
- Fungsi sinkronisasi JavaScript mereset grup memakai `Pendidikan`, `Anak-Anak`, `Remaja`, dan `Dewasa`.

Akibatnya sebagian wrapper tidak ditemukan saat state UI di-reset. State awal dari server dan state setelah AJAX dapat menampilkan penanda aktif yang berbeda.

## 3. Tujuan

1. Hanya satu kategori aktif pada satu waktu.
2. `Semua Kategori` aktif hanya ketika tidak ada kategori utama dan subkategori aktif.
3. Pemilihan subkategori baru otomatis menggantikan pilihan sebelumnya.
4. State UI, URL, parameter AJAX, dan hasil query tetap sinkron.
5. Refresh, pagination, filter status, sorting, dan browser back/forward tidak menghidupkan kembali pilihan kategori lama.
6. Desain katalog yang ada tetap dipertahankan.

## 4. Di Luar Ruang Lingkup

- Mengubah desain top bar, footer, navbar, warna, font, atau layout katalog.
- Mengubah struktur tabel atau membuat migration baru.
- Mengubah definisi kategori dan subkategori di database.
- Mengubah filter status ketersediaan buku.
- Mengubah mekanisme pencarian atau sorting selain sinkronisasi parameter yang diperlukan.
- Menambahkan filter multi-select.

## 5. Perilaku yang Diinginkan

### 5.0 Panel dropdown kategori

Jika user membuka panel `Buku Pendidikan` lalu membuka panel `Anak`:

- Panel `Buku Pendidikan` langsung tertutup.
- Hanya panel `Anak` yang terbuka.
- `aria-expanded` pada trigger `Buku Pendidikan` menjadi `false`.
- `aria-expanded` pada trigger `Anak` menjadi `true`.
- Membuka panel yang sama kembali menutup panel tersebut.

### 5.1 Kondisi awal

Jika `main_category` dan `sub_category` kosong:

- `Semua Kategori` aktif.
- Semua wrapper kategori utama tidak aktif.
- Semua item subkategori tidak memiliki class aktif.

### 5.2 Memilih subkategori

Jika user memilih `Buku Pendidikan: SD`:

- Hanya wrapper `Buku Pendidikan` yang aktif.
- Hanya item `SD` yang aktif.
- Semua wrapper dan item kategori lain tidak aktif.
- `Semua Kategori` tidak aktif.
- URL memuat `main_category=Buku Pendidikan&sub_category=SD`.

Jika user kemudian memilih `Anak: Fiksi`:

- `Buku Pendidikan: SD` dinonaktifkan sepenuhnya.
- Hanya `Anak: Fiksi` yang aktif.
- Query katalog hanya menggunakan kategori `Anak` dan subkategori `Fiksi`.

### 5.3 Memilih Semua Kategori

Jika user memilih `Semua Kategori`:

- Semua wrapper kategori utama tidak aktif.
- Semua item subkategori tidak aktif.
- Parameter `main_category` dan `sub_category` dihapus dari state URL dan AJAX.
- Semua judul buku ditampilkan, sesuai filter status, pencarian, dan sorting lain yang sedang aktif.

### 5.4 Memilih wrapper kategori utama

Klik wrapper kategori utama hanya membuka atau menutup panel subkategori. Klik tersebut tidak boleh mengaktifkan kategori sampai user memilih subkategori.

## 6. Persyaratan Fungsional

### FR-01 - Single active state

Pada DOM, jumlah wrapper kategori aktif maksimal satu dan jumlah item subkategori aktif maksimal satu.

### FR-02 - Satu pasangan parameter

State kategori hanya boleh memiliki:

- kosong/kosong, atau
- satu `main_category` dan satu `sub_category`.

### FR-03 - State server menjadi sumber awal

State yang dirender server harus langsung konsisten dengan parameter request. Tidak boleh ada markup aktif dari kategori lama ketika halaman dimuat ulang.

### FR-04 - State AJAX konsisten

Setelah `fetchCatalog()` selesai, fungsi sinkronisasi harus membersihkan seluruh state aktif sebelum menetapkan state baru.

### FR-05 - Browser navigation

Back dan forward browser harus mengaktifkan tepat satu kategori sesuai parameter URL yang dipulihkan.

### FR-06 - Filter lain tetap dipertahankan

Perubahan kategori tidak boleh menghapus nilai pencarian, status, sorting, atau halaman kecuali halaman memang perlu kembali ke halaman pertama.

### FR-07 - Data kosong tetap valid

Kategori yang hasilnya nol tetap boleh dipilih dan hanya kategori tersebut yang aktif. UI tidak boleh mengaktifkan kategori lain sebagai fallback.

## 7. Persyaratan Non-Fungsional

- Tidak ada perubahan schema database.
- Tidak ada penghapusan atau perubahan data buku.
- Tidak ada perubahan desain visual selain perubahan state aktif yang memang diperlukan.
- Interaksi desktop dan mobile tetap responsif.
- Tidak menambahkan request AJAX ganda untuk satu klik.
- Class aktif tidak boleh bergantung pada nama grup hardcode yang berbeda dari `catalogHierarchy`.
- Implementasi harus menggunakan identifier yang berasal dari data kategori yang sama dengan markup.

## 8. Rencana Teknis

File utama yang diperkirakan:

- `resources/views/user/catalog.blade.php`

Area yang perlu diperbaiki:

1. `syncCategoryUiState()` harus mereset wrapper berdasarkan elemen DOM atau key yang sama dengan `catalogHierarchy`, bukan daftar nama manual yang tidak identik.
2. `selectSubcategory()` harus selalu mengirim satu pasangan kategori dan subkategori.
3. `fetchCatalog()` harus mengganti state kategori lama secara atomik.
4. Markup server harus tetap menetapkan satu state awal yang konsisten.
5. Test JavaScript atau feature test perlu memeriksa URL, parameter, dan hasil markup jika test harness tersedia.

Pendekatan yang disarankan:

- Gunakan `data-category-group` atau daftar wrapper `.pd-category-wrapper[data-category-group]` sebagai sumber reset.
- Hapus class aktif dari semua wrapper dan item sebelum menetapkan pilihan baru.
- Gunakan `CSS.escape()` atau pencarian elemen berbasis loop ketika mencocokkan nilai kategori yang mengandung spasi atau karakter khusus.
- Pertahankan query backend yang sudah memakai `main_category` dan `sub_category`.

## 9. Kriteria Penerimaan

1. Saat katalog pertama kali dibuka, tepat satu state aktif: `Semua Kategori` atau tidak ada kategori jika desain final memilih state netral.
2. Memilih `Buku Pendidikan: SD` membuat hanya pilihan tersebut aktif.
3. Setelah memilih `Anak: Fiksi`, pilihan `Buku Pendidikan: SD` tidak lagi memiliki class aktif atau label aktif.
4. Memilih `Semua Kategori` membersihkan seluruh pilihan kategori.
5. URL dan request AJAX hanya memiliki satu pasangan kategori.
6. Refresh halaman mempertahankan satu pilihan yang benar.
7. Back/forward browser mempertahankan satu pilihan yang benar.
8. Filter status, sorting, pencarian, dan pagination tetap bekerja.
9. Tidak ada perubahan pada data buku atau schema database.
10. Tidak ada perubahan pada top bar, footer, navbar, atau layout katalog.

## 10. Skenario Pengujian

| Skenario | Hasil yang diharapkan |
|---|---|
| Buka katalog tanpa parameter | Hanya `Semua Kategori` aktif |
| Pilih `Buku Pendidikan: SD` | Hanya `Buku Pendidikan: SD` aktif |
| Pilih `Anak: Fiksi` setelah pilihan sebelumnya | Pilihan sebelumnya hilang, hanya `Anak: Fiksi` aktif |
| Pilih `Semua Kategori` | Semua state kategori dibersihkan |
| Refresh pada `Remaja: Fiksi` | Hanya `Remaja: Fiksi` aktif |
| Back/forward setelah tiga pilihan berbeda | Setiap URL menampilkan satu state yang sesuai |
| Pilih kategori lalu status `Tersedia` | Kedua filter tetap sinkron dan hasil benar |
| Pilih kategori lalu sorting A-Z | Kategori tetap aktif, sorting berubah |
| Kategori tanpa hasil | Hanya kategori tersebut aktif dan empty state tampil |
| Klik cepat dua kategori | State akhir hanya kategori pada klik terakhir |

## 11. Dampak dan Risiko

### Dampak

- Perubahan terbatas pada state UI filter kategori.
- Tidak membutuhkan migration.
- Tidak membutuhkan perubahan model atau data buku.
- Tidak membutuhkan perubahan email, notifikasi, peminjaman, atau reservasi.

### Risiko

- Nama kategori mengandung spasi atau karakter khusus sehingga selector DOM tidak aman jika dibentuk tanpa escaping.
- Markup hasil AJAX harus memiliki struktur filter yang tetap tersedia saat sinkronisasi dipanggil.
- State server dan client dapat berbeda jika salah satu memakai label yang tidak sama.

Mitigasi utama adalah menggunakan nilai `data-*` dari DOM sebagai sumber identifier dan menguji perpindahan antar semua grup kategori.

## 12. Keputusan yang Dibutuhkan

Tidak ada keputusan produk tambahan yang diperlukan untuk implementasi awal. Definisi yang dipakai adalah single-select: satu kategori utama dan satu subkategori aktif, atau `Semua Kategori`.

## 13. Status PRD

Status: Draft siap ditinjau.

Implementasi kode belum dilakukan.