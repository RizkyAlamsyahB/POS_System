# 🏪 Multi-Outlet Point of Sale (POS) System

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.1+-777BB4?style=flat-square&logo=php" alt="PHP">
  <img src="https://img.shields.io/badge/CodeIgniter-4.x-EE4623?style=flat-square&logo=codeigniter" alt="CodeIgniter">
  <img src="https://img.shields.io/badge/Tests-66%20Passed-success?style=flat-square" alt="Tests">
  <img src="https://img.shields.io/badge/License-MIT-blue?style=flat-square" alt="License">
</p>

## 📖 Tentang Aplikasi

Sistem Point of Sale (POS) berbasis web untuk mengelola **multiple outlet** dengan fitur lengkap manajemen produk, stok, transaksi, promosi, dan laporan penjualan. Dibangun menggunakan **CodeIgniter 4** framework dengan **CodeIgniter Shield** untuk authentication & authorization.

### ✨ Fitur Utama

#### 🔐 Sistem Autentikasi & Role Management
- **3 Level User**: Admin, Manager, Cashier
- **Multi-Outlet Access Control**: Super admin bisa akses semua outlet
- **User Active Status**: Admin bisa nonaktifkan user
- **Outlet Active Status**: Kontrol akses per outlet

#### 👨‍💼 Panel Admin
- ✅ Manajemen outlet (CRUD + status aktif/nonaktif)
- ✅ Manajemen user & role assignment
- ✅ Manajemen kategori produk
- ✅ Manajemen produk (dengan upload gambar)
- ✅ Manajemen stok per outlet
- ✅ Manajemen promosi & diskon
- ✅ Laporan penjualan semua outlet
- ✅ DataTables untuk semua list data

#### 👔 Panel Manager
- ✅ View informasi outlet sendiri
- ✅ Manajemen stok outlet sendiri
- ✅ Laporan penjualan outlet sendiri
- ✅ View transaksi & detail

#### 💰 Panel Kasir (POS)
- ✅ Interface POS full-screen & responsive
- ✅ Pencarian produk by kategori
- ✅ Cart management (add, update qty, remove)
- ✅ Multiple payment methods (Cash, Card, E-Wallet)
- ✅ Real-time stock validation
- ✅ Automatic promotion/discount calculation
- ✅ Print receipt ready

#### 📊 Sistem Pelaporan
- ✅ Sales report dengan filter tanggal
- ✅ Transaction history dengan detail
- ✅ Export ready (extensible untuk PDF/Excel)

## 🛠️ Tech Stack

- **Framework**: CodeIgniter 4.5+
- **PHP**: 8.1+ (8.2+ recommended)
- **Database**: MySQL 5.7+ / MariaDB 10.3+
- **Authentication**: CodeIgniter Shield 1.2+
- **Frontend**: Bootstrap 5 + Mazer Admin Template
- **UI Components**: DataTables, Bootstrap Icons, Iconly
- **Real-time Updates**: Pusher WebSocket
- **Testing**: PHPUnit 10.5+

## 🚀 Quick Start

### Prerequisites

Pastikan sistem Anda sudah memiliki:
- PHP 8.1 atau lebih tinggi
- Composer
- MySQL 5.7+ atau MariaDB 10.3+
- Web server (Apache/Nginx) atau PHP built-in server

### Installation

#### 1. Clone Repository
```bash
git clone https://github.com/RizkyAlamsyahB/POS_System.git
cd POS_System
```

#### 2. Install Dependencies
```bash
composer install
```

#### 3. Setup Environment
```bash
cp env .env
```

Edit file `.env` sesuai konfigurasi Anda:
```env
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost:8080/'

database.default.hostname = localhost
database.default.database = pos_system
database.default.username = root
database.default.password = your_password
database.default.DBDriver = MySQLi
database.default.DBPrefix = 

# Pusher WebSocket Configuration (Real-time Updates)
pusher.appId = your_pusher_app_id
pusher.appKey = your_pusher_app_key
pusher.appSecret = your_pusher_app_secret
pusher.appCluster = ap1
pusher.useTLS = true
```

#### 4. Buat Database
```bash
# Login ke MySQL
mysql -u root -p

# Buat database
CREATE DATABASE pos_system;
exit;
```

#### 5. Setup Permission Folder Writable
```bash
# Set permission untuk folder writable
chmod -R 755 writable/

# Jika masih error permission (development only):
chmod -R 777 writable/
```

