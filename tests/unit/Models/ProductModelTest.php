<?php

namespace Tests\Unit\Models;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\ProductModel;
use App\Models\CategoryModel;

class ProductModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $productModel;
    protected $categoryModel;
    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;
    protected $namespace   = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    protected function createCategory()
    {
        $categoryData = [
            'name'        => 'Makanan',
            'description' => 'Menu makanan utama',
        ];
        return $this->categoryModel->insert($categoryData);
    }

    public function testCanCreateProduct()
    {
        $categoryId = $this->createCategory();

        $data = [
            'category_id' => $categoryId,
            'sku'         => 'PRD001',
            'barcode'     => '1234567890123',
            'name'        => 'Nasi Goreng',
            'unit'        => 'porsi',
            'price'       => 25000,
            'cost_price'  => 15000,
        ];

        $productId = $this->productModel->insert($data);
        
        $this->assertIsNumeric($productId);
        $this->assertGreaterThan(0, $productId);
        
        $product = $this->productModel->find($productId);
        $this->assertEquals('PRD001', $product['sku']);
        $this->assertEquals('Nasi Goreng', $product['name']);
        $this->assertEquals(25000, $product['price']);
    }

    public function testCannotCreateProductWithDuplicateSKU()
    {
        $categoryId = $this->createCategory();

        // Insert first product
        $data1 = [
            'category_id' => $categoryId,
            'sku'         => 'PRD001',
            'barcode'     => '1234567890123',
            'name'        => 'Nasi Goreng',
            'unit'        => 'porsi',
            'price'       => 25000,
            'cost_price'  => 15000,
        ];
        $this->productModel->insert($data1);

        // Try to insert product with same SKU
        $data2 = [
            'category_id' => $categoryId,
            'sku'         => 'PRD001', // Duplicate
            'barcode'     => '9876543210987',
            'name'        => 'Mie Goreng',
            'unit'        => 'porsi',
            'price'       => 20000,
            'cost_price'  => 12000,
        ];
        
        $result = $this->productModel->insert($data2);
        
        $this->assertFalse($result);
        $this->assertNotEmpty($this->productModel->errors());
    }

    public function testCannotCreateProductWithDuplicateBarcode()
    {
        $categoryId = $this->createCategory();

        // Insert first product
        $data1 = [
            'category_id' => $categoryId,
            'sku'         => 'PRD001',
            'barcode'     => '1234567890123',
            'name'        => 'Nasi Goreng',
            'unit'        => 'porsi',
            'price'       => 25000,
            'cost_price'  => 15000,
        ];
        $this->productModel->insert($data1);

        // Try to insert product with same barcode
        $data2 = [
            'category_id' => $categoryId,
            'sku'         => 'PRD002',
            'barcode'     => '1234567890123', // Duplicate
            'name'        => 'Mie Goreng',
            'unit'        => 'porsi',
            'price'       => 20000,
            'cost_price'  => 12000,
        ];
        
        $result = $this->productModel->insert($data2);
        
        $this->assertFalse($result);
        $this->assertNotEmpty($this->productModel->errors());
    }

    public function testCannotCreateProductWithoutRequiredFields()
    {
        $data = [
            'name' => 'Nasi Goreng',
        ];

        $result = $this->productModel->insert($data);
        
        $this->assertFalse($result);
        
        $errors = $this->productModel->errors();
        $this->assertArrayHasKey('category_id', $errors);
        $this->assertArrayHasKey('sku', $errors);
        $this->assertArrayHasKey('barcode', $errors);
    }

    public function testCanReadProduct()
    {
        $categoryId = $this->createCategory();

        $data = [
            'category_id' => $categoryId,
            'sku'         => 'PRD001',
            'barcode'     => '1234567890123',
            'name'        => 'Nasi Goreng',
            'unit'        => 'porsi',
            'price'       => 25000,
            'cost_price'  => 15000,
        ];

        $productId = $this->productModel->insert($data);
        $product = $this->productModel->find($productId);

        $this->assertNotNull($product);
        $this->assertEquals('Nasi Goreng', $product['name']);
    }

    public function testCanUpdateProduct()
    {
        $categoryId = $this->createCategory();

        // Create product
        $data = [
            'category_id' => $categoryId,
            'sku'         => 'PRD001',
            'barcode'     => '1234567890123',
            'name'        => 'Nasi Goreng',
            'unit'        => 'porsi',
            'price'       => 25000,
            'cost_price'  => 15000,
        ];
        $productId = $this->productModel->insert($data);

        // Update product
        $updateData = [
            'name'       => 'Nasi Goreng Spesial',
            'price'      => 30000,
            'cost_price' => 18000,
        ];
        
        $result = $this->productModel->update($productId, $updateData);
        
        $this->assertTrue($result);
        
        $product = $this->productModel->find($productId);
        $this->assertEquals('Nasi Goreng Spesial', $product['name']);
        $this->assertEquals(30000, $product['price']);
        $this->assertEquals(18000, $product['cost_price']);
    }

    public function testCanDeleteProduct()
    {
        $categoryId = $this->createCategory();

        $data = [
            'category_id' => $categoryId,
            'sku'         => 'PRD001',
            'barcode'     => '1234567890123',
            'name'        => 'Nasi Goreng',
            'unit'        => 'porsi',
            'price'       => 25000,
            'cost_price'  => 15000,
        ];

        $productId = $this->productModel->insert($data);
        
        $result = $this->productModel->delete($productId);
        
        $this->assertTrue($result);
        
        $product = $this->productModel->find($productId);
        $this->assertNull($product);
    }

    public function testCanGetProductsWithCategory()
    {
        $categoryId = $this->createCategory();

        $data = [
            'category_id' => $categoryId,
            'sku'         => 'PRD001',
            'barcode'     => '1234567890123',
            'name'        => 'Nasi Goreng',
            'unit'        => 'porsi',
            'price'       => 25000,
            'cost_price'  => 15000,
        ];
        $this->productModel->insert($data);

        $productsWithCategory = $this->productModel->getProductsWithCategory();

        $this->assertIsArray($productsWithCategory);
        $this->assertArrayHasKey('category_name', $productsWithCategory[0]);
        $this->assertEquals('Makanan', $productsWithCategory[0]['category_name']);
    }

    public function testCanGetProductsByCategory()
    {
        // Create two categories
        $categoryId1 = $this->createCategory();
        
        $categoryData2 = [
            'name'        => 'Minuman',
            'description' => 'Aneka minuman',
        ];
        $categoryId2 = $this->categoryModel->insert($categoryData2);

        // Create products for different categories
        $products = [
            [
                'category_id' => $categoryId1,
                'sku'         => 'PRD001',
                'barcode'     => '1234567890123',
                'name'        => 'Nasi Goreng',
                'unit'        => 'porsi',
                'price'       => 25000,
                'cost_price'  => 15000,
            ],
            [
                'category_id' => $categoryId1,
                'sku'         => 'PRD002',
                'barcode'     => '1234567890124',
                'name'        => 'Mie Goreng',
                'unit'        => 'porsi',
                'price'       => 20000,
                'cost_price'  => 12000,
            ],
            [
                'category_id' => $categoryId2,
                'sku'         => 'PRD003',
                'barcode'     => '1234567890125',
                'name'        => 'Es Teh',
                'unit'        => 'gelas',
                'price'       => 5000,
                'cost_price'  => 2000,
            ],
        ];

        foreach ($products as $product) {
            $this->productModel->insert($product);
        }

        // Get products from Makanan category
        $categoryProducts = $this->productModel->getProductsByCategory($categoryId1);

        $this->assertCount(2, $categoryProducts);
        
        foreach ($categoryProducts as $product) {
            $this->assertEquals($categoryId1, $product['category_id']);
        }
    }

    public function testCanGetAllProducts()
    {
        $categoryId = $this->createCategory();

        // Insert multiple products
        $products = [
            [
                'category_id' => $categoryId,
                'sku'         => 'PRD001',
                'barcode'     => '1234567890123',
                'name'        => 'Nasi Goreng',
                'unit'        => 'porsi',
                'price'       => 25000,
                'cost_price'  => 15000,
            ],
            [
                'category_id' => $categoryId,
                'sku'         => 'PRD002',
                'barcode'     => '1234567890124',
                'name'        => 'Mie Goreng',
                'unit'        => 'porsi',
                'price'       => 20000,
                'cost_price'  => 12000,
            ],
        ];

        foreach ($products as $product) {
            $this->productModel->insert($product);
        }

        $allProducts = $this->productModel->findAll();
        
        $this->assertCount(2, $allProducts);
    }

    public function testTimestampsAreAutomaticallySet()
    {
        $categoryId = $this->createCategory();

        $data = [
            'category_id' => $categoryId,
            'sku'         => 'PRD001',
            'barcode'     => '1234567890123',
            'name'        => 'Nasi Goreng',
            'unit'        => 'porsi',
            'price'       => 25000,
            'cost_price'  => 15000,
        ];

        $productId = $this->productModel->insert($data);
        $product = $this->productModel->find($productId);

        $this->assertArrayHasKey('created_at', $product);
        $this->assertArrayHasKey('updated_at', $product);
        $this->assertNotNull($product['created_at']);
        $this->assertNotNull($product['updated_at']);
    }

    public function testCanFindProductBySKU()
    {
        $categoryId = $this->createCategory();

        $data = [
            'category_id' => $categoryId,
            'sku'         => 'PRD001',
            'barcode'     => '1234567890123',
            'name'        => 'Nasi Goreng',
            'unit'        => 'porsi',
            'price'       => 25000,
            'cost_price'  => 15000,
        ];

        $this->productModel->insert($data);

        $product = $this->productModel->where('sku', 'PRD001')->first();
        
        $this->assertNotNull($product);
        $this->assertEquals('Nasi Goreng', $product['name']);
    }

    public function testCanFindProductByBarcode()
    {
        $categoryId = $this->createCategory();

        $data = [
            'category_id' => $categoryId,
            'sku'         => 'PRD001',
            'barcode'     => '1234567890123',
            'name'        => 'Nasi Goreng',
            'unit'        => 'porsi',
            'price'       => 25000,
            'cost_price'  => 15000,
        ];

        $this->productModel->insert($data);

        $product = $this->productModel->where('barcode', '1234567890123')->first();
        
        $this->assertNotNull($product);
        $this->assertEquals('Nasi Goreng', $product['name']);
    }

    public function testCanSearchProducts()
    {
        $categoryId = $this->createCategory();

        // Insert products
        $products = [
            [
                'category_id' => $categoryId,
                'sku'         => 'PRD001',
                'barcode'     => '1234567890123',
                'name'        => 'Nasi Goreng Spesial',
                'unit'        => 'porsi',
                'price'       => 25000,
                'cost_price'  => 15000,
            ],
            [
                'category_id' => $categoryId,
                'sku'         => 'PRD002',
                'barcode'     => '1234567890124',
                'name'        => 'Nasi Goreng Biasa',
                'unit'        => 'porsi',
                'price'       => 20000,
                'cost_price'  => 12000,
            ],
            [
                'category_id' => $categoryId,
                'sku'         => 'PRD003',
                'barcode'     => '1234567890125',
                'name'        => 'Mie Goreng',
                'unit'        => 'porsi',
                'price'       => 18000,
                'cost_price'  => 10000,
            ],
        ];

        foreach ($products as $product) {
            $this->productModel->insert($product);
        }

        // Search for 'Nasi Goreng'
        $results = $this->productModel->like('name', 'Nasi Goreng')->findAll();
        
        $this->assertCount(2, $results);
    }
}
