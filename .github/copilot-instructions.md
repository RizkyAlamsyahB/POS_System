# Copilot Instructions for POS System

## Project Overview
This is a **Multi-Outlet Point of Sale (POS) system** built on CodeIgniter 4 framework with **CodeIgniter Shield** authentication. The system supports multiple retail outlets with role-based access control (admin, manager, cashier).

**Current Branch:** `dev` - Main development branch with complete POS functionality

## Architecture & Structure

### Core Framework: CodeIgniter 4
- **Entry Point**: `public/index.php` (NOT in project root - web server must point to `public/` folder)
- **CLI Tool**: `./spark` - CodeIgniter's command-line interface for migrations, scaffolding, and maintenance
- **PHP Version**: 8.1+ required (8.2+ recommended for deprecation compatibility)
- **Database**: MySQLi by default (configured in `app/Config/Database.php`)
- **Authentication**: CodeIgniter Shield v1.2.0 for user authentication and authorization

### Directory Structure
```
app/
├── Controllers/        # HTTP request handlers
│   ├── AuthController.php        # Login/logout handling
│   └── DashboardController.php   # Role-based dashboards + POS
├── Models/            # Database models
│   ├── UserModel.php    # Extended Shield UserModel with outlet relationship
│   └── OutletModel.php  # Outlet management
├── Views/             # View templates
│   ├── layouts/       # Reusable layouts
│   │   ├── app.php            # Main Mazer admin layout (used by admin-mazer.php)
│   │   ├── main.php           # Simple layout (used by login)
│   │   ├── dashboard.php      # Alternative dashboard layout
│   │   └── partials/
│   │       └── sidebar.php    # Dynamic sidebar based on user role
│   ├── auth/          # Login page
│   ├── dashboard/     # Admin & Manager dashboards
│   │   ├── admin-mazer.php    # Admin dashboard using Mazer template
│   │   └── manager.php        # Manager dashboard
│   └── pos/           # Cashier POS interface
│       └── index.php          # Full-screen POS (extends app.php, hides sidebar)
├── Config/            # Configuration files
│   ├── Auth.php       # Shield auth configuration
│   ├── AuthGroups.php # Role and permission definitions
│   └── Routes.php     # Application routes
└── Database/          # Migrations and Seeds
    ├── Migrations/    # Database schema versions
    │   ├── 2025-11-01-001516_CreateOutletsTable.php
    │   └── 2025-11-01-001528_AddOutletIdToUsersTable.php
    └── Seeds/         # Initial data
        └── InitialDataSeeder.php

public/                # Web-accessible files (document root)
├── index.php          # Front controller (NOT in project root!)
└── mazer/             # Mazer admin template assets (CSS, JS, fonts, images)
tests/                 # PHPUnit tests
writable/              # Cache, logs, sessions, uploads (must be writable by web server)
```

### Namespace Conventions
- Controllers: `namespace App\Controllers;`
- Models: `namespace App\Models;`
- Config: `namespace Config;` (NOT `App\Config`)
- PSR-4 autoloading configured in `composer.json`

## Authentication & Authorization (CodeIgniter Shield)

### User Roles (Groups)
Defined in `app/Config/AuthGroups.php`:
- **admin**: Full access to all outlets, users, and system settings
- **manager**: Access to outlet management, reports, products, inventory, promotions
- **cashier**: POS transaction access only

### User Model Extension
Custom `App\Models\UserModel` extends Shield's UserModel:
- Adds `outlet_id` field (NULL = super admin with access to all outlets)
- Methods: `getUserWithOutlet()`, `getUsersByOutlet()`, `isSuperAdmin()`, `getUserOutletId()`

### Login Credentials (from InitialDataSeeder)
```
Admin:    admin / admin123 (Super Admin - All Outlets)
Manager:  manager1 / manager123 (Outlet Jakarta Pusat)
Cashier:  cashier1 / cashier123 (Outlet Jakarta Pusat)
Cashier:  cashier2 / cashier123 (Outlet Jakarta Selatan)
```

### Route Protection
Routes use Shield's `group` filter for role-based access:
```php
$routes->group('admin', ['filter' => 'group:admin'], function($routes) {
    $routes->get('dashboard', 'DashboardController::adminDashboard');
});
```

### Role-Based Redirection
After login, users are redirected based on role:
- **admin** → `/admin/dashboard`
- **manager** → `/manager/dashboard`
- **cashier** → `/pos`

## Development Workflows

### First-Time Setup
```bash
# 1. Install dependencies
composer install

# 2. Copy environment file and configure
cp env .env
# Edit .env to set:
#   - CI_ENVIRONMENT = development
#   - app.baseURL = 'http://localhost:8080/'
#   - database.default.* (hostname, database, username, password)

# 3. Run migrations (creates all tables including Shield auth tables)
php spark migrate

# 4. Seed initial data (creates 3 outlets and 4 users)
php spark db:seed InitialDataSeeder

# 5. Start development server
php spark serve
# Access at: http://localhost:8080
```

