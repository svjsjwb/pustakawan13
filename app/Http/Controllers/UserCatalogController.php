<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Reservation;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UserCatalogController extends Controller
{
    private array $catalogHierarchy = [
        'Buku Pelajaran' => [
            'TK' => 'TK',
            'SD/MI' => 'SD/MI',
            'SMP/MTs' => 'SMP/MTs',
            'SMA/SMK/MA/SMAK/MAK' => 'SMA/SMK/MA/SMAK/MAK',
            'Referensi' => 'Referensi',
        ],

        'Buku Umum' => [
            'Agama' => 'Agama',
            'Majalah' => 'Majalah',
            'Peta' => 'Peta',
            'Referensi' => 'Referensi',
            'Buku Anak' => 'Buku Anak',
            'Buku Remaja' => 'Buku Remaja',
            'Buku Dewasa' => 'Buku Dewasa',
            'Ilmu Sosial' => 'Ilmu Sosial',
            'Ilmu Murni/Sains' => 'Ilmu Murni/Sains',
            'Bahasa/Sastra' => 'Bahasa/Sastra',
            'Ilmu Terapan' => 'Ilmu Terapan',
        ],
    ];

    private array $catalogSynopses = [
        'Matematika Ceria Kelas 4 SD' => 'Buku ini mengajak siswa kelas 4 SD memahami bilangan, operasi hitung, pecahan, pengukuran, geometri, dan penyajian data melalui contoh yang dekat dengan kehidupan sehari-hari. Penjelasan bertahap, ilustrasi, serta latihan kontekstual membantu pembaca membangun ketelitian, keberanian memecahkan masalah, dan dasar penalaran matematika yang kuat.',

        'Ilmu Pengetahuan Alam dan Sosial (IPAS) SD' => 'IPAS memperkenalkan hubungan antara makhluk hidup, benda, energi, lingkungan, masyarakat, dan ruang tempat manusia tinggal. Melalui kegiatan pengamatan dan pertanyaan inkuiri, siswa belajar menghubungkan konsep sains dengan geografi serta kehidupan sosial di Indonesia, sekaligus melatih rasa ingin tahu dan kepedulian terhadap lingkungan.',

        'Bahasa Indonesia: Pintar Membaca & Menulis SD' => 'Buku ini mendampingi siswa SD mengembangkan kemampuan membaca pemahaman, menemukan gagasan utama, memperkaya kosakata, dan menulis kalimat hingga paragraf sederhana. Materinya disusun melalui teks dan latihan yang beragam agar pembaca mampu menyampaikan pikiran dengan runtut, memahami informasi secara kritis, serta menikmati kegiatan literasi.',

        'Pendidikan Pancasila & Kewarganegaraan SD' => 'Materi buku membahas nilai Pancasila, aturan hidup bersama, hak dan kewajiban, keberagaman, serta semangat gotong royong dalam lingkungan keluarga dan sekolah. Kisah keteladanan dan situasi sehari-hari membantu siswa memahami sikap sebagai warga negara yang jujur, bertanggung jawab, menghargai perbedaan, dan mencintai Indonesia.',

        'Matematika Terpadu untuk SMP/MTs Kelas VII' => 'Buku ini membahas bilangan, aljabar, persamaan, perbandingan, geometri, serta penyajian data untuk siswa kelas VII. Setiap topik disertai penjelasan konsep dan pemecahan masalah bertahap, sehingga pembaca dapat memahami alasan di balik suatu prosedur, mengerjakan latihan dengan sistematis, dan mempersiapkan diri menghadapi materi matematika berikutnya.',

        'IPA Terpadu: Fisika & Biologi SMP' => 'Buku ini menghubungkan konsep fisika dan biologi dengan fenomena yang ditemui siswa dalam kehidupan sehari-hari, seperti gerak, energi, materi, makhluk hidup, dan lingkungan. Kegiatan pengamatan serta eksperimen sederhana mendorong pembaca menyusun pertanyaan, membaca bukti, dan menarik kesimpulan berdasarkan cara berpikir ilmiah.',

        'English in Focus for Junior High School' => 'Buku teks ini melatih kemampuan menyimak, berbicara, membaca, dan menulis bahasa Inggris melalui dialog, teks pendek, kosakata, serta latihan tata bahasa yang bertahap. Tema pembelajarannya dekat dengan kehidupan remaja, sehingga pembaca dapat memperluas ungkapan sehari-hari, meningkatkan kepercayaan diri berkomunikasi, dan memahami teks sederhana.',

        'Ilmu Pengetahuan Sosial: Dinamika Kehidupan Bangsa' => 'Pembahasan buku mencakup interaksi antarruang, kondisi sosial budaya Asia Tenggara, sejarah perkembangan bangsa, serta kegiatan ekonomi dan perubahan masyarakat Indonesia. Dengan melihat hubungan antara peristiwa, wilayah, dan keputusan manusia, pembaca memperoleh dasar untuk memahami dinamika kehidupan bangsa secara kritis dan menghargai keberagaman.',

        'Fisika Lanjutan untuk SMA/MA Kelas XI' => 'Buku ini mengupas mekanika fluida, suhu dan termodinamika, gelombang mekanik, serta optika geometri dengan pendekatan konsep dan perhitungan. Contoh penerapan membantu siswa mengaitkan rumus dengan gejala nyata, sementara latihan bertingkat melatih penalaran kuantitatif dan kesiapan menghadapi evaluasi sekolah maupun seleksi perguruan tinggi.',

        'Biologi Sel & Genetika Siswa SMA' => 'Materi buku berfokus pada struktur dan fungsi sel, DNA, sintesis protein, pembelahan sel, mutasi, serta prinsip pewarisan sifat. Penjelasan konsep dan contoh persilangan membantu pembaca memahami bagaimana informasi genetik bekerja dari tingkat molekul hingga organisme, sekaligus membangun dasar untuk mempelajari bioteknologi dan kesehatan.',

        'Kimia Stoikiometri & Struktur Atom SMA' => 'Buku ini menjelaskan perkembangan model atom, konfigurasi elektron, ikatan kimia, konsep mol, persamaan reaksi, stoikiometri, dan reaksi redoks. Uraian langkah demi langkah serta latihan perhitungan membantu siswa menghubungkan partikel mikroskopis dengan perubahan zat yang diamati, meningkatkan ketelitian, dan memecahkan soal kimia secara terstruktur.',

        'Kalkulus & Matematika Peminatan SMA' => 'Pembahasan mencakup limit fungsi, turunan, integral, fungsi trigonometri, dan penerapannya untuk menganalisis perubahan serta luas daerah. Buku ini menuntun pembaca dari pemahaman definisi menuju strategi penyelesaian soal, sehingga siswa dapat mengembangkan penalaran simbolik dan mempersiapkan kemampuan matematika untuk studi lanjut.',

        'Ekonomi & Akuntansi Keuangan SMA' => 'Buku ini membahas konsep kebutuhan dan kelangkaan, kegiatan ekonomi, kebijakan fiskal, serta pencatatan siklus akuntansi pada perusahaan jasa dan dagang. Contoh transaksi dan latihan laporan keuangan membantu pembaca memahami cara informasi ekonomi digunakan untuk mengambil keputusan, sekaligus melatih kecermatan membaca dan menyusun data keuangan.',

        'Petualangan Si Kancil di Hutan Ajaib' => 'Si Kancil menghadapi berbagai persoalan di hutan ajaib ketika beberapa hewan di sekitarnya berada dalam bahaya. Dengan kecerdikan, keberanian, dan bantuan para sahabat, ia mencari jalan keluar tanpa mengandalkan kekuatan semata. Cerita ini mengajak pembaca menikmati petualangan sambil belajar tentang kerja sama, kepedulian, dan tanggung jawab.',

        'Sahabat dari Bintang Kecil' => 'Seorang anak menemukan sahabat tak biasa dari sebuah bintang kecil dan perlahan belajar memahami perbedaan dunia mereka. Pertemuan itu menghadirkan petualangan imajinatif tentang rasa ingin tahu, kepercayaan, dan perpisahan yang hangat tanpa menghilangkan keceriaan khas cerita anak. Pembaca diajak menghargai persahabatan serta keberagaman makhluk hidup.',

        'Kumpulan Dongeng Klasik Nusantara' => 'Kumpulan ini menghadirkan kembali kisah Malin Kundang, Danau Toba, Keong Mas, Timun Mas, dan cerita daerah lainnya dalam bahasa yang mudah diikuti anak. Setiap dongeng memperkenalkan latar budaya dan tokoh dengan pilihan moral yang berbeda, sehingga pembaca dapat menikmati cerita sekaligus mengenal nilai kejujuran, keberanian, dan akibat dari keserakahan.',

        'Kisah Abu Nawas Penuh Hikmah' => 'Buku ini berisi kisah-kisah jenaka Abu Nawas yang sering menghadapi persoalan rumit dengan kecerdikan dan jawaban tak terduga. Di balik humor, setiap cerita menyampaikan pelajaran tentang keadilan, kesabaran, dan kemampuan melihat masalah dari sudut pandang berbeda. Pembaca memperoleh hiburan sekaligus bahan renungan yang ringan dan bermakna.',

        'Milo Si Kucing Pemberani' => 'Milo adalah kucing kecil yang harus berani keluar dari kebiasaannya ketika sebuah kejadian di lingkungan rumah membutuhkan pertolongan. Dalam perjalanan sederhana itu, ia belajar mengenali rasa takut, meminta bantuan, dan mencoba langkah baru. Ilustrasi dan alur yang ringan membantu pembaca anak memahami arti keberanian, empati, serta kepedulian kepada teman.',

        'Dino Si Dinosaurus Ramah' => 'Dino dikenal ramah, tetapi ia masih perlu belajar bahwa bermain bersama berarti mau berbagi dan mendengarkan teman. Ketika terjadi persoalan karena mainan yang diperebutkan, Dino mencari cara untuk memperbaiki hubungan tanpa kehilangan keceriaan. Cerita bergambar ini membantu anak mengenal empati, bergiliran, dan menyelesaikan konflik dengan sikap baik.',

        'Ensiklopedia Bergambar: Mengenal Hewan Laut' => 'Ensiklopedia ini memperkenalkan paus biru, hiu, penyu, terumbu karang, serta makhluk yang hidup di laut dalam melalui fakta singkat dan ilustrasi yang menarik. Pembaca mengenal habitat, ciri, dan peran setiap hewan dalam ekosistem laut, lalu diajak memahami pentingnya menjaga kebersihan laut dan melindungi keanekaragaman hayati.',

        'Eksperimen Sains Seru di Rumah' => 'Buku ini memandu anak melakukan percobaan aman menggunakan bahan yang mudah ditemukan di rumah, seperti air, kertas, garam, dan bahan dapur lainnya. Setiap kegiatan menjelaskan alat, langkah, hasil pengamatan, dan alasan ilmiahnya secara sederhana. Pembaca belajar membuat dugaan, mencatat perubahan, dan menemukan bahwa sains hadir dalam keseharian.',

        'Langit di Balik Jendela Sekolah' => 'Lima siswa SMA dengan kepribadian berbeda membentuk sebuah kelompok musik dan berusaha mempertahankan impian mereka di tengah tugas sekolah, keraguan, serta perbedaan pilihan. Persahabatan mereka diuji oleh ambisi dan perubahan hubungan, tetapi prosesnya memperlihatkan arti saling mendukung, berani berkarya, dan tumbuh bersama.',

        'Negeri Lima Menara' => 'Alif meninggalkan kampung halaman untuk belajar di sebuah pesantren bersama sahabat-sahabat dari berbagai daerah. Kehidupan asrama, disiplin, persahabatan, dan cita-cita mereka membentuk perjalanan penuh tantangan yang digerakkan oleh keyakinan bahwa usaha sungguh-sungguh membuka jalan. Novel ini mengangkat pendidikan, persaudaraan, dan keberanian mengejar mimpi tanpa membocorkan akhir kisah.',

        'Hujan' => 'Di masa depan setelah bencana besar, Lail menjalani sesi terapi untuk menata kembali ingatan tentang hujan, kehilangan, persahabatan, dan cinta yang membentuk hidupnya. Perjalanan emosionalnya mempertemukan pertanyaan tentang melupakan dan menerima masa lalu dengan perubahan teknologi. Pembaca diajak merenungkan ketabahan, pilihan manusia, serta makna melepaskan.',

        'Dilan 1990: Dia adalah Dilanku' => 'Milea menceritakan kepindahannya ke Bandung dan pertemuannya dengan Dilan, siswa yang unik, puitis, serta sulit ditebak. Hubungan mereka tumbuh melalui percakapan ringan, perhatian kecil, dan dinamika kehidupan SMA pada era 1990-an. Novel ini menawarkan kisah remaja tentang ketertarikan, pertemanan, dan pilihan hati dengan suasana humoris.',

        'Si Juki: Berani Gagal' => 'Si Juki menghadapi berbagai situasi sehari-hari yang kocak, canggung, dan sering kali tidak berjalan sesuai rencana. Di balik komedi dan sindirannya, cerita ini membahas tekanan untuk selalu berhasil serta pentingnya belajar dari kesalahan. Pembaca remaja dapat menikmati humor sekaligus melihat kegagalan sebagai bagian wajar dari proses bertumbuh.',

        'Gundala Putra Petir Reborn' => 'Gundala kembali sebagai pahlawan yang berhadapan dengan ancaman dan ketidakadilan di tengah kehidupan kota yang penuh persoalan. Aksi, kekuatan petir, dan pilihan sulit sang tokoh menjadi penggerak cerita, sementara konflik mempertemukan keberanian pribadi dengan tanggung jawab sosial. Komik ini menyajikan pahlawan super Indonesia dalam visual modern tanpa menghilangkan nilai kepedulian.',

        'The 7 Habits of Highly Effective Teens' => 'Buku pengembangan diri ini menerjemahkan tujuh kebiasaan penting ke dalam persoalan yang sering dihadapi remaja, seperti menetapkan tujuan, mengatur waktu, bekerja sama, dan menjaga keseimbangan hidup. Contoh praktis serta latihan refleksi membantu pembaca membangun disiplin, komunikasi, dan relasi yang sehat secara bertahap sesuai situasi mereka.',

        'Temukan Bakat & Passion-mu Sejak Muda' => 'Buku ini membantu remaja mengenali minat, kekuatan, nilai pribadi, dan pengalaman yang dapat menjadi petunjuk dalam memilih kegiatan maupun arah karier. Pembahasannya mendorong pembaca mencoba, mengevaluasi, dan mengembangkan kemampuan tanpa terjebak pada label bakat. Hasilnya, pembaca memperoleh kerangka yang lebih realistis untuk merancang langkah masa depan.',

        'Laut Bercerita' => 'Kisah ini mengikuti Biru Laut dan kawan-kawannya yang terlibat dalam gerakan mahasiswa pada masa penuh tekanan politik, lalu memperlihatkan dampak kehilangan bagi keluarga yang ditinggalkan. Dengan sudut pandang yang intim, novel membahas keberanian, ingatan, dan pencarian keadilan tanpa mengungkap seluruh rahasia cerita. Pembaca diajak memahami sisi manusiawi sebuah sejarah.',

        'Cantik Itu Luka' => 'Dewi Ayu dan keluarganya menjalani kehidupan yang dibentuk oleh sejarah panjang, kekerasan, perubahan kekuasaan, serta mitos di kota pesisir Halimunda. Realisme magis memadukan kisah keluarga, cinta, tubuh, dan luka sosial dalam narasi yang berlapis. Novel ini mengajak pembaca menelusuri bagaimana masa lalu terus memengaruhi pilihan dan nasib generasi berikutnya.',

        'Sapiens: Riwayat Singkat Umat Manusia' => 'Buku ini menelusuri perjalanan Homo sapiens dari revolusi kognitif, pertanian, dan industri hingga dunia modern melalui pertanyaan tentang bahasa, kepercayaan, ekonomi, dan kekuasaan. Dengan menghubungkan sejarah, biologi, dan budaya, pembaca memperoleh sudut pandang luas untuk memahami bagaimana kerja sama manusia membentuk masyarakat serta tantangan masa depannya.',

        'Filosofi Teras: Stoikisme Kuno untuk Mental Tangguh' => 'Buku ini memperkenalkan prinsip stoikisme dan penerapannya pada kecemasan, kemarahan, penilaian orang lain, serta situasi yang berada di luar kendali. Gagasan filsafat dijelaskan melalui contoh kehidupan modern dan latihan membedakan fakta dari interpretasi. Pembaca memperoleh cara berpikir yang lebih jernih untuk merespons masalah dengan tenang dan bertanggung jawab.',

        'The Psychology of Money' => 'Buku ini membahas bahwa keputusan keuangan tidak hanya ditentukan oleh pengetahuan, tetapi juga pengalaman, kebiasaan, emosi, dan cara seseorang memandang risiko. Melalui sejumlah pelajaran singkat, pembaca diajak memahami tabungan, investasi, kekayaan, kesabaran, serta kecukupan. Manfaat utamanya adalah kerangka berpikir yang lebih sehat dalam mengelola uang jangka panjang.',

        'Zero to One: Catatan Membangun Masa Depan' => 'Buku ini mengajak pembaca memahami perbedaan antara mengulang sesuatu yang sudah ada dan menciptakan nilai baru. Pembahasannya mencakup inovasi, visi perusahaan, persaingan, tim, monopoli, serta peran teknologi dalam membangun masa depan. Pembaca memperoleh pertanyaan strategis untuk menilai peluang dan merancang bisnis yang memiliki keunikan nyata.',

        'Kecerdasan Buatan dan Transformasi Digital' => 'Buku ini menjelaskan peran kecerdasan buatan, data besar, dan pembelajaran mesin dalam perubahan proses bisnis serta layanan publik. Pembahasan mencakup peluang, penerapan, kebutuhan infrastruktur, dan tantangan etika yang perlu diperhatikan organisasi. Pembaca memperoleh gambaran menyeluruh untuk memahami strategi transformasi digital secara realistis dan bertanggung jawab.',

        'Arsitektur Web Modern dan Cloud Computing' => 'Buku ini membahas cara merancang aplikasi web yang dapat berkembang melalui arsitektur terdistribusi, layanan mikro, API, container, dan infrastruktur cloud. Materi menghubungkan keputusan teknis dengan kebutuhan keandalan, keamanan, pemantauan, serta deployment. Pembaca mendapatkan dasar untuk memilih pola arsitektur dan membangun sistem yang lebih mudah dirawat.',

        'Pedagogi Kritis: Pendidikan Pembebasan Abad 21' => 'Buku ini membahas pendidikan sebagai proses dialog yang membantu manusia membaca dunia dan ikut mengubahnya, bukan sekadar menerima pengetahuan secara pasif. Gagasan tentang relasi guru dan peserta didik, kesadaran kritis, serta pengalaman sosial dikaji untuk membangun pembelajaran yang memanusiakan. Pembaca memperoleh landasan reflektif untuk menilai praktik pendidikan.',

        'Metode Penelitian Pendidikan Kuantitatif & Kualitatif' => 'Buku ini membahas perumusan masalah, kajian teori, desain penelitian, teknik pengumpulan data, analisis kuantitatif dan kualitatif, hingga penyusunan laporan ilmiah. Perbandingan pendekatan membantu pembaca memilih metode yang sesuai dengan pertanyaan penelitian dan menjaga validitas temuan. Materinya bermanfaat bagi mahasiswa, guru, dan peneliti yang menyiapkan karya akademik.',
    ];

    public function index(Request $request)
    {
        // Ambil data kategori beserta subkategori dari database
        $categories = Category::with('subcategories')
            ->orderByRaw("
                CASE name
                    WHEN 'Buku Pelajaran' THEN 1
                    WHEN 'Buku Umum' THEN 2
                    ELSE 99
                END
            ")
            ->get();

        // Eager load category, subcategory, dan copies.shelf.zone.floor untuk eliminasi N+1
        $query = Book::with([
            'category',
            'subcategory',
            'copies.shelf.zone.floor'
        ]);

        // ── Search ────────────────────────────────────────────
        $search = trim((string) $request->input('search', ''));

        if ($search !== '') {
            $query->where('judul_buku', 'ilike', "{$search}%");
        }

        // ── Filter Kategori & Subkategori berbasis ID ─────────
        $categoryId = $request->input('category_id') ?? $request->input('category');
        $subcategoryId = $request->input('subcategory_id') ?? $request->input('subcategory');

        // Kompatibilitas mundur dengan parameter teks legacy (jika ada yang mengakses URL lama)
        if (!$categoryId && $request->filled('main_category')) {
            $cat = $categories->firstWhere('name', $request->input('main_category'));
            $categoryId = $cat?->id;
        }
        if (!$subcategoryId && $request->filled('sub_category')) {
            $subQuery = Subcategory::where('name', $request->input('sub_category'));
            if ($categoryId) {
                $subQuery->where('category_id', $categoryId);
            }
            $sub = $subQuery->first();
            $subcategoryId = $sub?->id;
        }

        $categoryId = is_numeric($categoryId) ? (int) $categoryId : null;
        $subcategoryId = is_numeric($subcategoryId) ? (int) $subcategoryId : null;

        $selectedCategory = $categoryId ? $categories->firstWhere('id', $categoryId) : null;
        $selectedSubcategory = ($selectedCategory && $subcategoryId)
            ? $selectedCategory->subcategories->firstWhere('id', $subcategoryId)
            : ($subcategoryId ? Subcategory::find($subcategoryId) : null);

        $mainCategory = $selectedCategory?->name ?? '';
        $subCategory = $selectedSubcategory?->name ?? '';
        $educationLevel = $request->input('education_level');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($subcategoryId) {
            $query->where('subcategory_id', $subcategoryId);
        }

        // ── Filter status ─────────────────────────────────────
        $status = $request->input('status');

        if ($status) {
            if ($status === 'tersedia') {
                $query->where('stok', '>', 0);
            } elseif ($status === 'habis') {
                $query->where('stok', '<=', 0);
            }
        }

        // ── Sorting ───────────────────────────────────────────
        $sort = $request->input('sort', 'terbaru');

        match ($sort) {
            'az'      => $query->orderBy('judul_buku', 'asc'),
            'za'      => $query->orderBy('judul_buku', 'desc'),
            'terlama' => $query->orderByRaw('created_at ASC NULLS LAST, id ASC'),

            default => $query
                ->orderByRaw(
                    "CASE WHEN NULLIF(TRIM(cover), '') IS NOT NULL THEN 0 ELSE 1 END"
                )
                ->orderByRaw('created_at DESC NULLS LAST, id DESC'),
        };

        $books = $query->paginate(20)->withQueryString();

        $books->getCollection()->transform(function (Book $book) {

            // AMBIL LOKASI LANTAI DAN ZONA DARI BOOK COPIES
            $lokasiRak = $book->copies
                ->map(function ($copy) {
                    $shelf = $copy->shelf;
                    $zone = $shelf?->zone;
                    $floor = $zone?->floor;

                    if (!$shelf || !$zone || !$floor) {
                        return null;
                    }

                    // Hanya lantai 1 TSSO dan lantai 3 TSPM
                    $validLocation =
                        ($floor->floor_number == 1 && $zone->code === 'TSSO') ||
                        ($floor->floor_number == 3 && $zone->code === 'TSPM');

                    if (!$validLocation) {
                        return null;
                    }

                    return $floor->name . ' / ' . $zone->code;
                })
                ->filter()
                ->unique()
                ->values();

            $book->rak = $lokasiRak->isNotEmpty()
                ? $lokasiRak->implode(', ')
                : null;


            // SYNOPSIS BUKU (TETAP SEPERTI KODE AWAL)
            if (isset($this->catalogSynopses[$book->title])) {
                $book->description = $this->catalogSynopses[$book->title];
            } elseif (
                !$book->description ||
                str_starts_with($book->description, 'Deskripsi buku dummy')
            ) {
                $category =
                    $book->category?->name
                    ?? $book->main_category
                    ?? 'koleksi umum';

                $book->description =
                    "Buku berjudul {$book->title} membahas topik utama "
                    . "yang berkaitan dengan {$category} melalui uraian dan "
                    . "contoh yang disesuaikan dengan tema koleksi. Pembaca "
                    . "dapat memperoleh pemahaman dasar, memperluas wawasan, "
                    . "serta menggunakan gagasan yang dibahas sebagai bahan "
                    . "belajar dan rujukan awal sesuai kebutuhan.";
            }

            return $book;
        });

        $isAjax = $request->ajax() || $request->input('ajax') == '1' || $request->wantsJson();

        // ── Favorit session & status user ─────────────────────
        $favoriteIds = session('user_favorites', []);

        $reservedBookIds = [];
        $borrowedBookIds = [];

        if (Auth::check() && Auth::user()->email) {

            $user = Auth::user();
            $member = $user->member;

            if ($member) {

                $reservedBookIds = Reservation::where(
                    'member_id',
                    $member->id
                )
                    ->whereIn('status', [
                        'menunggu',
                        'disetujui',
                        'siap_diambil',
                    ])
                    ->pluck('book_id')
                    ->all();

                $borrowedBookIds = DB::table('borrowing_details')
                    ->join('borrowings', 'borrowing_details.borrowing_id', '=', 'borrowings.id')
                    ->where('borrowings.member_id', $member->id)
                    ->whereIn('borrowings.status', ['dipinjam', 'diperpanjang', 'terlambat'])
                    ->pluck('borrowing_details.book_id')
                    ->unique()
                    ->values()
                    ->all();
            }
        }

        // ── AJAX Response for Realtime Filter & Paginasi (Bypass Query Stat) ──
        if ($isAjax) {
            return response()->json([
                'success' => true,

                'total' => $books->total(),

                'category_id'    => $categoryId,
                'subcategory_id' => $subcategoryId,
                'main_category'  => $mainCategory,
                'sub_category'   => $subCategory,
                'status'         => $status,
                'sort'           => $sort,

                'grid_html' => view(
                    'user.partials.catalog-grid',
                    [
                        'books' => $books,
                        'favoriteIds' => $favoriteIds,
                        'reservedBookIds' => $reservedBookIds,
                        'borrowedBookIds' => $borrowedBookIds,
                    ]
                )->render(),

                'meta_html' => view(
                    'user.partials.catalog-meta',
                    [
                        'books' => $books,
                        'search' => $search,
                        'selectedCategory' => $selectedCategory,
                        'selectedSubcategory' => $selectedSubcategory,
                        'mainCategory' => $mainCategory,
                        'subCategory' => $subCategory,
                        'status' => $status,
                        'activeSort' => $sort,
                    ]
                )->render(),
            ]);
        }

        // ── Real counts per category_id & subcategory_id (Hanya untuk Full Page Load) ──
        $subCounts = Cache::remember('catalog_sub_counts_by_id_v2', 600, function () {
            return DB::table('books')
                ->selectRaw('subcategory_id, count(*) as total')
                ->whereNotNull('subcategory_id')
                ->groupBy('subcategory_id')
                ->pluck('total', 'subcategory_id')
                ->all();
        });

        $mainCounts = Cache::remember('catalog_main_counts_by_id_v2', 600, function () {
            return DB::table('books')
                ->selectRaw('category_id, count(*) as total')
                ->whereNotNull('category_id')
                ->groupBy('category_id')
                ->pluck('total', 'category_id')
                ->all();
        });

        // ── Statistik perpustakaan untuk hero section ─────────
        $stats = Cache::remember('catalog_hero_stats', 300, function () {
            return [
                'total_titles'    => Book::count(),
                'available_count' => Book::where('stok', '>', 0)->count(),
                'borrowed_count'  => Borrowing::where('status', 'dipinjam')->count(),
            ];
        });

        return view('user.catalog', [
            'books'                 => $books,
            'categories'            => $categories,
            'selectedCategory'      => $selectedCategory,
            'selectedSubcategory'   => $selectedSubcategory,
            'selectedCategoryId'    => $categoryId ?? '',
            'selectedSubcategoryId' => $subcategoryId ?? '',
            'catalogHierarchy'      => $this->catalogHierarchy,
            'mainCounts'            => $mainCounts,
            'subCounts'             => $subCounts,
            'favoriteIds'           => $favoriteIds,
            'search'                => $search ?? '',
            'mainCategory'          => $mainCategory ?? '',
            'subCategory'           => $subCategory ?? '',
            'educationLevel'        => $educationLevel ?? '',
            'activeCategory'        => $categoryId ?? '',
            'activeSort'            => $sort,
            'status'                => $status ?? '',
            'stats'                 => $stats,
            'reservedBookIds'       => $reservedBookIds,
            'borrowedBookIds'       => $borrowedBookIds,
        ]);
    }
}
