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
                'description' => 'Kategori produk makanan',
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Minuman',
                'description' => 'Kategori produk minuman',
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Elektronik',
                'description' => 'Kategori produk elektronik',
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Perlengkapan Rumah',
                'description' => 'Kategori perlengkapan rumah tangga',
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('categories')->insertBatch($categories);
        echo "✅ Inserted 4 categories successfully.\n";

        // ==============================
        // 2️⃣  Insert Products
        // ==============================
        $products = [
            // Makanan
            [
                'category_id'  => 1,
                'sku'          => 'MKN001',
                'barcode'      => '8991234567001',
                'barcode_alt'  => null,
                'name'         => 'Indomie Goreng',
                'unit'         => 'PCS',
                'price'        => 3500.00,
                'cost_price'   => 2800.00,
                'tax_type'     => 'PPN',
                'tax_rate'     => 11.00,
                'tax_included' => 1,
                'image'        => null,
                'is_active'    => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'category_id'  => 1,
                'sku'          => 'MKN002',
                'barcode'      => '8991234567002',
                'barcode_alt'  => null,
                'name'         => 'Mie Sedaap Goreng',
                'unit'         => 'PCS',
                'price'        => 3200.00,
                'cost_price'   => 2600.00,
                'tax_type'     => 'PPN',
                'tax_rate'     => 11.00,
                'tax_included' => 1,
                'image'        => null,
                'is_active'    => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'category_id'  => 1,
                'sku'          => 'MKN003',
                'barcode'      => '8991234567003',
                'barcode_alt'  => null,
                'name'         => 'Chitato Rasa Sapi Panggang',
                'unit'         => 'PCS',
                'price'        => 12000.00,
                'cost_price'   => 9500.00,
                'tax_type'     => 'PPN',
                'tax_rate'     => 11.00,
                'tax_included' => 1,
                'image'        => null,
                'is_active'    => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            // Minuman
            [
                'category_id'  => 2,
                'sku'          => 'MNM001',
                'barcode'      => '8991234567101',
                'barcode_alt'  => null,
                'name'         => 'Aqua 600ml',
                'unit'         => 'PCS',
                'price'        => 4000.00,
                'cost_price'   => 3200.00,
                'tax_type'     => 'PPN',
                'tax_rate'     => 11.00,
                'tax_included' => 1,
                'image'        => null,
                'is_active'    => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'category_id'  => 2,
                'sku'          => 'MNM002',
                'barcode'      => '8991234567102',
                'barcode_alt'  => null,
                'name'         => 'Coca Cola 390ml',
                'unit'         => 'PCS',
                'price'        => 6500.00,
                'cost_price'   => 5000.00,
                'tax_type'     => 'PPN',
                'tax_rate'     => 11.00,
                'tax_included' => 1,
                'image'        => null,
                'is_active'    => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'category_id'  => 2,
                'sku'          => 'MNM003',
                'barcode'      => '8991234567103',
                'barcode_alt'  => null,
                'name'         => 'Teh Botol Sosro 450ml',
                'unit'         => 'PCS',
                'price'        => 5500.00,
                'cost_price'   => 4200.00,
                'tax_type'     => 'PPN',
                'tax_rate'     => 11.00,
                'tax_included' => 1,
                'image'        => null,
                'is_active'    => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            // Elektronik
            [
                'category_id'  => 3,
                'sku'          => 'ELK001',
                'barcode'      => '8991234567201',
                'barcode_alt'  => null,
                'name'         => 'Baterai AA Alkaline (2pcs)',
                'unit'         => 'PCS',
                'price'        => 15000.00,
                'cost_price'   => 12000.00,
                'tax_type'     => 'PPN',
                'tax_rate'     => 11.00,
                'tax_included' => 0,
                'image'        => null,
                'is_active'    => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'category_id'  => 3,
                'sku'          => 'ELK002',
                'barcode'      => '8991234567202',
                'barcode_alt'  => null,
                'name'         => 'Kabel USB Type-C 1m',
                'unit'         => 'PCS',
                'price'        => 35000.00,
                'cost_price'   => 28000.00,
                'tax_type'     => 'PPN',
                'tax_rate'     => 11.00,
                'tax_included' => 0,
                'image'        => null,
                'is_active'    => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            // Perlengkapan Rumah
            [
                'category_id'  => 4,
                'sku'          => 'PRT001',
                'barcode'      => '8991234567301',
                'barcode_alt'  => null,
                'name'         => 'Sabun Cuci Piring 800ml',
                'unit'         => 'PCS',
                'price'        => 18000.00,
                'cost_price'   => 14500.00,
                'tax_type'     => 'PPN',
                'tax_rate'     => 11.00,
                'tax_included' => 1,
                'image'        => null,
                'is_active'    => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'category_id'  => 4,
                'sku'          => 'PRT002',
                'barcode'      => '8991234567302',
                'barcode_alt'  => null,
                'name'         => 'Tissue Kotak 250 lembar',
                'unit'         => 'BOX',
                'price'        => 12500.00,
                'cost_price'   => 9800.00,
                'tax_type'     => 'PPN',
                'tax_rate'     => 11.00,
                'tax_included' => 1,
                'image'        => null,
                'is_active'    => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('products')->insertBatch($products);
        echo "✅ Inserted 10 products successfully.\n";

        // ==============================
        // 3️⃣  Insert Initial Stock for All Outlets
        // ==============================
        $outlets = $this->db->table('outlets')->get()->getResultArray();
        $productIds = range(1, 10); // Assuming products have IDs 1-10

        $stocks = [];
        foreach ($outlets as $outlet) {
            foreach ($productIds as $productId) {
                $stocks[] = [
                    'product_id' => $productId,
                    'outlet_id'  => $outlet['id'],
                    'stock'      => rand(20, 100), // Random stock between 20-100
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
        }

        $this->db->table('product_stocks')->insertBatch($stocks);
        echo "✅ Inserted initial stock for all outlets successfully.\n";

        echo "🎉 Product data seeding completed!\n";
    }
}
