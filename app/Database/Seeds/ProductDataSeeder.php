<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductDataSeeder extends Seeder
{
    public function run()
    {
        // ==============================
        // 1️⃣  Insert Categories
        // ==============================
        $categories = [
            [
                'name'        => 'Makanan',
                'description' => 'Menu makanan utama dan ringan',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Minuman',
                'description' => 'Aneka minuman dingin dan panas',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Dessert',
                'description' => 'Makanan penutup dan kudapan manis',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Tambahan',
                'description' => 'Topping, sambal, atau bahan tambahan lainnya',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('categories')->insertBatch($categories);
        echo "✅ Inserted restaurant categories successfully.\n";

        // ==============================
        // 2️⃣  Insert Products
        // ==============================
        $products = [
            // Makanan
            [
                'category_id'  => 1,
                'sku'          => 'MKN001',
                'barcode'      => '8990010001',
                'name'         => 'Nasi Goreng Spesial',
                'unit'         => 'PORSI',
                'price'        => 25000.00,
                'cost_price'   => 16000.00,
                'tax_type'     => 'PPN',
                'tax_rate'     => 11.00,
                'tax_included' => 1,
            ],
            [
                'category_id'  => 1,
                'sku'          => 'MKN002',
                'barcode'      => '8990010002',
                'name'         => 'Ayam Geprek Sambal Matah',
                'unit'         => 'PORSI',
                'price'        => 28000.00,
                'cost_price'   => 18000.00,
                'tax_type'     => 'PPN',
                'tax_rate'     => 11.00,
                'tax_included' => 1,
            ],
            [
                'category_id'  => 1,
                'sku'          => 'MKN003',
                'barcode'      => '8990010003',
                'name'         => 'Spaghetti Bolognese',
                'unit'         => 'PORSI',
                'price'        => 32000.00,
                'cost_price'   => 20000.00,
                'tax_type'     => 'PPN',
                'tax_rate'     => 11.00,
                'tax_included' => 1,
            ],

            // Minuman
            [
                'category_id'  => 2,
                'sku'          => 'MNM001',
                'barcode'      => '8990020001',
                'name'         => 'Kopi Latte',
                'unit'         => 'GELAS',
                'price'        => 22000.00,
                'cost_price'   => 12000.00,
                'tax_type'     => 'PPN',
                'tax_rate'     => 11.00,
                'tax_included' => 1,
            ],
            [
                'category_id'  => 2,
                'sku'          => 'MNM002',
                'barcode'      => '8990020002',
                'name'         => 'Es Teh Manis',
                'unit'         => 'GELAS',
                'price'        => 8000.00,
                'cost_price'   => 4000.00,
                'tax_type'     => 'NONE',
                'tax_rate'     => 0.00,
                'tax_included' => 0,
            ],
            [
                'category_id'  => 2,
                'sku'          => 'MNM003',
                'barcode'      => '8990020003',
                'name'         => 'Jus Alpukat',
                'unit'         => 'GELAS',
                'price'        => 18000.00,
                'cost_price'   => 9000.00,
                'tax_type'     => 'PPN',
                'tax_rate'     => 11.00,
                'tax_included' => 1,
            ],

            // Dessert
            [
                'category_id'  => 3,
                'sku'          => 'DST001',
                'barcode'      => '8990030001',
                'name'         => 'Pancake Coklat',
                'unit'         => 'PORSI',
                'price'        => 15000.00,
                'cost_price'   => 9000.00,
                'tax_type'     => 'PPN',
                'tax_rate'     => 11.00,
                'tax_included' => 1,
            ],
            [
                'category_id'  => 3,
                'sku'          => 'DST002',
                'barcode'      => '8990030002',
                'name'         => 'Cheesecake Slice',
                'unit'         => 'POTONG',
                'price'        => 20000.00,
                'cost_price'   => 12000.00,
                'tax_type'     => 'PPN',
                'tax_rate'     => 11.00,
                'tax_included' => 1,
            ],

            // Tambahan
            [
                'category_id'  => 4,
                'sku'          => 'TMB001',
                'barcode'      => '8990040001',
                'name'         => 'Telur Mata Sapi',
                'unit'         => 'PCS',
                'price'        => 5000.00,
                'cost_price'   => 2500.00,
                'tax_type'     => 'NONE',
                'tax_rate'     => 0.00,
                'tax_included' => 0,
            ],
            [
                'category_id'  => 4,
                'sku'          => 'TMB002',
                'barcode'      => '8990040002',
                'name'         => 'Keju Parut',
                'unit'         => 'TAKARAN',
                'price'        => 7000.00,
                'cost_price'   => 3500.00,
                'tax_type'     => 'NONE',
                'tax_rate'     => 0.00,
                'tax_included' => 0,
            ],
        ];

        // Tambahkan timestamp otomatis
        foreach ($products as &$p) {
            $p['created_at'] = date('Y-m-d H:i:s');
            $p['updated_at'] = date('Y-m-d H:i:s');
            $p['image'] = null;
        }

        $this->db->table('products')->insertBatch($products);
        echo "✅ Inserted 10 restaurant products successfully.\n";

        // ==============================
        // 3️⃣  Insert Initial Stock for All Outlets
        // ==============================
        $outlets = $this->db->table('outlets')->get()->getResultArray();
        $productIds = range(1, count($products));

        $stocks = [];
        foreach ($outlets as $outlet) {
            foreach ($productIds as $productId) {
                $stocks[] = [
                    'product_id' => $productId,
                    'outlet_id'  => $outlet['id'],
                    'stock'      => rand(10, 50),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
        }

        $this->db->table('product_stocks')->insertBatch($stocks);
        echo "✅ Initial stock for outlets inserted successfully.\n";

        echo "🎉 Restaurant product seeding completed!\n";
    }
}
