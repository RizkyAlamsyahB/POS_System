<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePromotionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'unique'     => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'discount_type' => [
                'type'       => 'ENUM',
                'constraint' => ['percentage', 'fixed_amount'],
                'default'    => 'percentage',
                'comment'    => 'percentage = %, fixed_amount = Rp',
            ],
            'discount_value' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'comment'    => 'Nilai diskon (% atau Rp)',
            ],
            'min_purchase' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
                'comment'    => 'Minimal pembelian (Rp)',
            ],
            'max_discount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
                'comment'    => 'Maksimal diskon untuk percentage type',
            ],
            'start_date' => [
                'type' => 'DATE',
            ],
            'end_date' => [
                'type' => 'DATE',
            ],
            'start_time' => [
                'type' => 'TIME',
                'null' => true,
                'comment' => 'Jam mulai promo (optional)',
            ],
            'end_time' => [
                'type' => 'TIME',
                'null' => true,
                'comment' => 'Jam selesai promo (optional)',
            ],
            'outlet_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'NULL = berlaku semua outlet',
            ],
            'is_active' => [
                'type'       => 'BOOLEAN',
                'default'    => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('outlet_id', 'outlets', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('promotions');
    }

    public function down()
    {
        $this->forge->dropTable('promotions');
    }
}
