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

Edit file `.env`:
```env
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080/'

database.default.hostname = localhost
database.default.database = pos_system
database.default.username = root
database.default.password = your_password
database.default.DBDriver = MySQLi
database.default.DBPrefix = 

# Pusher WebSocket Configuration
pusher.appId = your_pusher_app_id
pusher.appKey = your_pusher_app_key
pusher.appSecret = your_pusher_app_secret
pusher.appCluster = ap1
pusher.useTLS = true
```

#### 4. Buat Database
```bash
mysql -u root -p
CREATE DATABASE pos_system;
exit;
```

#### 5. Jalankan Migration
```bash
php spark migrate --all
```

#### 6. Seed Data Awal
```bash
php spark db:seed InitialDataSeeder
php spark db:seed ProductDataSeeder
php spark db:seed PromotionSeeder
```

#### 7. Jalankan Server
```bash
php spark serve
```

Buka browser: `http://localhost:8080`

### 🔑 Default Login Credentials

**Super Admin**
* Username: `admin`
* Password: `admin123`

**Manager**
* Username: `manager1`
* Password: `manager123`

**Cashier (Jakarta Pusat)**
* Username: `cashier1`
* Password: `cashier123`

**Cashier (Jakarta Selatan)**
* Username: `cashier2`
* Password: `cashier123`

---

## 🧪 Testing

### Run Unit Tests
```bash
./vendor/bin/phpunit
```

### Test Coverage
* ✅ **66 tests** passed
* ✅ **170 assertions**
* ✅ **100% passing rate**

**Test Files:**
* `OutletModelTest.php` - 10 tests (Outlet CRUD & validation)
* `UserModelTest.php` - 13 tests (User management & authentication)
* `CategoryModelTest.php` - 14 tests (Category operations)
* `ProductModelTest.php` - 14 tests (Product management & stock)
* `PromotionModelTest.php` - 15 tests (Promotion & discount calculation)

---

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

**Rizky Alamsyah**
GitHub: [@RizkyAlamsyahB](https://github.com/RizkyAlamsyahB)

---

<p align="center">
  <b>Made with ❤️ using CodeIgniter 4</b><br>
  <sub>POS System v1.0.0</sub>
</p>
