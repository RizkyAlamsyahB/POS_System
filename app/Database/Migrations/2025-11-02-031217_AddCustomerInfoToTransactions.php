<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCustomerInfoToTransactions extends Migration
{
    public function up()
    {
        $fields = [
            'order_type' => [
                'type'       => 'ENUM',
                'constraint' => ['dine_in', 'take_away', 'delivery'],
                'default'    => 'dine_in',
                'after'      => 'user_id',
                'comment'    => 'Tipe order: Dine In / Take Away / Delivery',
            ],
            'table_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'after'      => 'order_type',
                'comment'    => 'Nomor meja untuk dine-in',
            ],
            'customer_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'table_number',
                'comment'    => 'Nama pembeli (untuk take away / delivery)',
            ],
            'total_tax_included' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
                'after'      => 'total_tax',
                'comment'    => 'Total pajak yang sudah termasuk dalam harga (untuk laporan)',
            ],
        ];

        $this->forge->addColumn('transactions', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('transactions', ['order_type', 'table_number', 'customer_name', 'total_tax_included']);
    }
}