> **📌 Catatan**: Folder `writable/` sudah include struktur lengkap dari repository:
> - `writable/cache/` - untuk cache sistem
> - `writable/logs/` - untuk log aplikasi  
> - `writable/session/` - untuk session files
> - `writable/uploads/` - untuk file upload user
> - `writable/debugbar/` - untuk debug toolbar
>
> Setiap folder sudah dilindungi dengan `index.html` untuk mencegah directory listing.

> **🚀 Auto-Create Upload Directories**: Sistem otomatis membuat folder upload yang diperlukan (seperti `public/uploads/products/`) saat pertama kali ada upload file. Tidak perlu dibuat manual!

#### 6. Jalankan Migration
```bash
php spark migrate --all
```

#### 7. Seed Data Awal
```bash
# Seed outlet dan users (WAJIB)
php spark db:seed InitialDataSeeder

# (Opsional) Seed produk sample
php spark db:seed ProductDataSeeder

# (Opsional) Seed promosi sample
php spark db:seed PromotionSeeder
```

#### 8. Jalankan Server
```bash
php spark serve
```

Buka browser: `http://localhost:8080`

### 🔑 Default Login Credentials

Setelah seeding, Anda dapat login dengan:

**Super Admin** (Akses semua outlet)
- Username: `admin`
- Password: `admin123`

**Manager** (Outlet Jakarta Pusat)
- Username: `manager1`
- Password: `manager123`

**Cashier** (Outlet Jakarta Pusat)
- Username: `cashier1`
- Password: `cashier123`

**Cashier** (Outlet Jakarta Selatan)
- Username: `cashier2`
- Password: `cashier123`

## 📚 Panduan Penggunaan

### 1️⃣ Login ke Sistem

1. Akses `http://localhost:8080`
2. Masukkan username dan password
3. Sistem akan redirect sesuai role:
   - **Admin** → Admin Dashboard
   - **Manager** → Manager Dashboard
   - **Cashier** → POS Interface

### 2️⃣ Untuk Admin

#### Mengelola Outlet
1. Menu **Admin** → **Outlets**
2. Klik **Add New Outlet** untuk tambah outlet baru
3. Isi form:
   - Code (unique, e.g., OUT001)
   - Name
   - Address
   - Phone
   - Status (Active/Inactive)
4. Klik **Save**

> **Note**: Outlet yang inactive tidak bisa melakukan transaksi

#### Mengelola User
1. Menu **Admin** → **Users**
2. Klik **Add New User**
3. Isi form:
   - Username (unique)
   - Email
   - Password
   - Role (Admin/Manager/Cashier)
   - Outlet (kosongkan untuk super admin)
   - Status (Active/Inactive)
4. Klik **Save**

> **Tip**: User inactive akan otomatis logout saat login

#### Mengelola Kategori
1. Menu **Admin** → **Categories**
2. Klik **Add New Category**
3. Isi nama kategori dan deskripsi
4. Klik **Save**

#### Mengelola Produk
1. Menu **Admin** → **Products**
2. Klik **Add New Product**
3. Isi form:
   - SKU (unique, akan otomatis uppercase)
   - Barcode (unique)
   - Nama produk
   - Kategori
   - Unit (porsi, pcs, kg, dll)
   - Harga jual
   - Harga pokok (HPP)
   - Tax configuration (optional)
   - Upload gambar produk (optional, max 2MB, format: JPG/PNG)
4. Klik **Save**

> **💡 Tips Upload Gambar:**
> - Format yang didukung: JPG, JPEG, PNG
> - Ukuran maksimal: 2MB
> - Folder `public/uploads/products/` otomatis dibuat jika belum ada
> - Gambar otomatis di-rename untuk keamanan
> - Tidak perlu membuat folder upload manual!

#### Mengatur Stok Per Outlet
1. Menu **Admin** → **Products**
2. Klik icon **Stock** (📦) pada produk
3. Atur stock untuk setiap outlet
4. Set minimum stock level (untuk alert)
5. Klik **Update Stock**

> **Real-time Update**: Stok akan otomatis terupdate via WebSocket (Pusher) ke semua kasir yang sedang online

#### Mengelola Promosi
1. Menu **Admin** → **Promotions**
2. Klik **Add New Promotion**
3. Isi form:
   - Kode promo (unique)
   - Nama promo
   - Tipe diskon (Percentage/Fixed Amount)
   - Nilai diskon
   - Tanggal mulai & selesai
   - Waktu aktif (optional, misal: 10:00-14:00)
   - Outlet (pilih outlet atau kosongkan untuk semua outlet)
4. Klik **Save**
5. Klik **Manage Items** untuk assign produk ke promo
6. Pilih produk yang masuk promo
7. Klik **Add Selected**

