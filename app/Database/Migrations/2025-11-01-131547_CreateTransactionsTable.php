<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransactionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'transaction_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'unique'     => true,
                'comment'    => 'TRX-OUT001-20241101-0001',
            ],
            'outlet_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'comment'  => 'Kasir yang input',
            ],
            'total_amount' => [
                'type'    => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
                'comment' => 'Total sebelum diskon',
            ],
            'total_discount' => [
                'type'    => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
                'comment' => 'Total diskon (promo + manual)',
            ],
            'subtotal_before_tax' => [
                'type'    => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
                'comment' => 'DPP (Dasar Pengenaan Pajak)',
            ],
            'total_tax' => [
                'type'    => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
                'comment' => 'Total PPN + PB1',
            ],
            'grand_total' => [
                'type'    => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
                'comment' => 'Total akhir yang dibayar',
            ],
            'payment_method' => [
                'type'       => 'ENUM',
                'constraint' => ['cash', 'debit', 'credit', 'ewallet'],
                'default'    => 'cash',
            ],
            'cash_amount' => [
                'type'    => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
                'comment' => 'Jumlah uang yang diterima',
            ],
            'change_amount' => [
                'type'    => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
                'comment' => 'Uang kembalian',
            ],
            'payment_status' => [
                'type'       => 'ENUM',
                'constraint' => ['paid', 'void', 'pending'],
                'default'    => 'paid',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addKey('outlet_id');
        $this->forge->addKey('user_id');
        $this->forge->addKey('created_at');
        $this->forge->addKey('payment_status');
        
        $this->forge->addForeignKey('outlet_id', 'outlets', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'RESTRICT', 'CASCADE');
        
        $this->forge->createTable('transactions');
    }

    public function down()
    {
        $this->forge->dropTable('transactions');
    }
}
