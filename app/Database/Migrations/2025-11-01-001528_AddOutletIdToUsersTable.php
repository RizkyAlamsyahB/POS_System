<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOutletIdToUsersTable extends Migration
{
    public function up()
    {
        $fields = [
            'outlet_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'NULL = super admin (akses semua outlet)',
                'after'      => 'id',
            ],
        ];

        $this->forge->addColumn('users', $fields);

        // Add foreign key constraint
        $this->forge->addForeignKey('outlet_id', 'outlets', 'id', 'RESTRICT', 'CASCADE', 'fk_users_outlet_id');
        $this->db->query('ALTER TABLE users ADD INDEX idx_outlet (outlet_id)');
    }

    public function down()
    {
        // Check if foreign key exists before dropping
        $db = \Config\Database::connect();
        $foreignKeys = $db->query("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'users' 
            AND CONSTRAINT_NAME = 'fk_users_outlet_id'
        ")->getResult();

        if (!empty($foreignKeys)) {
            $this->forge->dropForeignKey('users', 'fk_users_outlet_id');
        }

        // Drop column if exists
        if ($db->fieldExists('outlet_id', 'users')) {
            $this->forge->dropColumn('users', 'outlet_id');
        }
    }
}