#### Melihat Laporan
1. Menu **Admin** → **Reports**
2. Filter berdasarkan:
   - Tanggal mulai & akhir
   - Outlet (optional - kosongkan untuk semua outlet)
   - Payment method (optional)
3. Klik **Filter**
4. Lihat summary: Total sales, Total transactions, Average transaction
5. Klik **View Detail** untuk detail transaksi per item
6. Export ready (extensible untuk PDF/Excel)

### 3️⃣ Untuk Manager

#### View Info Outlet
1. Menu **Manager** → **My Outlet**
2. Lihat informasi outlet Anda:
   - Outlet details
   - Total products
   - Total stock
   - Active staff

#### Update Stok
1. Menu **Manager** → **Products**
2. Lihat daftar produk dengan stok outlet Anda
3. Klik **Update Stock** pada produk
4. Masukkan jumlah stok baru
5. Klik **Update**

> **Note**: Manager hanya bisa update stok outlet sendiri

#### Lihat Laporan
1. Menu **Manager** → **Reports**
2. Filter berdasarkan tanggal
3. View hanya transaksi outlet sendiri
4. Lihat detail per transaksi

### 4️⃣ Untuk Kasir (POS Interface)

#### Melakukan Transaksi

**1. Login sebagai Cashier**
   - Sistem otomatis redirect ke POS Interface
   - POS tampil full-screen untuk kemudahan

**2. Pilih Kategori**
   - Sidebar kiri menampilkan kategori produk
   - Klik kategori untuk filter produk
   - Klik "All Products" untuk tampilkan semua

**3. Tambah Produk ke Cart**
   - Klik card produk untuk tambah ke cart (qty +1)
   - Atau klik tombol **+** untuk tambah quantity
   - Stok otomatis divalidasi (tidak bisa melebihi stok tersedia)

**4. Kelola Cart**
   - **+** untuk tambah quantity
   - **-** untuk kurangi quantity
   - **🗑️** untuk hapus item dari cart
   - Total, subtotal, dan diskon otomatis dihitung
   - Promo otomatis diaplikasikan jika ada

**5. Informasi Customer (Optional)**
   - Isi nama customer
   - Isi nomor HP customer
   - Data ini tersimpan di transaksi untuk keperluan follow-up

**6. Pilih Payment Method**
   - **Cash** - Pembayaran tunai
   - **Credit Card** - Kartu kredit/debit
   - **E-Wallet** - QRIS/GoPay/OVO/Dana

**7. Proses Pembayaran**
   - Klik **Process Payment**
   - Masukkan jumlah uang yang diterima
   - Sistem otomatis hitung kembalian
   - Jika kurang, akan muncul alert
   - Klik **Confirm Payment**

**8. Selesai**
   - Transaksi berhasil tersimpan
   - Stok otomatis berkurang
   - Klik **Print Receipt** untuk cetak struk
   - Klik **New Transaction** untuk transaksi baru

> **Tips POS:**
> - Gunakan Tab untuk quick navigation
> - Tekan Enter untuk confirm payment
> - Gunakan scanner barcode untuk input cepat (coming soon)
> - Promo dengan time range akan otomatis aktif/nonaktif sesuai jam

## 🧪 Testing

### Run Unit Tests
```bash
# Run semua tests
./vendor/bin/phpunit

# Run specific test suite
./vendor/bin/phpunit tests/unit/Models/

# Run dengan testdox format (lebih readable)
./vendor/bin/phpunit --testdox

# Run specific test file
./vendor/bin/phpunit tests/unit/Models/OutletModelTest.php

# Run dengan coverage report (requires xdebug)
./vendor/bin/phpunit --coverage-html coverage/
```

### Test Coverage
- ✅ **66 tests** passed
- ✅ **170 assertions** 
- ✅ **100% passing rate**

**Test Files:**
- `OutletModelTest.php` - 10 tests (Outlet CRUD & validation)
- `UserModelTest.php` - 13 tests (User management & authentication)
- `CategoryModelTest.php` - 14 tests (Category operations)
- `ProductModelTest.php` - 14 tests (Product management & stock)
- `PromotionModelTest.php` - 15 tests (Promotion & discount calculation)

## 🔧 Troubleshooting

### ❌ Error: "Cache unable to write to writable/cache/"

**Penyebab**: Permission folder `writable/` tidak sesuai.

**Solusi**:
```bash
# Set permission yang benar
chmod -R 755 writable/

# Atau untuk development (lebih permisif)
chmod -R 777 writable/

# Pastikan ownership benar (jika pakai Apache)
sudo chown -R www-data:www-data writable/
```

