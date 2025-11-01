<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductStocksTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'product_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'outlet_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'stock' => [
                'type'    => 'INT',
                'default' => 0,
                'comment' => 'Jumlah stok tersedia',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('product_id');
        $this->forge->addKey('outlet_id');
        $this->forge->addUniqueKey(['product_id', 'outlet_id'], 'unique_product_outlet');
        
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('outlet_id', 'outlets', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('product_stocks');
    }

    public function down()
    {
        $this->forge->dropTable('product_stocks');
    }
}
