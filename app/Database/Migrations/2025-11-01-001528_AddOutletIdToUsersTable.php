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
        // Drop foreign key first
        $this->forge->dropForeignKey('users', 'fk_users_outlet_id');
        $this->forge->dropColumn('users', 'outlet_id');
    }
}