### ❌ Error Database Connection

**Penyebab**: Konfigurasi database di `.env` salah atau database belum dibuat.

**Solusi**:

1. Pastikan database sudah dibuat:
```sql
CREATE DATABASE pos_system;
```

2. Cek konfigurasi di `.env`:
```env
database.default.hostname = localhost
database.default.database = pos_system
database.default.username = root
database.default.password = your_password
database.default.DBDriver = MySQLi
```

3. Test koneksi:
```bash
php spark db:table users
```

### ❌ Error 500 Internal Server Error

**Penyebab**: Biasanya masalah permission, routing, atau konfigurasi web server.

**Solusi**:

**Untuk Apache:**
1. Pastikan document root mengarah ke folder `public/`
2. Pastikan file `.htaccess` ada di `public/`
3. Enable mod_rewrite:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

**Untuk Nginx:**
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/POS_System/public;
    
    index index.php index.html;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

**Untuk Development:**
```bash
# Selalu gunakan php spark serve untuk testing
php spark serve
```

### ❌ Login Gagal "Invalid credentials"

**Penyebab**: Data seeder belum dijalankan atau password salah.

**Solusi**:
```bash
# Jalankan seeder untuk buat user default
php spark db:seed InitialDataSeeder
```

Gunakan kredensial default:
- Username: `admin` / Password: `admin123`

### ❌ Error "Route not found"

**Penyebab**: URL rewriting tidak berfungsi atau baseURL salah.

**Solusi**:

1. Cek konfigurasi `.env`:
```env
app.baseURL = 'http://localhost:8080/'
```

2. Untuk Apache, pastikan `.htaccess` ada di folder `public/`

3. Untuk development, selalu gunakan:
```bash
php spark serve
```

### ❌ Real-time Update Tidak Berfungsi

**Penyebab**: Konfigurasi Pusher belum disetup atau salah.

**Solusi**:

1. Daftar di [Pusher.com](https://pusher.com) (Free plan tersedia)

2. Buat app baru dan copy credentials

3. Update `.env`:
```env
pusher.appId = your_app_id
pusher.appKey = your_app_key
pusher.appSecret = your_app_secret
pusher.appCluster = ap1
pusher.useTLS = true
```

4. Test connection via Pusher Debug Console

## 📁 Struktur Project

```
POS_System/
├── app/
│   ├── Controllers/
│   │   ├── Admin/          # Admin controllers
│   │   ├── Manager/        # Manager controllers
│   │   └── Cashier/        # Cashier/POS controllers
│   ├── Models/             # Database models
│   ├── Views/              # View templates
│   └── Libraries/          # Custom libraries (PusherService)
├── public/
│   ├── uploads/            # User uploads
│   │   └── products/       # Product images
│   ├── assets/             # Frontend assets
│   └── index.php           # Front controller
├── writable/
│   ├── cache/              # Cache files (auto-generated)
│   ├── logs/               # Log files (auto-generated)
│   ├── session/            # Session files (auto-generated)
│   └── uploads/            # Temp uploads (auto-generated)
├── tests/
│   └── unit/               # Unit tests
├── vendor/                 # Composer dependencies
├── .env                    # Environment config (copy from env)
├── composer.json           # Composer config
└── spark                   # CLI tool
```

## 🔐 Security Features

- ✅ **CSRF Protection** - Enabled by default
- ✅ **XSS Filtering** - Auto escape output
- ✅ **SQL Injection Prevention** - Query Builder protection
- ✅ **Password Hashing** - Bcrypt via Shield
- ✅ **Session Security** - HTTPOnly & Secure cookies
- ✅ **File Upload Validation** - Type & size checking
- ✅ **Role-Based Access Control** - Shield authorization
- ✅ **Directory Listing Protection** - index.html in writable folders

## 📖 Documentation

- [CodeIgniter 4 User Guide](https://codeigniter.com/user_guide/)
- [CodeIgniter Shield Documentation](https://shield.codeigniter.com)
- [Mazer Admin Template](https://zuramai.github.io/mazer/)
- [DataTables Documentation](https://datatables.net)
- [Pusher Documentation](https://pusher.com/docs/)
- [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.0/)

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

**Rizky Alamsyah**
- GitHub: [@RizkyAlamsyahB](https://github.com/RizkyAlamsyahB)

## 🙏 Acknowledgments

- CodeIgniter Team for the amazing framework
- Mazer Template for the beautiful admin UI
- All contributors and testers

---

<p align="center">
  <b>Made with ❤️ using CodeIgniter 4</b><br>
  <sub>POS System v1.0.0</sub>
</p>
