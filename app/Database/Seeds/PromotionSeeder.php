<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PromotionSeeder extends Seeder
{
    public function run()
    {
        // Get outlets for sample data
        $db = \Config\Database::connect();
        $outlets = $db->table('outlets')->get()->getResultArray();
        $outletIds = array_column($outlets, 'id');
        
        // Sample promotions data
        $promotions = [
            [
                'code'           => 'FLASH50',
                'name'           => 'Flash Sale 50%',
                'description'    => 'Diskon 50% untuk produk pilihan (maksimal Rp 100.000)',
                'discount_type'  => 'percentage',
                'discount_value' => 50,
                'min_purchase'   => null,
                'max_discount'   => 100000,
                'start_date'     => date('Y-m-d'),
                'end_date'       => date('Y-m-d', strtotime('+7 days')),
                'start_time'     => '10:00:00',
                'end_time'       => '14:00:00',
                'outlet_id'      => null, // Semua outlet
                'is_active'      => 1,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'code'           => 'DISCOUNT25K',
                'name'           => 'Potongan Langsung 25K',
                'description'    => 'Potongan Rp 25.000 untuk belanja minimal Rp 200.000',
                'discount_type'  => 'fixed_amount',
                'discount_value' => 25000,
                'min_purchase'   => 200000,
                'max_discount'   => null,
                'start_date'     => date('Y-m-d'),
                'end_date'       => date('Y-m-d', strtotime('+30 days')),
                'start_time'     => null,
                'end_time'       => null,
                'outlet_id'      => !empty($outletIds) ? $outletIds[0] : null, // Outlet pertama
                'is_active'      => 1,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'code'           => 'WEEKEND20',
                'name'           => 'Weekend Sale 20%',
                'description'    => 'Diskon 20% khusus weekend',
                'discount_type'  => 'percentage',
                'discount_value' => 20,
                'min_purchase'   => 100000,
                'max_discount'   => 50000,
                'start_date'     => date('Y-m-d', strtotime('next Saturday')),
                'end_date'       => date('Y-m-d', strtotime('next Sunday')),
                'start_time'     => null,
                'end_time'       => null,
                'outlet_id'      => null, // Semua outlet
                'is_active'      => 1,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'code'           => 'PAYDAY15',
                'name'           => 'Payday Promo 15%',
                'description'    => 'Diskon 15% tanpa minimal belanja',
                'discount_type'  => 'percentage',
                'discount_value' => 15,
                'min_purchase'   => null,
                'max_discount'   => 75000,
                'start_date'     => date('Y-m-25'),
                'end_date'       => date('Y-m-t'), // End of month
                'start_time'     => null,
                'end_time'       => null,
                'outlet_id'      => null,
                'is_active'      => 0, // Inactive example
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
        ];

        // Insert promotions
        $builder = $db->table('promotions');
        foreach ($promotions as $promotion) {
            $builder->insert($promotion);
        }

        echo "✅ Sample promotions inserted successfully!\n";
        
        // Get products for sample promotion items
        $products = $db->table('products')->limit(10)->get()->getResultArray();
        $productIds = array_column($products, 'id');
        
        if (!empty($productIds)) {
            // Get promotion IDs
            $createdPromotions = $db->table('promotions')->get()->getResultArray();
            
            // Add sample products to first 2 promotions
            $promotionItems = [];
            
            if (count($createdPromotions) >= 1) {
                // Add 5 random products to FLASH50
                $randomProducts = array_slice($productIds, 0, 5);
                foreach ($randomProducts as $productId) {
                    $promotionItems[] = [
                        'promotion_id' => $createdPromotions[0]['id'],
                        'product_id'   => $productId,
                        'created_at'   => date('Y-m-d H:i:s'),
                        'updated_at'   => date('Y-m-d H:i:s'),
                    ];
                }
            }
            
            if (count($createdPromotions) >= 2) {
                // Add 3 random products to DISCOUNT25K
                $randomProducts = array_slice($productIds, 3, 3);
                foreach ($randomProducts as $productId) {
                    $promotionItems[] = [
                        'promotion_id' => $createdPromotions[1]['id'],
                        'product_id'   => $productId,
                        'created_at'   => date('Y-m-d H:i:s'),
                        'updated_at'   => date('Y-m-d H:i:s'),
                    ];
                }
            }
            
            if (!empty($promotionItems)) {
                $itemsBuilder = $db->table('promotion_items');
                $itemsBuilder->insertBatch($promotionItems);
                echo "✅ Sample promotion items inserted successfully!\n";
            }
        }
    }
}
