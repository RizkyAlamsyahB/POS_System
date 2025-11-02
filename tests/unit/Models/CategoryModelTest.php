<?php

namespace Tests\Unit\Models;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\CategoryModel;

class CategoryModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $model;
    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;
    protected $namespace   = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new CategoryModel();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testCanCreateCategory()
    {
        $data = [
            'name'        => 'Makanan',
            'description' => 'Menu makanan utama dan ringan',
        ];

        $categoryId = $this->model->insert($data);
        
        $this->assertIsNumeric($categoryId);
        $this->assertGreaterThan(0, $categoryId);
        
        $category = $this->model->find($categoryId);
        $this->assertEquals('Makanan', $category['name']);
        $this->assertEquals('Menu makanan utama dan ringan', $category['description']);
    }

    public function testCannotCreateCategoryWithDuplicateName()
    {
        // Insert first category
        $data1 = [
            'name'        => 'Makanan',
            'description' => 'Menu makanan utama',
        ];
        $this->model->insert($data1);

        // Try to insert category with same name
        $data2 = [
            'name'        => 'Makanan', // Duplicate
            'description' => 'Menu makanan lainnya',
        ];
        
        $result = $this->model->insert($data2);
        
        $this->assertFalse($result);
        $this->assertNotEmpty($this->model->errors());
    }

    public function testCannotCreateCategoryWithoutName()
    {
        $data = [
            'description' => 'Menu makanan utama',
        ];

        $result = $this->model->insert($data);
        
        $this->assertFalse($result);
        
        $errors = $this->model->errors();
        $this->assertArrayHasKey('name', $errors);
    }

    public function testCanReadCategory()
    {
        $data = [
            'name'        => 'Makanan',
            'description' => 'Menu makanan utama dan ringan',
        ];

        $categoryId = $this->model->insert($data);
        $category = $this->model->find($categoryId);

        $this->assertNotNull($category);
        $this->assertEquals('Makanan', $category['name']);
    }

    public function testCanUpdateCategory()
    {
        // Create category
        $data = [
            'name'        => 'Makanan',
            'description' => 'Menu makanan utama',
        ];
        $categoryId = $this->model->insert($data);

        // Update category
        $updateData = [
            'name'        => 'Makanan & Snack',
            'description' => 'Menu makanan utama dan ringan',
        ];
        
        $result = $this->model->update($categoryId, $updateData);
        
        $this->assertTrue($result);
        
        $category = $this->model->find($categoryId);
        $this->assertEquals('Makanan & Snack', $category['name']);
        $this->assertEquals('Menu makanan utama dan ringan', $category['description']);
    }

    public function testCanDeleteCategory()
    {
        $data = [
            'name'        => 'Makanan',
            'description' => 'Menu makanan utama dan ringan',
        ];

        $categoryId = $this->model->insert($data);
        
        $result = $this->model->delete($categoryId);
        
        $this->assertTrue($result);
        
        $category = $this->model->find($categoryId);
        $this->assertNull($category);
    }

    public function testCanGetAllCategories()
    {
        // Insert multiple categories
        $categories = [
            ['name' => 'Makanan', 'description' => 'Menu makanan utama'],
            ['name' => 'Minuman', 'description' => 'Aneka minuman'],
            ['name' => 'Dessert', 'description' => 'Makanan penutup'],
        ];

        foreach ($categories as $category) {
            $this->model->insert($category);
        }

        $allCategories = $this->model->findAll();
        
        $this->assertCount(3, $allCategories);
    }

    public function testCanGetCategoriesWithProductCount()
    {
        // This test requires ProductModel
        // We'll create a basic test without actual products
        $data = [
            'name'        => 'Makanan',
            'description' => 'Menu makanan utama',
        ];
        $this->model->insert($data);

        $categoriesWithCount = $this->model->getCategoriesWithProductCount();
        
        $this->assertIsArray($categoriesWithCount);
        $this->assertArrayHasKey('product_count', $categoriesWithCount[0]);
    }

    public function testCanCheckIfCategoryHasProducts()
    {
        // Create category
        $data = [
            'name'        => 'Makanan',
            'description' => 'Menu makanan utama',
        ];
        $categoryId = $this->model->insert($data);

        // Check if category has products (should be false without products)
        $hasProducts = $this->model->hasProducts($categoryId);
        
        $this->assertFalse($hasProducts);
    }

    public function testTimestampsAreAutomaticallySet()
    {
        $data = [
            'name'        => 'Makanan',
            'description' => 'Menu makanan utama dan ringan',
        ];

        $categoryId = $this->model->insert($data);
        $category = $this->model->find($categoryId);

        $this->assertArrayHasKey('created_at', $category);
        $this->assertArrayHasKey('updated_at', $category);
        $this->assertNotNull($category['created_at']);
        $this->assertNotNull($category['updated_at']);
    }

    public function testCanFindCategoryByName()
    {
        $data = [
            'name'        => 'Makanan',
            'description' => 'Menu makanan utama dan ringan',
        ];

        $this->model->insert($data);

        $category = $this->model->where('name', 'Makanan')->first();
        
        $this->assertNotNull($category);
        $this->assertEquals('Menu makanan utama dan ringan', $category['description']);
    }

    public function testCategoryNameMaxLength()
    {
        $data = [
            'name'        => str_repeat('A', 101), // Exceeds 100 char limit
            'description' => 'Test description',
        ];

        $result = $this->model->insert($data);
        
        $this->assertFalse($result);
        $this->assertNotEmpty($this->model->errors());
    }

    public function testCanCreateCategoryWithoutDescription()
    {
        $data = [
            'name' => 'Makanan',
        ];

        $categoryId = $this->model->insert($data);
        
        $this->assertIsNumeric($categoryId);
        $this->assertGreaterThan(0, $categoryId);
        
        $category = $this->model->find($categoryId);
        $this->assertEquals('Makanan', $category['name']);
        $this->assertNull($category['description']);
    }

    public function testCanSearchCategories()
    {
        // Insert categories
        $categories = [
            ['name' => 'Makanan Berat', 'description' => 'Menu makanan utama'],
            ['name' => 'Makanan Ringan', 'description' => 'Snack dan cemilan'],
            ['name' => 'Minuman', 'description' => 'Aneka minuman'],
        ];

        foreach ($categories as $category) {
            $this->model->insert($category);
        }

        // Search for 'Makanan'
        $results = $this->model->like('name', 'Makanan')->findAll();
        
        $this->assertCount(2, $results);
    }
}
