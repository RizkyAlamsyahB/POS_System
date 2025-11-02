<?php

namespace Tests\Unit\Models;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\UserModel;
use App\Models\OutletModel;

class UserModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $userModel;
    protected $outletModel;
    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;
    protected $namespace   = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userModel = new UserModel();
        $this->outletModel = new OutletModel();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testCanCreateUser()
    {
        $data = [
            'username' => 'testuser',
            'active'   => 1,
        ];

        $userId = $this->userModel->insert($data);
        
        $this->assertIsNumeric($userId);
        $this->assertGreaterThan(0, $userId);
        
        $user = $this->userModel->find($userId);
        $this->assertEquals('testuser', $user->username);
        $this->assertEquals(1, $user->active);
    }

    public function testCanCreateUserWithOutlet()
    {
        // Create outlet first
        $outletData = [
            'code'      => 'OUT001',
            'name'      => 'Outlet Jakarta Pusat',
            'address'   => 'Jl. Sudirman No. 123',
            'phone'     => '021-12345678',
            'is_active' => 1,
        ];
        $outletId = $this->outletModel->insert($outletData);

        // Create user with outlet
        $userData = [
            'username'  => 'manager1',
            'active'    => 1,
            'outlet_id' => $outletId,
        ];

        $userId = $this->userModel->insert($userData);
        
        $this->assertIsNumeric($userId);
        
        $user = $this->userModel->find($userId);
        $this->assertEquals($outletId, $user->outlet_id);
    }

    public function testCanCreateSuperAdminUser()
    {
        // Super admin has no outlet (outlet_id = NULL)
        $data = [
            'username'  => 'superadmin',
            'active'    => 1,
            'outlet_id' => null,
        ];

        $userId = $this->userModel->insert($data);
        
        $this->assertIsNumeric($userId);
        
        $user = $this->userModel->find($userId);
        $this->assertNull($user->outlet_id);
    }

    public function testCanReadUser()
    {
        $data = [
            'username' => 'testuser',
            'active'   => 1,
        ];

        $userId = $this->userModel->insert($data);
        $user = $this->userModel->find($userId);

        $this->assertNotNull($user);
        $this->assertEquals('testuser', $user->username);
    }

    public function testCanUpdateUser()
    {
        // Create user
        $data = [
            'username' => 'testuser',
            'active'   => 1,
        ];
        $userId = $this->userModel->insert($data);

        // Update user
        $updateData = [
            'username' => 'updateduser',
            'active'   => 0,
        ];
        
        $result = $this->userModel->update($userId, $updateData);
        
        $this->assertTrue($result);
        
        $user = $this->userModel->find($userId);
        $this->assertEquals('updateduser', $user->username);
        $this->assertEquals(0, $user->active);
    }

    public function testCanDeleteUser()
    {
        $data = [
            'username' => 'testuser',
            'active'   => 1,
        ];

        $userId = $this->userModel->insert($data);
        
        $result = $this->userModel->delete($userId);
        
        $this->assertTrue($result);
        
        $user = $this->userModel->find($userId);
        $this->assertEmpty($user);
    }

    public function testCanGetUserWithOutlet()
    {
        // Create outlet
        $outletData = [
            'code'      => 'OUT001',
            'name'      => 'Outlet Jakarta Pusat',
            'address'   => 'Jl. Sudirman No. 123',
            'phone'     => '021-12345678',
            'is_active' => 1,
        ];
        $outletId = $this->outletModel->insert($outletData);

        // Create user with outlet
        $userData = [
            'username'  => 'manager1',
            'active'    => 1,
            'outlet_id' => $outletId,
        ];
        $userId = $this->userModel->insert($userData);

        // Get user with outlet info
        $userWithOutlet = $this->userModel->getUserWithOutlet($userId);

        $this->assertNotNull($userWithOutlet);
        $this->assertEquals('manager1', $userWithOutlet->username);
        $this->assertEquals('OUT001', $userWithOutlet->outlet_code);
        $this->assertEquals('Outlet Jakarta Pusat', $userWithOutlet->outlet_name);
    }

    public function testCanGetUsersByOutlet()
    {
        // Create outlet
        $outletData = [
            'code'      => 'OUT001',
            'name'      => 'Outlet Jakarta Pusat',
            'address'   => 'Jl. Sudirman No. 123',
            'phone'     => '021-12345678',
            'is_active' => 1,
        ];
        $outletId = $this->outletModel->insert($outletData);

        // Create multiple users for the same outlet
        $users = [
            [
                'username'  => 'manager1',
                'active'    => 1,
                'outlet_id' => $outletId,
            ],
            [
                'username'  => 'cashier1',
                'active'    => 1,
                'outlet_id' => $outletId,
            ],
            [
                'username'  => 'cashier2',
                'active'    => 0, // Inactive user
                'outlet_id' => $outletId,
            ],
        ];

        foreach ($users as $user) {
            $this->userModel->insert($user);
        }

        // Get active users by outlet
        $outletUsers = $this->userModel->getUsersByOutlet($outletId);

        $this->assertCount(2, $outletUsers); // Only active users
        
        foreach ($outletUsers as $user) {
            $this->assertEquals($outletId, $user->outlet_id);
            $this->assertEquals(1, $user->active);
        }
    }

    public function testCanCheckIfUserIsSuperAdmin()
    {
        // Create super admin (no outlet)
        $superAdminData = [
            'username'  => 'superadmin',
            'active'    => 1,
            'outlet_id' => null,
        ];
        $superAdminId = $this->userModel->insert($superAdminData);

        // Create regular user with outlet
        $outletData = [
            'code'      => 'OUT001',
            'name'      => 'Outlet Jakarta Pusat',
            'address'   => 'Jl. Sudirman No. 123',
            'phone'     => '021-12345678',
            'is_active' => 1,
        ];
        $outletId = $this->outletModel->insert($outletData);

        $regularUserData = [
            'username'  => 'manager1',
            'active'    => 1,
            'outlet_id' => $outletId,
        ];
        $regularUserId = $this->userModel->insert($regularUserData);

        // Check super admin
        $this->assertTrue($this->userModel->isSuperAdmin($superAdminId));
        
        // Check regular user
        $this->assertFalse($this->userModel->isSuperAdmin($regularUserId));
    }

    public function testCanGetUserOutletId()
    {
        // Create outlet
        $outletData = [
            'code'      => 'OUT001',
            'name'      => 'Outlet Jakarta Pusat',
            'address'   => 'Jl. Sudirman No. 123',
            'phone'     => '021-12345678',
            'is_active' => 1,
        ];
        $outletId = $this->outletModel->insert($outletData);

        // Create user with outlet
        $userData = [
            'username'  => 'manager1',
            'active'    => 1,
            'outlet_id' => $outletId,
        ];
        $userId = $this->userModel->insert($userData);

        // Get outlet ID
        $retrievedOutletId = $this->userModel->getUserOutletId($userId);
        
        $this->assertEquals($outletId, $retrievedOutletId);
    }

    public function testGetUserOutletIdReturnsNullForSuperAdmin()
    {
        // Create super admin (no outlet)
        $superAdminData = [
            'username'  => 'superadmin',
            'active'    => 1,
            'outlet_id' => null,
        ];
        $superAdminId = $this->userModel->insert($superAdminData);

        // Get outlet ID
        $outletId = $this->userModel->getUserOutletId($superAdminId);
        
        $this->assertNull($outletId);
    }

    public function testCanGetAllUsers()
    {
        // Insert multiple users
        $users = [
            ['username' => 'user1', 'active' => 1],
            ['username' => 'user2', 'active' => 1],
            ['username' => 'user3', 'active' => 0],
        ];

        foreach ($users as $user) {
            $this->userModel->insert($user);
        }

        $allUsers = $this->userModel->findAll();
        
        $this->assertCount(3, $allUsers);
    }

    public function testCanFilterActiveUsers()
    {
        // Insert users with different status
        $users = [
            ['username' => 'user1', 'active' => 1],
            ['username' => 'user2', 'active' => 1],
            ['username' => 'user3', 'active' => 0],
        ];

        foreach ($users as $user) {
            $this->userModel->insert($user);
        }

        $activeUsers = $this->userModel->where('active', 1)->findAll();
        
        $this->assertCount(2, $activeUsers);
        
        foreach ($activeUsers as $user) {
            $this->assertEquals(1, $user->active);
        }
    }
}
