<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductDataSeeder extends Seeder
{
    public function run()
    {
        // Pastikan folder upload ada
        $uploadPath = FCPATH . 'uploads/products';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
            echo "📁 Created directory: {$uploadPath}\n";
        }

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
        // 2️⃣  Insert Products dengan Download Gambar
        // ==============================
        $productsData = [
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
                'image_url'    => 'https://images.unsplash.com/photo-1680674814945-7945d913319c?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1170',
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
                'image_url'    => 'https://images.unsplash.com/photo-1647102398925-e23f6486ca04?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1170',
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
                'image_url'    => 'https://images.unsplash.com/photo-1622973536968-3ead9e780960?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1170',
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
                'image_url'    => 'https://plus.unsplash.com/premium_photo-1664970900098-2676029e6a99?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1170',
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
                'image_url'    => 'https://images.unsplash.com/photo-1635217217664-578a7e17218f?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1074',
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
                'image_url'    => 'https://plus.unsplash.com/premium_photo-1661347868028-55440b53c791?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1170',
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
                'image_url'    => 'https://images.unsplash.com/photo-1668507740203-0654d38b6201?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1170',
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
                'image_url'    => 'https://images.unsplash.com/photo-1533134242443-d4fd215305ad?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1170',
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
                'image_url'    => 'https://plus.unsplash.com/premium_photo-1683133428030-ed210d7498ba?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1125',
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
                'image_url'    => 'https://images.unsplash.com/photo-1627935722051-395636b0d8a5?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1173',
            ],
        ];

        // Download gambar dan simpan produk
        $products = [];
        foreach ($productsData as $index => $productData) {
            $imagePath = null;
            
            if (isset($productData['image_url']) && !empty($productData['image_url'])) {
                echo "📥 Downloading image for: {$productData['name']}... ";
                
                try {
                    // Download gambar
                    $imageContent = @file_get_contents($productData['image_url']);
                    
                    if ($imageContent !== false) {
                        // Generate nama file unik
                        $extension = 'jpg'; // Default untuk Unsplash
                        $filename = $productData['sku'] . '_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
                        $fullPath = $uploadPath . '/' . $filename;
                        
                        // Simpan file
                        if (file_put_contents($fullPath, $imageContent)) {
                            $imagePath = 'uploads/products/' . $filename;
                            echo "✅ Success!\n";
                        } else {
                            echo "❌ Failed to save file\n";
                        }
                    } else {
                        echo "❌ Failed to download\n";
                    }
                } catch (\Exception $e) {
                    echo "❌ Error: " . $e->getMessage() . "\n";
                }
            }
            
            // Hapus image_url dari data dan tambahkan ke array products
            unset($productData['image_url']);
            $productData['image'] = $imagePath;
            $productData['created_at'] = date('Y-m-d H:i:s');
            $productData['updated_at'] = date('Y-m-d H:i:s');
            
            $products[] = $productData;
        }

        // Insert batch semua produk
        $this->db->table('products')->insertBatch($products);
        echo "✅ Inserted " . count($products) . " restaurant products successfully.\n";

        // ==============================
        // 3️⃣  Insert Initial Stock for All Outlets
        // ==============================
        $outlets = $this->db->table('outlets')->get()->getResultArray();
        
        if (empty($outlets)) {
            echo "⚠️  No outlets found. Skipping stock insertion.\n";
        } else {
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
            echo "✅ Initial stock for " . count($outlets) . " outlet(s) inserted successfully.\n";
        }

        echo "\n🎉 Restaurant product seeding with images completed!\n";
    }
}