### Environment Setup
1. Copy `env` to `.env` and configure:
   - `app.baseURL` - Set to your local URL (default: `http://localhost:8080/`)
   - `database.default.*` - Database credentials
   - `CI_ENVIRONMENT` - Set to `development` for detailed errors

### Running the Application
```bash
# Development server (uses PHP built-in server)
php spark serve

# Default runs on http://localhost:8080
# Custom port: php spark serve --port=8000
```

### Database Operations
```bash
# Run all migrations (includes Shield auth tables)
php spark migrate

# Rollback migrations
php spark migrate:rollback

# Run initial data seeder (creates outlets and users)
php spark db:seed InitialDataSeeder

# Check migration status
php spark migrate:status
```

### Shield Setup (Already Done)
```bash
# Install Shield (already in composer.json)
composer require codeigniter4/shield

# Publish Shield config and migrations
php spark shield:setup
```

### Code Generation
```bash
# Generate controller
php spark make:controller ControllerName

# Generate model
php spark make:model ModelName

# Generate migration
php spark make:migration MigrationName

# Generate seeder
php spark make:seeder SeederName
```

### Testing
```bash
# Run all tests
composer test
# OR
./vendor/bin/phpunit
```

## Database Schema (Multi-Outlet POS)

### Core Tables
1. **outlets** - Retail outlet/store master data
   - Primary key: `id`
   - Unique: `code` (e.g., OUT001)
   - Fields: `name`, `address`, `phone`, `is_active`

2. **users** - Shield users table (extended)
   - Extended with: `outlet_id` (NULL = super admin), `active` (user account status)
   - Shield manages: password hashing, email verification, authentication

3. **auth_groups_users** - Shield's role assignment table
   - Links users to groups (admin/manager/cashier)

4. **categories** - Product categories ✅ IMPLEMENTED
   - Fields: `name`, `description`
   - Used to organize products in POS interface

5. **products** - Product master data ✅ IMPLEMENTED
   - Fields: `sku`, `barcode`, `name`, `description`, `category_id`, `price`, `cost`, `tax_percentage`, `image`
   - Shared across all outlets (master data)

6. **product_stocks** - Stock per outlet ✅ IMPLEMENTED
   - Links: `product_id`, `outlet_id`
   - Fields: `quantity`, `min_stock_level`
   - Tracks inventory per outlet separately

7. **promotions** - Promotion/discount master ✅ IMPLEMENTED
   - Fields: `code`, `name`, `discount_type` (percentage/fixed), `discount_value`, `start_date`, `end_date`, `is_active`
   - Can be scoped to specific outlets via `outlet_id` (NULL = all outlets)

8. **promotion_items** - Products eligible for promotions ✅ IMPLEMENTED
   - Links: `promotion_id`, `product_id`

9. **transactions** - Sales transaction headers ✅ IMPLEMENTED
   - Links: `outlet_id`, `user_id` (cashier)
   - Fields: `transaction_number`, `subtotal`, `tax`, `discount`, `total`, `payment_method`, `customer_name`, `customer_phone`

10. **transaction_details** - Transaction line items ✅ IMPLEMENTED
    - Links: `transaction_id`, `product_id`
    - Fields: `quantity`, `unit_price`, `subtotal`, `discount`, `total`

## CodeIgniter 4 Specific Patterns

### Controllers
- **Always extend** `App\Controllers\BaseController`
- Use `auth()->user()` to get current logged-in user
- Use `auth()->loggedIn()` to check if user is authenticated
- Check roles: `auth()->user()->inGroup('admin')`
- Example:
  ```php
  class DashboardController extends BaseController
  {
      public function adminDashboard()
      {
          $data = [
              'title' => 'Admin Dashboard',
              'user'  => auth()->user(),
          ];
          return view('dashboard/admin', $data);
      }
  }
  ```

### Models
- Extend `CodeIgniter\Model` for built-in query builder and validation
- For user management, extend `CodeIgniter\Shield\Models\UserModel`
- Set `$table`, `$primaryKey`, `$allowedFields` properties
- Use `$useTimestamps = true` for auto `created_at`/`updated_at`
- Example:
  ```php
  class OutletModel extends Model
  {
      protected $table = 'outlets';
      protected $primaryKey = 'id';
      protected $allowedFields = ['code', 'name', 'address', 'phone', 'is_active'];
      protected $useTimestamps = true;
  }
  ```

### Routing
- Routes defined in `app/Config/Routes.php`
- Shield auth routes: `service('auth')->routes($routes);`
- Group routes by role using filters
- Named routes: `['as' => 'login']` allows `url_to('login')`

