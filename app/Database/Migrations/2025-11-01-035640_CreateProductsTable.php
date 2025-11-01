<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'category_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'sku' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'comment'    => 'Stock Keeping Unit',
            ],
            'barcode' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'barcode_alt' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'comment'    => 'Barcode alternatif',
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'unit' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
                'default'    => 'PCS',
                'comment'    => 'Satuan: PCS, BOX, KG, LUSIN',
            ],
            'price' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
                'comment'    => 'Harga jual',
            ],
            'cost_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
                'comment'    => 'Harga pokok/HPP',
            ],
            'tax_type' => [
                'type'       => 'ENUM',
                'constraint' => ['NONE', 'PPN', 'PB1'],
                'default'    => 'NONE',
                'comment'    => 'Jenis pajak',
            ],
            'tax_rate' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
                'comment'    => 'Persentase pajak (11.00 = 11%)',
            ],
            'tax_included' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'comment'    => '1=harga sudah termasuk pajak, 0=belum',
            ],
            'image' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'comment'    => 'Path/URL gambar produk',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('category_id');
        $this->forge->addUniqueKey('sku');
        $this->forge->addUniqueKey('barcode');
        $this->forge->addKey('barcode_alt');
        
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'RESTRICT', 'CASCADE');
        
        $this->forge->createTable('products');
    }

    public function down()
    {
        $this->forge->dropTable('products');
    }
}
