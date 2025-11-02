<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransactionDetailsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'transaction_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'product_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'qty' => [
                'type'    => 'INT',
                'default' => 1,
            ],
            'price' => [
                'type'    => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
                'comment' => 'Harga satuan saat transaksi',
            ],
            'cost_price' => [
                'type'    => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
                'comment' => 'HPP saat transaksi (untuk laporan profit)',
            ],
            'discount' => [
                'type'    => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
                'comment' => 'Diskon per item',
            ],
            'discount_note' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Keterangan diskon (promo name/manual)',
            ],
            'tax_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'comment'    => 'Jenis pajak yang dipakai',
            ],
            'tax_rate' => [
                'type'    => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 0.00,
                'comment' => 'Rate pajak saat transaksi',
            ],
            'tax_amount' => [
                'type'    => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
                'comment' => 'Nilai pajak',
            ],
            'subtotal' => [
                'type'    => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
                'comment' => 'Total setelah diskon + pajak',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('transaction_id');
        $this->forge->addKey('product_id');
        
        $this->forge->addForeignKey('transaction_id', 'transactions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'RESTRICT', 'CASCADE');
        
        $this->forge->createTable('transaction_details');
    }

    public function down()
    {
        $this->forge->dropTable('transaction_details');
    }
}