### Views
- **Layout System**: Views extend base layouts using CodeIgniter's view sections
- **Three layouts in use**:
  1. **app.php** (`layouts/app.php`) - Main Mazer admin template with sidebar for admin/manager
  2. **main.php** (`layouts/main.php`) - Simple layout for login page
  3. **dashboard.php** - Alternative dashboard layout (if needed)
- **POS Override**: `pos/index.php` extends `layouts/app` but hides sidebar via CSS for full-screen UX
- View inheritance pattern:
  ```php
  <?= $this->extend('layouts/app') ?>
  
  <?= $this->section('styles') ?>
  <style>/* Custom CSS */</style>
  <?= $this->endSection() ?>
  
  <?= $this->section('content') ?>
  <h1>Welcome, <?= esc($user->username) ?>!</h1>
  <?= $this->endSection() ?>
  
  <?= $this->section('scripts') ?>
  <script>/* Custom JS */</script>
  <?= $this->endSection() ?>
  ```
- **Sidebar**: Dynamic menu in `layouts/partials/sidebar.php` - shows different menu items based on user role
- Access data as variables: `<?= esc($variable) ?>`
- Session flash messages: `session('message')`, `session('error')`
- CSRF protection: `<?= csrf_field() ?>` in forms

### Filters (Middleware)
- Shield provides filters: `session`, `group:admin`, `permission:users.create`
- Apply in routes: `$routes->group('admin', ['filter' => 'group:admin'], ...)`
- Custom filters go in `app/Filters/`

## Project-Specific Context

### Current State (dev branch)
- ✅ Authentication system with Shield
- ✅ Login/logout functionality with user active status check
- ✅ Three user roles: admin, manager, cashier
- ✅ Mazer admin template integration (CDN-based)
- ✅ Role-based dashboards
- ✅ **Fully functional POS interface** with real backend integration
- ✅ **Product & Category Management** - Full CRUD operations
- ✅ **Stock Management** - Per-outlet inventory tracking
- ✅ **Promotion System** - Discount management with product assignments
- ✅ **Transaction System** - Complete checkout flow with database persistence
- ✅ **Reporting System** - Sales reports for admin and managers
- ✅ **User Management** - Admin can create/update/deactivate users
- ✅ **Outlet Management** - Admin controls outlet status (active/inactive)

### Implementation Details

#### Controllers Organization
- **Admin Controllers** (`app/Controllers/Admin/`):
  - `OutletController` - Outlet CRUD + DataTables
  - `UserController` - User CRUD + role assignment + status toggle
  - `CategoryController` - Category CRUD
  - `ProductController` - Product CRUD + stock management + image upload
  - `PromotionController` - Promotion CRUD + product assignments
- **Manager Controllers** (`app/Controllers/Manager/`):
  - `OutletController` - View own outlet only
  - `ProductController` - View products + update stock for assigned outlet
- **PosController** - Handles checkout transactions (AJAX)
- **ReportController** - Sales reports with DataTables for admin/manager
- **DashboardController** - Role-based dashboard views + POS interface

### Mazer Admin Template Integration
- **CDN-based**: Mazer CSS/JS loaded from `https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/`
- **Layout**: `app/Views/layouts/app.php` - Main admin layout with sidebar
- **Partials**: `layouts/partials/sidebar.php` - Dynamic sidebar based on user role (admin/manager/cashier menus)
- **POS Override**: POS interface extends `app.php` but hides sidebar with CSS (`#sidebar { display: none !important; }`) for full-screen experience
- **Features**: Dark mode toggle, responsive sidebar, active menu highlighting, perfect-scrollbar
- **Icon Library**: Iconly icons for UI components

### POS Interface Design (app/Views/pos/index.php)
- **Layout Override**: Extends `layouts/app` but hides Mazer sidebar via CSS for full-screen cashier experience
- **Layout**: Three-column design (category sidebar | product grid | invoice cart)
- **Responsive**: Mobile-friendly with collapsible invoice panel
- **Frontend Cart Management**: JavaScript-based cart with quantity controls
- **Product Display**: Grid layout with product images from database
- **Backend Integration**: AJAX POST to `/pos/checkout` for transaction processing
- **Stock Validation**: Real-time stock checking per outlet during checkout
- **Payment Methods**: Credit Card, PayPal, Cash (UI + backend support)
- **Mobile Behavior**: 
  - Cart slides in from right on product add
  - Bottom-right floating cart toggle button
  - Category sidebar hidden on very small screens

### DataTables Integration
All admin/manager list views use DataTables CDN for server-side pagination:
- **CDN**: `https://cdn.datatables.net/1.13.6/` (Bootstrap 5 theme)
- **Pattern**: AJAX endpoint `/datatable` returns JSON for each resource
- **Features**: Search, sort, pagination, responsive design
- **Example**: `admin/products/datatable`, `admin/outlets/datatable`

