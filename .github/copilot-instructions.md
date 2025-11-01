# Copilot Instructions for POS System

## Project Overview
This is a **Multi-Outlet Point of Sale (POS) system** built on CodeIgniter 4 framework with **CodeIgniter Shield** authentication. The system supports multiple retail outlets with role-based access control (admin, manager, cashier).

**Current Branch:** `feature/auth` - Authentication system with login/logout and role-based dashboards

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
│   └── DashboardController.php   # Role-based dashboards
├── Models/            # Database models
│   ├── UserModel.php    # Extended Shield UserModel with outlet relationship
│   └── OutletModel.php  # Outlet management
├── Views/             # View templates
│   ├── layouts/       # Reusable layouts (main.php, dashboard.php)
│   ├── auth/          # Login page
│   ├── dashboard/     # Admin & Manager dashboards
│   └── pos/           # Cashier POS interface
├── Config/            # Configuration files
│   ├── Auth.php       # Shield auth configuration
│   ├── AuthGroups.php # Role and permission definitions
│   └── Routes.php     # Application routes
└── Database/          # Migrations and Seeds
    ├── Migrations/    # Database schema
    └── Seeds/         # Initial data (InitialDataSeeder.php)

public/                # Web-accessible files (document root)
tests/                 # PHPUnit tests
writable/              # Cache, logs, sessions, uploads (must be writable)
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
   - Extended with: `outlet_id` (NULL = super admin)
   - Shield manages: password hashing, email verification, authentication

3. **auth_groups_users** - Shield's role assignment table
   - Links users to groups (admin/manager/cashier)

### Upcoming Tables (Not Yet Implemented)
- **categories** - Product categories
- **products** - Product master data with barcode, SKU, pricing, tax
- **product_stocks** - Stock per outlet
- **promotions** - Promotion/discount master with date/time/outlet scope
- **promotion_items** - Products eligible for promotions
- **transactions** - Sales transaction headers
- **transaction_details** - Transaction line items

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
- Layouts use sections: `$this->extend('layout')` and `$this->section('content')`
- Access data as variables: `<?= esc($variable) ?>`
- Session flash messages: `session('message')`, `session('error')`
- CSRF protection: `<?= csrf_field() ?>` in forms
- Example:
  ```php
  <?= $this->extend('layouts/dashboard') ?>
  
  <?= $this->section('content') ?>
  <h1>Welcome, <?= esc($user->username) ?>!</h1>
  <?= $this->endSection() ?>
  ```

### Filters (Middleware)
- Shield provides filters: `session`, `group:admin`, `permission:users.create`
- Apply in routes: `$routes->group('admin', ['filter' => 'group:admin'], ...)`
- Custom filters go in `app/Filters/`

## Project-Specific Context

### Current State (feature/auth)
- ✅ Authentication system with Shield
- ✅ Login/logout functionality
- ✅ Three user roles: admin, manager, cashier
- ✅ Role-based dashboards
- ✅ Outlet management structure
- ⏳ Product management (upcoming)
- ⏳ POS transaction system (upcoming)
- ⏳ Promotion system (upcoming)
- ⏳ Reporting system (upcoming)

### Multi-Outlet Logic
- Super admin (`outlet_id = NULL`) can access all outlets
- Managers and cashiers are assigned to specific outlets (`outlet_id = 1, 2, 3...`)
- All transactional data must be linked to `outlet_id` for proper reporting

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

## Important Notes
- **Never commit `.env`** - use `env` as template
- **Document root is `public/`** - configure web server accordingly
- Use `php spark` for CLI commands
- Shield stores passwords hashed with bcrypt
- Default redirect after login is role-based (see `AuthController::getRedirectUrl()`)
- Bootstrap 5 and Bootstrap Icons CDN used for UI
