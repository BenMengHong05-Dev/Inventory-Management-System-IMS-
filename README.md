# Enterprise Inventory Management System (Laravel + Raw SQL)

A complete, production-ready Inventory Management System built strictly using **Laravel** and **pure raw SQL** parameterized queries (`DB::select`, `DB::insert`, `DB::update`, `DB::delete`, `DB::transaction`) across all controllers, satisfying all 53 specified enterprise requirement modules.

---

## 🚀 Quick Start: 3 Ways to Run

### Option 1: One-Click Windows Launcher (Recommended)
Simply double-click:
```
run.bat
```
Or run in PowerShell:
```powershell
.\run.ps1
```
The script will automatically detect your PHP installation (including XAMPP, Laragon, or system PATH), install dependencies, run migrations with seeds, and launch the local server at `http://127.0.0.1:8000`.

---

### Option 2: Docker (Zero Local PHP Required)
If you have Docker Desktop installed, run:
```bash
docker compose up -d
```
The application will automatically build the container with PHP 8.3, install dependencies, run migrations with seeds, and serve the application at **`http://localhost:8000`**.

---

### Option 3: Manual Terminal Commands
If you have PHP 8.2+ and Composer in your PATH:

```bash
# 1. Install dependencies
composer install

# 2. Setup environment (if not already done)
cp .env.example .env
php artisan key:generate

# 3. Initialize SQLite database & seed initial data
php artisan migrate --seed

# 4. Start local development server
php artisan serve
```

---

## 🔐 Default Admin Credentials

| Role | Email | Password | Access Scope |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `admin@inventory.com` | `password` | Full system access (all 40+ permissions) |

---

## 📦 System Modules (53 Enterprise Requirements)

1. **Authentication & RBAC**:
   - Secure login, logout, profile updates, password hashing (`Hash::make`).
   - Role-based permissions matrix across 4 default roles (`Super Admin`, `Warehouse Manager`, `Purchasing Officer`, `Inventory Staff`).
   - Bilingual localization toggle (English and Khmer).

2. **Dashboard & KPIs**:
   - Real-time stock valuation, total asset value, PO spend, stock flow.
   - Interactive Chart.js stock trend charts, low stock alerts, pending approvals.

3. **Item & Inventory Management**:
   - Master item catalog with SKU generation, barcodes, units, categories, and brands.
   - Multi-warehouse inventory balances (Quantity on hand, Reserved, Available).
   - Low stock threshold monitoring and stock transaction ledger.

4. **Stock Movement Workflows**:
   - **Stock In / Stock Out**: Inbound and outbound batch receipts and dispatches.
   - **Stock Transfer**: Multi-warehouse transfer with in-transit stock deduction and destination receipt.
   - **Stock Adjustment**: Reason codes (loss, damage, audit correction) with dual-control approval.
   - **Stock Take**: Warehouse snapshotting, physical count entries, variance calculation, and reconciliation.
   - **Item Returns**: Returns to suppliers (debit memo) and returns from employees back to stock.

5. **Purchasing Cycle**:
   - **Purchase Requests (PR)**: Department requisitions with separation of duties on approvals.
   - **Purchase Orders (PO)**: Supplier PO generation with tax/total calculations.
   - **Goods Receiving (GRN)**: Partial delivery tracking, auto-restocking, and PO closure.

6. **Fixed Assets & Equipment**:
   - Asset register with QR code and barcode label generation.
   - Employee check-out (assignments) and check-in (returns) with condition inspection.
   - Maintenance ticketing (preventive, corrective, calibration) with vendor cost tracking.
   - Asset disposal / write-off authorization.
   - Warranty tracking with expiration countdown.

7. **Master Data Management**:
   - Categories, Brands, Units of Measurement, Suppliers, Warehouses, Storage Locations/Bins, Departments, and Employees.

8. **Reports & Audit**:
   - Financial Valuation, Inventory Levels, Asset Register, and Purchasing Spend reports with instant CSV exports.
   - Immutable audit trail capturing IP addresses, user agents, and before/after JSON diffs.

9. **Barcode & REST API**:
   - Printable label sheets for Code-128 barcodes and QR codes (`/labels/items`, `/labels/assets`).
   - REST API endpoints for handheld barcode scanner lookups (`/api/lookup/{code}`).

---

## 🛠️ Technology Stack & Architecture

- **Backend**: Laravel 11
- **Database**: SQLite (default zero-configuration) or MySQL (configurable via `.env`)
- **Queries**: 100% Raw SQL parameterized queries via `Illuminate\Support\Facades\DB`
- **Frontend**: Tailwind CSS, Alpine.js, Lucide Icons, Chart.js, JsBarcode, QRCode.js
- **Security**: Parameterized queries against SQL injection, CSRF protection, separation of duties on approvals, immutable audit logging
