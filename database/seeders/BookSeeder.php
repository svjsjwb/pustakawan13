<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Rack;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::with('subcategories')
            ->whereNull('parent_id')
            ->get();

        if ($categories->isEmpty()) {
            throw new \Exception("Data Category belum tersedia. Jalankan CategorySeeder & SubcategorySeeder terlebih dahulu.");
        }

        $racks = Rack::pluck('code')->toArray();
        $defaultRack = !empty($racks) ? $racks[0] : 'A1';

        $counter = 1;
        foreach ($categories as $category) {
            foreach ($category->subcategories as $subcategory) {
                // Buat 3 buku per subkategori
                for ($b = 1; $b <= 3; $b++) {
                    $rackCode = !empty($racks) ? $racks[($counter - 1) % count($racks)] : $defaultRack;

                    Book::updateOrCreate(
                        [
                            'sku' => 'BK-' . str_pad($counter, 5, '0', STR_PAD_LEFT),
                        ],
                        [
                            'category_id'    => $category->id,
                            'subcategory_id' => $subcategory->id,
                            'judul_buku'     => "Koleksi {$subcategory->name} Vol. {$b}",
                            'penulis'        => "Penulis {$subcategory->name} {$b}",
                            'stok'           => 5,
                            'status'         => 'Tersedia',
                            'no_iventaris'   => 'INV/' . date('Y') . '/' . str_pad($counter, 4, '0', STR_PAD_LEFT),
                            'kode_buku'      => 'KB-' . strtoupper(substr($category->name, 0, 3)) . '-' . str_pad($counter, 3, '0', STR_PAD_LEFT),
                            'ddc'            => sprintf('%03d.%d', ($counter * 10) % 900 + 100, $b),
                            'rak'            => $rackCode,
                            'edition'        => 'Cetakan ke-' . $b,
                        ]
                    );

                    $counter++;
                }
            }
        }
    }
}

