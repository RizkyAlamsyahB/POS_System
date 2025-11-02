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
- **Testing**: PHPUnit 10.5+

## 🚀 Quick Start

### Prerequisites

Pastikan sistem Anda sudah memiliki:
- PHP 8.1 atau lebih tinggi
- Composer
- MySQL 5.7+ atau MariaDB 10.3+
- Web server (Apache/Nginx) atau PHP built-in server

### Installation

1. **Clone Repository**
```bash
git clone https://github.com/RizkyAlamsyahB/POS_System.git
cd POS_System
```

2. **Install Dependencies**
```bash
composer install
```

3. **Setup Environment**
```bash
cp env .env
```

Edit file `.env`:
```env
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost:8080/'

database.default.hostname = localhost
database.default.database = pos_system
database.default.username = root
database.default.password = your_password
database.default.DBDriver = MySQLi

# Pusher WebSocket Configuration (Real-time Updates)
pusher.appId = your_pusher_app_id
pusher.appKey = your_pusher_app_key
pusher.appSecret = your_pusher_app_secret
pusher.appCluster = ap1
pusher.useTLS = true
```

4. **Buat Database**
```bash
# Login ke MySQL
mysql -u root -p

# Buat database
CREATE DATABASE pos_system;
exit;
```

5. **Jalankan Migration**
```bash
php spark migrate --all
```

6. **Seed Data Awal**
```bash
# Seed outlet dan users
php spark db:seed InitialDataSeeder

# (Opsional) Seed produk sample
php spark db:seed ProductDataSeeder

# (Opsional) Seed promosi sample
php spark db:seed PromotionSeeder
```

7. **Jalankan Server**
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
   - SKU (unique)
   - Barcode (unique)
   - Nama produk
   - Kategori
   - Unit (porsi, pcs, kg, dll)
   - Harga jual
   - Harga pokok
   - Tax (optional)
   - Upload gambar (optional)
4. Klik **Save**

#### Mengatur Stok Per Outlet
1. Menu **Admin** → **Products**
2. Klik icon **Stock** pada produk
3. Atur stock untuk setiap outlet
4. Set minimum stock level (untuk alert)
5. Klik **Update**

#### Mengelola Promosi
1. Menu **Admin** → **Promotions**
2. Klik **Add New Promotion**
3. Isi form:
   - Kode promo (unique)
   - Nama promo
   - Tipe diskon (Percentage/Fixed Amount)
   - Nilai diskon
   - Tanggal mulai & selesai
   - Waktu aktif (optional)
   - Outlet (pilih outlet atau kosongkan untuk semua)
4. Klik **Save**
5. Klik **Manage Items** untuk assign produk ke promo
6. Pilih produk yang masuk promo
7. Klik **Add Selected**

#### Melihat Laporan
1. Menu **Admin** → **Reports**
2. Filter berdasarkan:
   - Tanggal mulai & akhir
   - Outlet (optional)
   - Payment method (optional)
3. Klik **Filter**
4. Klik **View Detail** untuk detail transaksi
5. Export ready (extensible)

### 3️⃣ Untuk Manager

#### View Info Outlet
1. Menu **Manager** → **My Outlet**
2. Lihat informasi outlet Anda

#### Update Stok
1. Menu **Manager** → **Products**
2. Klik **Update Stock**
3. Masukkan jumlah stok baru
4. Klik **Update**

#### Lihat Laporan
1. Menu **Manager** → **Reports**
2. Filter berdasarkan tanggal
3. View hanya transaksi outlet sendiri

### 4️⃣ Untuk Kasir (POS)

#### Melakukan Transaksi

1. **Login sebagai Cashier**
   - Sistem otomatis redirect ke POS

2. **Pilih Kategori**
   - Sidebar kiri menampilkan kategori
   - Klik kategori untuk filter produk

3. **Tambah Produk ke Cart**
   - Klik produk untuk tambah ke cart
   - Atau klik tombol **+** untuk tambah quantity

4. **Kelola Cart**
   - **+/-** untuk ubah quantity
   - **🗑️** untuk hapus item
   - Total otomatis dihitung

5. **Informasi Customer (Optional)**
   - Isi nama customer
   - Isi nomor HP customer

6. **Pilih Payment Method**
   - Cash
   - Credit Card
   - E-Wallet

7. **Proses Pembayaran**
   - Klik **Process Payment**
   - Masukkan jumlah uang yang diterima
   - Sistem otomatis hitung kembalian
   - Klik **Confirm Payment**

8. **Print Receipt**
   - Setelah sukses, klik **Print Receipt**
   - Atau klik **New Transaction** untuk transaksi baru

## 🧪 Testing

### Run Unit Tests
```bash
# Run semua tests
./vendor/bin/phpunit

# Run specific test suite
./vendor/bin/phpunit tests/unit/Models/

# Run dengan testdox format
./vendor/bin/phpunit --testdox

# Run specific test file
./vendor/bin/phpunit tests/unit/Models/OutletModelTest.php
```

**Test Coverage:**
- ✅ 66 tests
- ✅ 170 assertions
- ✅ 100% passing
- OutletModel: 10 tests
- UserModel: 13 tests
- CategoryModel: 14 tests
- ProductModel: 14 tests
- PromotionModel: 15 tests

##  Documentation

- [CodeIgniter 4 User Guide](https://codeigniter.com/user_guide/)
- [CodeIgniter Shield Docs](https://shield.codeigniter.com)
- [Mazer Template Docs](https://zuramai.github.io/mazer/)
- [DataTables Documentation](https://datatables.net)
- [Pusher Documentation](https://pusher.com/docs/)

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

**Made with ❤️ using CodeIgniter 4**

