<?php

namespace Tests\Unit\Models;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\OutletModel;

class OutletModelTest extends CIUnitTestCase
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
        $this->model = new OutletModel();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testCanCreateOutlet()
    {
        $data = [
            'code'      => 'OUT001',
            'name'      => 'Outlet Jakarta Pusat',
            'address'   => 'Jl. Sudirman No. 123',
            'phone'     => '021-12345678',
            'is_active' => 1,
        ];

        $outletId = $this->model->insert($data);
        
        $this->assertIsNumeric($outletId);
        $this->assertGreaterThan(0, $outletId);
        
        $outlet = $this->model->find($outletId);
        $this->assertEquals('OUT001', $outlet['code']);
        $this->assertEquals('Outlet Jakarta Pusat', $outlet['name']);
        $this->assertEquals(1, $outlet['is_active']);
    }

    public function testCannotCreateOutletWithDuplicateCode()
    {
        // Insert first outlet
        $data1 = [
            'code'      => 'OUT001',
            'name'      => 'Outlet Jakarta Pusat',
            'address'   => 'Jl. Sudirman No. 123',
            'phone'     => '021-12345678',
            'is_active' => 1,
        ];
        $this->model->insert($data1);

        // Try to insert outlet with same code
        $data2 = [
            'code'      => 'OUT001', // Duplicate
            'name'      => 'Outlet Jakarta Selatan',
            'address'   => 'Jl. Gatot Subroto No. 456',
            'phone'     => '021-87654321',
            'is_active' => 1,
        ];
        
        $result = $this->model->insert($data2);
        
        $this->assertFalse($result);
        $this->assertNotEmpty($this->model->errors());
    }

    public function testCannotCreateOutletWithoutRequiredFields()
    {
        $data = [
            'address'   => 'Jl. Sudirman No. 123',
            'phone'     => '021-12345678',
        ];

        $result = $this->model->insert($data);
        
        $this->assertFalse($result);
        
        $errors = $this->model->errors();
        $this->assertArrayHasKey('code', $errors);
        $this->assertArrayHasKey('name', $errors);
    }

    public function testCanReadOutlet()
    {
        $data = [
            'code'      => 'OUT001',
            'name'      => 'Outlet Jakarta Pusat',
            'address'   => 'Jl. Sudirman No. 123',
            'phone'     => '021-12345678',
            'is_active' => 1,
        ];

        $outletId = $this->model->insert($data);
        $outlet = $this->model->find($outletId);

        $this->assertNotNull($outlet);
        $this->assertEquals('OUT001', $outlet['code']);
        $this->assertEquals('Outlet Jakarta Pusat', $outlet['name']);
    }

    public function testCanUpdateOutlet()
    {
        // Create outlet
        $data = [
            'code'      => 'OUT001',
            'name'      => 'Outlet Jakarta Pusat',
            'address'   => 'Jl. Sudirman No. 123',
            'phone'     => '021-12345678',
            'is_active' => 1,
        ];
        $outletId = $this->model->insert($data);

        // Update outlet
        $updateData = [
            'name'      => 'Outlet Jakarta Pusat - Updated',
            'address'   => 'Jl. Sudirman No. 999',
            'is_active' => 0,
        ];
        
        $result = $this->model->update($outletId, $updateData);
        
        $this->assertTrue($result);
        
        $outlet = $this->model->find($outletId);
        $this->assertEquals('Outlet Jakarta Pusat - Updated', $outlet['name']);
        $this->assertEquals('Jl. Sudirman No. 999', $outlet['address']);
        $this->assertEquals(0, $outlet['is_active']);
    }

    public function testCanDeleteOutlet()
    {
        $data = [
            'code'      => 'OUT001',
            'name'      => 'Outlet Jakarta Pusat',
            'address'   => 'Jl. Sudirman No. 123',
            'phone'     => '021-12345678',
            'is_active' => 1,
        ];

        $outletId = $this->model->insert($data);
        
        $result = $this->model->delete($outletId);
        
        $this->assertTrue($result);
        
        $outlet = $this->model->find($outletId);
        $this->assertNull($outlet);
    }

    public function testCanGetAllOutlets()
    {
        // Insert multiple outlets
        $outlets = [
            [
                'code'      => 'OUT001',
                'name'      => 'Outlet Jakarta Pusat',
                'address'   => 'Jl. Sudirman No. 123',
                'phone'     => '021-12345678',
                'is_active' => 1,
            ],
            [
                'code'      => 'OUT002',
                'name'      => 'Outlet Jakarta Selatan',
                'address'   => 'Jl. Gatot Subroto No. 456',
                'phone'     => '021-87654321',
                'is_active' => 1,
            ],
            [
                'code'      => 'OUT003',
                'name'      => 'Outlet Jakarta Barat',
                'address'   => 'Jl. Taman Palem No. 789',
                'phone'     => '021-11112222',
                'is_active' => 0,
            ],
        ];

        foreach ($outlets as $outlet) {
            $this->model->insert($outlet);
        }

        $allOutlets = $this->model->findAll();
        
        $this->assertCount(3, $allOutlets);
    }

    public function testCanGetActiveOutletsOnly()
    {
        // Insert outlets with different status
        $outlets = [
            [
                'code'      => 'OUT001',
                'name'      => 'Outlet Jakarta Pusat',
                'address'   => 'Jl. Sudirman No. 123',
                'phone'     => '021-12345678',
                'is_active' => 1,
            ],
            [
                'code'      => 'OUT002',
                'name'      => 'Outlet Jakarta Selatan',
                'address'   => 'Jl. Gatot Subroto No. 456',
                'phone'     => '021-87654321',
                'is_active' => 1,
            ],
            [
                'code'      => 'OUT003',
                'name'      => 'Outlet Jakarta Barat',
                'address'   => 'Jl. Taman Palem No. 789',
                'phone'     => '021-11112222',
                'is_active' => 0,
            ],
        ];

        foreach ($outlets as $outlet) {
            $this->model->insert($outlet);
        }

        $activeOutlets = $this->model->getActiveOutlets();
        
        $this->assertCount(2, $activeOutlets);
        
        foreach ($activeOutlets as $outlet) {
            $this->assertEquals(1, $outlet['is_active']);
        }
    }

    public function testCanFindOutletByCode()
    {
        $data = [
            'code'      => 'OUT001',
            'name'      => 'Outlet Jakarta Pusat',
            'address'   => 'Jl. Sudirman No. 123',
            'phone'     => '021-12345678',
            'is_active' => 1,
        ];

        $this->model->insert($data);

        $outlet = $this->model->where('code', 'OUT001')->first();
        
        $this->assertNotNull($outlet);
        $this->assertEquals('Outlet Jakarta Pusat', $outlet['name']);
    }

    public function testTimestampsAreAutomaticallySet()
    {
        $data = [
            'code'      => 'OUT001',
            'name'      => 'Outlet Jakarta Pusat',
            'address'   => 'Jl. Sudirman No. 123',
            'phone'     => '021-12345678',
            'is_active' => 1,
        ];

        $outletId = $this->model->insert($data);
        $outlet = $this->model->find($outletId);

        $this->assertArrayHasKey('created_at', $outlet);
        $this->assertArrayHasKey('updated_at', $outlet);
        $this->assertNotNull($outlet['created_at']);
        $this->assertNotNull($outlet['updated_at']);
    }
}
