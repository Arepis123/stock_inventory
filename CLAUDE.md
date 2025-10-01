# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel-based Stock Inventory Management System for safety equipment distribution, built with Livewire and Flux UI components. The application manages distribution of safety helmets and t-shirts across multiple regions and warehouses with QR code functionality.

## Technology Stack

- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Livewire 3, Flux UI (Pro), Volt
- **Database**: Configured for SQLite/MySQL
- **Build Tools**: Vite 6, TailwindCSS 4
- **Testing**: Pest PHP

## Development Commands

### Initial Setup
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

### Development Server
```bash
composer run dev
# This runs: php artisan serve + php artisan queue:listen + npm run dev concurrently
```

### Individual Commands
```bash
# Backend server
php artisan serve

# Frontend development
npm run dev

# Queue worker
php artisan queue:listen --tries=1

# Build assets
npm run build
```

### Testing
```bash
composer run test
# Equivalent to: php artisan config:clear && php artisan test
```

### Database Management
```bash
php artisan migrate
php artisan make:migration create_table_name
php artisan make:model ModelName
```

### Livewire Components
```bash
php artisan make:livewire ComponentName
```

## Application Architecture

### Core Models
- **StockInventory**: Main distribution records with equipment tracking
- **StockManagement**: Central inventory management
- **ABMStorage**: Regional storage tracking
- **Staff**: Active staff members for distribution
- **Region/Warehouse**: Geographic organization
- **QrScan**: QR code functionality for distribution tracking

### Key Livewire Components
- **Dashboard**: Comprehensive analytics and statistics dashboard
- **StockInventoryForm**: Main distribution form with dual-source deduction
- **Admin Components**: Management interfaces for regions, warehouses, staff, and stock

### Equipment Tracking System
The application uses a dual-tracking approach:
1. **Legacy fields**: `for_use_stock`, `for_storing`, `quantity`
2. **Equipment-specific fields**: `helmet_quantity`, `tshirt_quantity`
3. **Usage breakdown**: `for_use_helmets/tshirts`, `for_storing_helmets/tshirts`

### Stock Deduction Sources
- **total_stocks**: Deduct from main inventory, optionally add to regional ABM storage
- **abm_storage**: Deduct from regional ABM centre storage

### Route Structure
- `/dashboard`: Main analytics dashboard
- `/stock-inventory`: Distribution form
- `/stock-distribution`: QR code distribution interface
- `/admin/*`: Administrative management interfaces
- QR code routes: `/qr/generate`, `/qr/scan/{token}`

### PDF/Excel Export
- Uses `barryvdh/laravel-dompdf` for PDF generation
- Uses `phpoffice/phpspreadsheet` for Excel exports
- Export available via `/admin/export/distributions`

## Database Schema Notes

### Key Tables
- `stock_inventories`: Main distribution records
- `stock_management`: Central inventory tracking
- `abm_storages`: Regional storage tracking
- `qr_scans`: QR code tracking and validation
- `regions`, `warehouses`, `staff`: Master data

### Important Fields
- Equipment quantities are tracked separately for helmets and t-shirts
- `deduction_source` determines inventory source
- Usage breakdown allows tracking contractor vs storage allocation

## Testing

- Uses Pest PHP testing framework
- Test suites: Unit (`tests/Unit`) and Feature (`tests/Feature`)
- SQLite in-memory database for testing
- Run tests with: `composer run test`

## Development Notes

- Application uses Flux UI Pro components (requires license)
- QR code functionality includes rate limiting middleware
- Form validation includes stock availability checking
- Toast notifications via Flux for user feedback
- Supports both legacy and new equipment tracking approaches