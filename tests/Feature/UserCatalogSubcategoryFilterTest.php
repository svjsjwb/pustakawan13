<?php

namespace Tests\Feature;

use App\Http\Controllers\UserCatalogController;
use App\Models\Book;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Tests\TestCase;

class UserCatalogSubcategoryFilterTest extends TestCase
{
    use RefreshDatabase;

    private array $subcategories = [
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

    private array $bookTitles = [];

    protected function setUp(): void
    {
        parent::setUp();

        foreach ($this->subcategories as $mainCategory => $subcategories) {
            $category = Category::create([
                'name' => $mainCategory,
                'level' => 1,
            ]);

            foreach ($subcategories as $subCategory) {
                $subcategory = Subcategory::create([
                    'category_id' => $category->id,
                    'name' => $subCategory,
                ]);

                $title = 'Fixture ' . $mainCategory . ' ' . $subCategory;
                $this->bookTitles[$mainCategory][$subCategory] = $title;

                $stock = $mainCategory === 'Buku Umum' && $subCategory === 'Agama' ? 0 : 4;
                Book::create([
                    'judul_buku' => $title,
                    'penulis' => 'Penulis Fixture',
                    'publisher' => 'Penerbit Fixture',
                    'sku' => 'FIX-' . strtoupper(bin2hex(random_bytes(5))),
                    'category_id' => $category->id,
                    'subcategory_id' => $subcategory->id,
                    'main_category' => $mainCategory,
                    'sub_category' => $subCategory,
                    'stok' => $stock,
                ]);
            }
        }
    }

    public function test_each_subcategory_returns_only_its_own_books(): void
    {
        foreach ($this->subcategories as $mainCategory => $subcategories) {
            foreach ($subcategories as $subCategory) {
                $result = $this->catalogJson([
                    'main_category' => $mainCategory,
                    'sub_category' => $subCategory,
                ]);

                $this->assertSame(1, $result['total'], "Unexpected total for {$mainCategory} / {$subCategory}");
                $this->assertStringContainsString($this->bookTitles[$mainCategory][$subCategory], $result['grid_html']);

                foreach ($this->bookTitles as $otherGroup => $otherBooks) {
                    foreach ($otherBooks as $otherSubcategory => $otherTitle) {
                        if ($otherGroup === $mainCategory && $otherSubcategory === $subCategory) {
                            continue;
                        }

                        $this->assertStringNotContainsString($otherTitle, $result['grid_html']);
                    }
                }
            }
        }
    }

    public function test_each_all_group_option_returns_its_complete_group_only(): void
    {
        foreach ($this->subcategories as $mainCategory => $subcategories) {
            $result = $this->catalogJson(['main_category' => $mainCategory]);

            $this->assertSame(count($subcategories), $result['total']);
            $otherGroup = $mainCategory === 'Buku Pelajaran' ? 'Buku Umum' : 'Buku Pelajaran';

            foreach ($this->bookTitles[$mainCategory] as $title) {
                $this->assertStringContainsString($title, $result['grid_html']);
            }
            foreach ($this->bookTitles[$otherGroup] as $title) {
                $this->assertStringNotContainsString($title, $result['grid_html']);
            }
        }
    }

    public function test_reference_subcategories_remain_separate_by_parent_category(): void
    {
        $schoolReference = $this->catalogJson([
            'main_category' => 'Buku Pelajaran',
            'sub_category' => 'Referensi',
        ]);
        $generalReference = $this->catalogJson([
            'main_category' => 'Buku Umum',
            'sub_category' => 'Referensi',
        ]);

        $this->assertSame(1, $schoolReference['total']);
        $this->assertStringContainsString($this->bookTitles['Buku Pelajaran']['Referensi'], $schoolReference['grid_html']);
        $this->assertStringNotContainsString($this->bookTitles['Buku Umum']['Referensi'], $schoolReference['grid_html']);

        $this->assertSame(1, $generalReference['total']);
        $this->assertStringContainsString($this->bookTitles['Buku Umum']['Referensi'], $generalReference['grid_html']);
        $this->assertStringNotContainsString($this->bookTitles['Buku Pelajaran']['Referensi'], $generalReference['grid_html']);
    }

    public function test_empty_subcategory_does_not_fall_back_to_other_books(): void
    {
        $result = $this->catalogJson([
            'main_category' => 'Buku Pelajaran',
            'sub_category' => 'TK yang belum didata',
        ]);

        $this->assertSame(0, $result['total']);
        $this->assertStringContainsString('Tidak Ada Buku Ditemukan', $result['grid_html']);
    }

    public function test_subcategory_combines_with_search_status_and_sort(): void
    {
        $result = $this->catalogJson([
            'main_category' => 'Buku Umum',
            'sub_category' => 'Agama',
            'search' => 'Fixture Buku Umum Agama',
            'status' => 'habis',
            'sort' => 'az',
        ]);

        $this->assertSame(1, $result['total']);
        $this->assertStringContainsString($this->bookTitles['Buku Umum']['Agama'], $result['grid_html']);
    }

    public function test_get_request_without_filters_shows_all_books(): void
    {
        $request = Request::create('/user/catalog', 'GET');
        $view = app(UserCatalogController::class)->index($request);

        $this->assertInstanceOf(View::class, $view);
        $this->assertSame('', $view->getData()['mainCategory']);
        $this->assertSame('', $view->getData()['subCategory']);
        $this->assertSame(array_sum(array_map('count', $this->subcategories)), $view->getData()['books']->total());
        $this->assertStringContainsString($this->bookTitles['Buku Pelajaran']['TK'], $view->render());
        $this->assertStringContainsString($this->bookTitles['Buku Umum']['Referensi'], $view->render());
    }

    public function test_get_request_with_category_filters_shows_only_filtered_books(): void
    {
        $request = Request::create('/user/catalog', 'GET', [
            'main_category' => 'Buku Pelajaran',
            'sub_category' => 'TK',
        ]);
        $view = app(UserCatalogController::class)->index($request);

        $this->assertInstanceOf(View::class, $view);
        $this->assertSame('Buku Pelajaran', $view->getData()['mainCategory']);
        $this->assertSame('TK', $view->getData()['subCategory']);
        $this->assertSame(1, $view->getData()['books']->total());
        $this->assertStringContainsString($this->bookTitles['Buku Pelajaran']['TK'], $view->render());
        $this->assertStringNotContainsString($this->bookTitles['Buku Umum']['Referensi'], $view->render());
    }

    private function catalogJson(array $filters): array
    {
        $request = Request::create('/user/catalog', 'GET', $filters + ['ajax' => '1']);
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        return app(UserCatalogController::class)->index($request)->getData(true);
    }
}