### Seeder System
Multiple seeders available for development:
- **InitialDataSeeder**: Creates outlets + users (run first)
- **ProductDataSeeder**: Creates categories + 20+ food/beverage products with images
- **PromotionSeeder**: Creates sample promotions with product assignments
- Run with: `php spark db:seed SeederName`

### Multi-Outlet Logic
- Super admin (`outlet_id = NULL`) can access all outlets
- Managers and cashiers are assigned to specific outlets (`outlet_id = 1, 2, 3...`)
- All transactional data must be linked to `outlet_id` for proper reporting

#### Outlet Status (Active/Inactive)
Outlets have `is_active` status that controls access and functionality:

| Component | Behavior when outlet is INACTIVE |
|-----------|----------------------------------|
| **Login** | Manager/cashier can still login (authentication is global) |
| **Filter** | `OutletActiveFilter` checks outlet status and blocks write operations |
| **POS Transactions** | ❌ Blocked - Cannot create new transactions |
| **Dashboard** | ✅ Accessible - Shows warning alert about inactive status |
| **Reports/History** | ✅ Accessible - Can view historical data (read-only) |
| **Master Data** | ❌ Blocked - Cannot create/update/delete products, stock, etc. |
| **Admin Access** | ✅ Always allowed - Admin bypasses outlet status checks |

**Implementation:**
- `app/Filters/OutletActiveFilter.php` - Middleware that checks outlet status
- Applied to routes: `manager/*` and `pos` routes
- Blocks routes containing: `/store`, `/update`, `/delete`, `/create`, `pos`
- Allows read-only access to dashboard and reports
- Shows warning alert in manager dashboard when outlet is inactive

**Filter Usage:**
```php
// In Routes.php
$routes->group('manager', ['filter' => 'group:manager|outletactive'], ...)
$routes->group('', ['filter' => 'group:admin,manager,cashier|outletactive'], ...)
```

#### User Active Status
Users have `active` field in database:
- **ActiveUserFilter** (`app/Filters/ActiveUserFilter.php`) forces logout for inactive users
- Admin can toggle user status via `/admin/users/toggle-status/:id`
- Applied globally to all authenticated routes
- Prevents inactive users from accessing any part of the system

### Common POS Patterns (To Be Implemented)
When implementing POS features, follow these patterns:
- **Master data**: Products, categories (shared across outlets)
- **Stock management**: `product_stocks` table tracks inventory per outlet
- **Promotions**: Can be scoped to all outlets or specific outlets
- **Transactions**: Must capture outlet_id, user_id, and payment details
- **Reporting**: Filter by outlet_id unless user is super admin

### Database Conventions
- Use migrations for all schema changes (never manual SQL)
- Timestamps: `created_at`, `updated_at` (auto-handled by Model with `$useTimestamps = true`)
- Soft deletes: Use `deleted_at` for important transactional data
- Foreign keys: Always define relationships with `ON DELETE` and `ON UPDATE` actions
- Unique constraints: Use for business keys (outlet code, product SKU, barcode)

### Testing Strategy
PHPUnit test suite includes:
- **Feature Tests**: 
  - `tests/feature/Admin/` - Admin controller integration tests
  - `tests/feature/Manager/` - Manager controller integration tests
- **Unit Tests**: 
  - `tests/unit/Controllers/` - Controller unit tests
  - `tests/unit/Models/` - Model unit tests
  - `tests/unit/Filters/` - Filter unit tests
- **Run Tests**: `composer test` or `./vendor/bin/phpunit`
- Test database configured separately in `.env` (`CI_ENVIRONMENT=testing`)

## Important Notes
- **Never commit `.env`** - use `env` as template
- **Document root is `public/`** - configure web server accordingly
- Use `php spark` for CLI commands
- Shield stores passwords hashed with bcrypt
- Default redirect after login is role-based (see `AuthController::getRedirectUrl()`)
- Bootstrap 5 and Bootstrap Icons CDN used for UI
- Mazer admin template loaded via CDN (not bundled locally except `/public/mazer/` assets)

## Future Enhancement Ideas
1. **Advanced Reporting**:
   - Export reports to PDF/Excel
   - Graphical charts for sales trends
   - Profit margin analysis per product
   
2. **Customer Management**:
   - Customer database with loyalty points
   - Purchase history tracking
   - Customer-specific promotions
   
3. **Inventory Alerts**:
   - Low stock email notifications
   - Automatic reorder suggestions
   - Stock movement tracking (transfers between outlets)
   
4. **Multi-Language Support**:
   - CodeIgniter's built-in localization
   - Language switcher in UI
   
5. **Payment Integration**:
   - Integrate with payment gateways (Stripe, PayPal API)
   - Split payments support
   - Electronic receipt via email/SMS

