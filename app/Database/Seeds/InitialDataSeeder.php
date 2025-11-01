<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        // ==============================
        // 1️⃣  Reset data lama
        // ==============================
        $this->db->disableForeignKeyChecks();

        // Hapus data lama dari tabel terkait
        $this->db->table('auth_groups_users')->truncate();
        $this->db->table('auth_identities')->truncate();
        $this->db->table('users')->truncate();
        $this->db->table('outlets')->truncate();

        $this->db->enableForeignKeyChecks();

        echo "🧹 Tables truncated successfully...\n";

        // ==============================
        // 2️⃣  Insert Master Outlets
        // ==============================
        $outlets = [
            [
                'code'       => 'OUT001',
                'name'       => 'Outlet Jakarta Pusat',
                'address'    => 'Jl. Sudirman No. 123, Jakarta Pusat',
                'phone'      => '021-12345678',
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code'       => 'OUT002',
                'name'       => 'Outlet Jakarta Selatan',
                'address'    => 'Jl. TB Simatupang No. 456, Jakarta Selatan',
                'phone'      => '021-87654321',
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code'       => 'OUT003',
                'name'       => 'Outlet Bandung',
                'address'    => 'Jl. Dago No. 789, Bandung',
                'phone'      => '022-11223344',
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('outlets')->insertBatch($outlets);

        echo "🏬 Inserted 3 outlets successfully.\n";

        // ==============================
        // 3️⃣  Insert Users via Shield
        // ==============================
        $users = auth()->getProvider();

        // --- Super Admin (akses semua outlet) ---
        $admin = new User([
            'username' => 'admin',
            'email'    => 'admin@pos.com',
            'password' => 'admin123',
        ]);
        $users->save($admin);
        $adminId = $users->getInsertID();
        $this->db->table('users')->where('id', $adminId)->update(['outlet_id' => null, 'active' => 1]);
        $adminEntity = $users->findById($adminId);
        $adminEntity->addGroup('admin');

        // --- Manager Outlet 1 ---
        $manager = new User([
            'username' => 'manager1',
            'email'    => 'manager1@pos.com',
            'password' => 'manager123',
        ]);
        $users->save($manager);
        $managerId = $users->getInsertID();
        $this->db->table('users')->where('id', $managerId)->update(['outlet_id' => 1, 'active' => 1]);
        $managerEntity = $users->findById($managerId);
        $managerEntity->addGroup('manager');

        // --- Cashier Outlet 1 ---
        $cashier1 = new User([
            'username' => 'cashier1',
            'email'    => 'cashier1@pos.com',
            'password' => 'cashier123',
        ]);
        $users->save($cashier1);
        $cashier1Id = $users->getInsertID();
        $this->db->table('users')->where('id', $cashier1Id)->update(['outlet_id' => 1, 'active' => 1]);
        $cashier1Entity = $users->findById($cashier1Id);
        $cashier1Entity->addGroup('cashier');

        // --- Cashier Outlet 2 ---
        $cashier2 = new User([
            'username' => 'cashier2',
            'email'    => 'cashier2@pos.com',
            'password' => 'cashier123',
        ]);
        $users->save($cashier2);
        $cashier2Id = $users->getInsertID();
        $this->db->table('users')->where('id', $cashier2Id)->update(['outlet_id' => 2, 'active' => 1]);
        $cashier2Entity = $users->findById($cashier2Id);
        $cashier2Entity->addGroup('cashier');

        // ==============================
        // ✅  Output hasil seeding
        // ==============================
        echo "👤 Created 4 users successfully:\n";
        echo "  - admin@pos.com / admin123 (Super Admin)\n";
        echo "  - manager1@pos.com / manager123 (Manager - Outlet 1)\n";
        echo "  - cashier1@pos.com / cashier123 (Cashier - Outlet 1)\n";
        echo "  - cashier2@pos.com / cashier123 (Cashier - Outlet 2)\n";
    }
}
