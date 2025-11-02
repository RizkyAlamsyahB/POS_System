<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UpdateProductTaxSeeder extends Seeder
{
    public function run()
    {
        // Update beberapa produk agar tax_included = false
        // Sehingga bisa test perhitungan tax di POS
        
        $db = \Config\Database::connect();
        
        // Update Ayam Geprek & Spaghetti
        $db->table('products')
           ->whereIn('id', [2, 3])
           ->update([
               'tax_included' => 0,
               'tax_type' => 'PPN',
               'tax_rate' => 11.00
           ]);
        
        echo "✅ Updated products ID 2,3:\n";
        echo "   - tax_included = 0 (pajak belum termasuk)\n";
        echo "   - tax_type = PPN\n";
        echo "   - tax_rate = 11%\n";
        echo "\n📋 Produk:\n";
        echo "   - Ayam Geprek Sambal Matah (Rp 28.000 + PPN 11%)\n";
        echo "   - Spaghetti Bolognese (Rp 32.000 + PPN 11%)\n";
        echo "\n🎯 Sekarang PPN akan ditambahkan di Payment Summary!\n";
    }
}
