<?php

namespace Tests\Unit\Models;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\PromotionModel;
use App\Models\OutletModel;

class PromotionModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $promotionModel;
    protected $outletModel;
    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;
    protected $namespace   = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->promotionModel = new PromotionModel();
        $this->outletModel = new OutletModel();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    protected function createOutlet()
    {
        $outletData = [
            'code'      => 'OUT001',
            'name'      => 'Outlet Jakarta Pusat',
            'address'   => 'Jl. Sudirman No. 123',
            'phone'     => '021-12345678',
            'is_active' => 1,
        ];
        return $this->outletModel->insert($outletData);
    }

    public function testCanCreatePromotion()
    {
        $data = [
            'code'           => 'PROMO001',
            'name'           => 'Diskon Hari Raya',
            'description'    => 'Diskon spesial untuk hari raya',
            'discount_type'  => 'percentage',
            'discount_value' => 10,
            'start_date'     => date('Y-m-d'),
            'end_date'       => date('Y-m-d', strtotime('+30 days')),
            'is_active'      => 1,
        ];

        $promotionId = $this->promotionModel->insert($data);
        
        $this->assertIsNumeric($promotionId);
        $this->assertGreaterThan(0, $promotionId);
        
        $promotion = $this->promotionModel->find($promotionId);
        $this->assertEquals('PROMO001', $promotion['code']);
        $this->assertEquals('Diskon Hari Raya', $promotion['name']);
        $this->assertEquals('percentage', $promotion['discount_type']);
        $this->assertEquals(10, $promotion['discount_value']);
    }

    public function testCanCreatePromotionWithOutlet()
    {
        $outletId = $this->createOutlet();

        $data = [
            'code'           => 'PROMO001',
            'name'           => 'Diskon Outlet Jakarta',
            'description'    => 'Diskon khusus outlet Jakarta',
            'discount_type'  => 'fixed_amount',
            'discount_value' => 5000,
            'start_date'     => date('Y-m-d'),
            'end_date'       => date('Y-m-d', strtotime('+30 days')),
            'outlet_id'      => $outletId,
            'is_active'      => 1,
        ];

        $promotionId = $this->promotionModel->insert($data);
        
        $this->assertIsNumeric($promotionId);
        
        $promotion = $this->promotionModel->find($promotionId);
        $this->assertEquals($outletId, $promotion['outlet_id']);
    }

    public function testCanCreateGlobalPromotion()
    {
        // Global promotion has outlet_id = NULL
        $data = [
            'code'           => 'PROMO001',
            'name'           => 'Diskon Nasional',
            'description'    => 'Diskon untuk semua outlet',
            'discount_type'  => 'percentage',
            'discount_value' => 15,
            'start_date'     => date('Y-m-d'),
            'end_date'       => date('Y-m-d', strtotime('+30 days')),
            'outlet_id'      => null,
            'is_active'      => 1,
        ];

        $promotionId = $this->promotionModel->insert($data);
        
        $this->assertIsNumeric($promotionId);
        
        $promotion = $this->promotionModel->find($promotionId);
        $this->assertNull($promotion['outlet_id']);
    }

    public function testCannotCreatePromotionWithDuplicateCode()
    {
        // Insert first promotion
        $data1 = [
            'code'           => 'PROMO001',
            'name'           => 'Diskon Hari Raya',
            'discount_type'  => 'percentage',
            'discount_value' => 10,
            'start_date'     => date('Y-m-d'),
            'end_date'       => date('Y-m-d', strtotime('+30 days')),
            'is_active'      => 1,
        ];
        $this->promotionModel->insert($data1);

        // Try to insert promotion with same code
        $data2 = [
            'code'           => 'PROMO001', // Duplicate
            'name'           => 'Diskon Lebaran',
            'discount_type'  => 'percentage',
            'discount_value' => 20,
            'start_date'     => date('Y-m-d'),
            'end_date'       => date('Y-m-d', strtotime('+30 days')),
            'is_active'      => 1,
        ];
        
        $result = $this->promotionModel->insert($data2);
        
        $this->assertFalse($result);
        $this->assertNotEmpty($this->promotionModel->errors());
    }

    public function testCannotCreatePromotionWithoutRequiredFields()
    {
        $data = [
            'description' => 'Diskon spesial',
        ];

        $result = $this->promotionModel->insert($data);
        
        $this->assertFalse($result);
        
        $errors = $this->promotionModel->errors();
        $this->assertArrayHasKey('code', $errors);
        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('discount_type', $errors);
    }

    public function testCannotCreatePromotionWithInvalidDiscountType()
    {
        $data = [
            'code'           => 'PROMO001',
            'name'           => 'Diskon Hari Raya',
            'discount_type'  => 'invalid_type', // Invalid
            'discount_value' => 10,
            'start_date'     => date('Y-m-d'),
            'end_date'       => date('Y-m-d', strtotime('+30 days')),
            'is_active'      => 1,
        ];

        $result = $this->promotionModel->insert($data);
        
        $this->assertFalse($result);
        $this->assertNotEmpty($this->promotionModel->errors());
    }

    public function testCanReadPromotion()
    {
        $data = [
            'code'           => 'PROMO001',
            'name'           => 'Diskon Hari Raya',
            'discount_type'  => 'percentage',
            'discount_value' => 10,
            'start_date'     => date('Y-m-d'),
            'end_date'       => date('Y-m-d', strtotime('+30 days')),
            'is_active'      => 1,
        ];

        $promotionId = $this->promotionModel->insert($data);
        $promotion = $this->promotionModel->find($promotionId);

        $this->assertNotNull($promotion);
        $this->assertEquals('Diskon Hari Raya', $promotion['name']);
    }

    public function testCanUpdatePromotion()
    {
        // Create promotion
        $data = [
            'code'           => 'PROMO001',
            'name'           => 'Diskon Hari Raya',
            'discount_type'  => 'percentage',
            'discount_value' => 10,
            'start_date'     => date('Y-m-d'),
            'end_date'       => date('Y-m-d', strtotime('+30 days')),
            'is_active'      => 1,
        ];
        $promotionId = $this->promotionModel->insert($data);

        // Update promotion
        $updateData = [
            'name'           => 'Diskon Hari Raya 2025',
            'discount_value' => 15,
            'is_active'      => 0,
        ];
        
        $result = $this->promotionModel->update($promotionId, $updateData);
        
        $this->assertTrue($result);
        
        $promotion = $this->promotionModel->find($promotionId);
        $this->assertEquals('Diskon Hari Raya 2025', $promotion['name']);
        $this->assertEquals(15, $promotion['discount_value']);
        $this->assertEquals(0, $promotion['is_active']);
    }

    public function testCanDeletePromotion()
    {
        $data = [
            'code'           => 'PROMO001',
            'name'           => 'Diskon Hari Raya',
            'discount_type'  => 'percentage',
            'discount_value' => 10,
            'start_date'     => date('Y-m-d'),
            'end_date'       => date('Y-m-d', strtotime('+30 days')),
            'is_active'      => 1,
        ];

        $promotionId = $this->promotionModel->insert($data);
        
        $result = $this->promotionModel->delete($promotionId);
        
        $this->assertTrue($result);
        
        $promotion = $this->promotionModel->find($promotionId);
        $this->assertNull($promotion);
    }

    public function testCanGetAllPromotions()
    {
        // Insert multiple promotions
        $promotions = [
            [
                'code'           => 'PROMO001',
                'name'           => 'Diskon Hari Raya',
                'discount_type'  => 'percentage',
                'discount_value' => 10,
                'start_date'     => date('Y-m-d'),
                'end_date'       => date('Y-m-d', strtotime('+30 days')),
                'is_active'      => 1,
            ],
            [
                'code'           => 'PROMO002',
                'name'           => 'Diskon Kemerdekaan',
                'discount_type'  => 'fixed_amount',
                'discount_value' => 5000,
                'start_date'     => date('Y-m-d'),
                'end_date'       => date('Y-m-d', strtotime('+15 days')),
                'is_active'      => 1,
            ],
        ];

        foreach ($promotions as $promotion) {
            $this->promotionModel->insert($promotion);
        }

        $allPromotions = $this->promotionModel->findAll();
        
        $this->assertCount(2, $allPromotions);
    }

    public function testCanGetActivePromotions()
    {
        // Create promotions with different dates
        $promotions = [
            [
                'code'           => 'PROMO001',
                'name'           => 'Promo Aktif',
                'discount_type'  => 'percentage',
                'discount_value' => 10,
                'start_date'     => date('Y-m-d', strtotime('-5 days')),
                'end_date'       => date('Y-m-d', strtotime('+30 days')),
                'is_active'      => 1,
            ],
            [
                'code'           => 'PROMO002',
                'name'           => 'Promo Belum Aktif',
                'discount_type'  => 'percentage',
                'discount_value' => 15,
                'start_date'     => date('Y-m-d', strtotime('+5 days')), // Future
                'end_date'       => date('Y-m-d', strtotime('+30 days')),
                'is_active'      => 1,
            ],
            [
                'code'           => 'PROMO003',
                'name'           => 'Promo Sudah Lewat',
                'discount_type'  => 'percentage',
                'discount_value' => 20,
                'start_date'     => date('Y-m-d', strtotime('-30 days')),
                'end_date'       => date('Y-m-d', strtotime('-5 days')), // Past
                'is_active'      => 1,
            ],
        ];

        foreach ($promotions as $promotion) {
            $this->promotionModel->insert($promotion);
        }

        $activePromotions = $this->promotionModel->getActivePromotions();

        $this->assertCount(1, $activePromotions);
        $this->assertEquals('Promo Aktif', $activePromotions[0]['name']);
    }

    public function testCanGetPromotionsWithOutlet()
    {
        $outletId = $this->createOutlet();

        $data = [
            'code'           => 'PROMO001',
            'name'           => 'Diskon Outlet Jakarta',
            'discount_type'  => 'percentage',
            'discount_value' => 10,
            'start_date'     => date('Y-m-d'),
            'end_date'       => date('Y-m-d', strtotime('+30 days')),
            'outlet_id'      => $outletId,
            'is_active'      => 1,
        ];
        $this->promotionModel->insert($data);

        $promotionsWithOutlet = $this->promotionModel->getPromotionsWithOutlet();

        $this->assertIsArray($promotionsWithOutlet);
        $this->assertArrayHasKey('outlet_name', $promotionsWithOutlet[0]);
        $this->assertEquals('Outlet Jakarta Pusat', $promotionsWithOutlet[0]['outlet_name']);
    }

    public function testTimestampsAreAutomaticallySet()
    {
        $data = [
            'code'           => 'PROMO001',
            'name'           => 'Diskon Hari Raya',
            'discount_type'  => 'percentage',
            'discount_value' => 10,
            'start_date'     => date('Y-m-d'),
            'end_date'       => date('Y-m-d', strtotime('+30 days')),
            'is_active'      => 1,
        ];

        $promotionId = $this->promotionModel->insert($data);
        $promotion = $this->promotionModel->find($promotionId);

        $this->assertArrayHasKey('created_at', $promotion);
        $this->assertArrayHasKey('updated_at', $promotion);
        $this->assertNotNull($promotion['created_at']);
        $this->assertNotNull($promotion['updated_at']);
    }

    public function testCanFindPromotionByCode()
    {
        $data = [
            'code'           => 'PROMO001',
            'name'           => 'Diskon Hari Raya',
            'discount_type'  => 'percentage',
            'discount_value' => 10,
            'start_date'     => date('Y-m-d'),
            'end_date'       => date('Y-m-d', strtotime('+30 days')),
            'is_active'      => 1,
        ];

        $this->promotionModel->insert($data);

        $promotion = $this->promotionModel->where('code', 'PROMO001')->first();
        
        $this->assertNotNull($promotion);
        $this->assertEquals('Diskon Hari Raya', $promotion['name']);
    }

    public function testCanFilterPromotionsByDiscountType()
    {
        $promotions = [
            [
                'code'           => 'PROMO001',
                'name'           => 'Diskon Persentase',
                'discount_type'  => 'percentage',
                'discount_value' => 10,
                'start_date'     => date('Y-m-d'),
                'end_date'       => date('Y-m-d', strtotime('+30 days')),
                'is_active'      => 1,
            ],
            [
                'code'           => 'PROMO002',
                'name'           => 'Diskon Nominal',
                'discount_type'  => 'fixed_amount',
                'discount_value' => 5000,
                'start_date'     => date('Y-m-d'),
                'end_date'       => date('Y-m-d', strtotime('+30 days')),
                'is_active'      => 1,
            ],
        ];

        foreach ($promotions as $promotion) {
            $this->promotionModel->insert($promotion);
        }

        $percentagePromotions = $this->promotionModel->where('discount_type', 'percentage')->findAll();
        
        $this->assertCount(1, $percentagePromotions);
        $this->assertEquals('percentage', $percentagePromotions[0]['discount_type']);
    }
}
