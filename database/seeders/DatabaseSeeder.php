<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Roles via Raw SQL
        $roles = [
            ['name' => 'Super Administrator', 'slug' => 'super-admin', 'description' => 'Full access to all system modules'],
            ['name' => 'Inventory Manager', 'slug' => 'inventory-manager', 'description' => 'Can manage inventory, approvals, and stock operations'],
            ['name' => 'Warehouse Staff', 'slug' => 'warehouse-staff', 'description' => 'Can receive, dispatch, and count physical inventory'],
            ['name' => 'Viewer', 'slug' => 'viewer', 'description' => 'Read-only access to inventory, reports, and assets'],
        ];

        foreach ($roles as $r) {
            DB::insert("INSERT IGNORE INTO roles (name, slug, description, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())", [
                $r['name'], $r['slug'], $r['description']
            ]);
        }

        // 2. Seed Permissions via Raw SQL
        $modules = [
            'dashboard' => ['view'],
            'users' => ['view', 'create', 'edit', 'delete'],
            'roles' => ['view', 'create', 'edit', 'delete'],
            'categories' => ['view', 'create', 'edit', 'delete'],
            'brands' => ['view', 'create', 'edit', 'delete'],
            'units' => ['view', 'create', 'edit', 'delete'],
            'items' => ['view', 'create', 'edit', 'delete', 'export'],
            'suppliers' => ['view', 'create', 'edit', 'delete'],
            'warehouses' => ['view', 'create', 'edit', 'delete'],
            'locations' => ['view', 'create', 'edit', 'delete'],
            'inventory' => ['view', 'export'],
            'stock_in' => ['view', 'create', 'approve'],
            'stock_out' => ['view', 'create', 'approve'],
            'stock_transfer' => ['view', 'create', 'approve', 'dispatch', 'receive'],
            'stock_adjustment' => ['view', 'create', 'approve'],
            'stock_take' => ['view', 'create', 'count', 'approve'],
            'purchase_requests' => ['view', 'create', 'approve'],
            'purchase_orders' => ['view', 'create', 'approve'],
            'goods_receipts' => ['view', 'create'],
            'assets' => ['view', 'create', 'edit', 'delete', 'export'],
            'asset_assignments' => ['view', 'create'],
            'asset_returns' => ['view', 'create'],
            'maintenance' => ['view', 'create', 'edit'],
            'disposals' => ['view', 'create'],
            'departments' => ['view', 'create', 'edit', 'delete'],
            'employees' => ['view', 'create', 'edit', 'delete'],
            'reports' => ['view', 'export'],
            'audit_logs' => ['view'],
        ];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $slug = "{$module}.{$action}";
                $name = ucwords(str_replace('_', ' ', $module)) . ' ' . ucfirst($action);
                DB::insert("INSERT IGNORE INTO permissions (name, slug, module, description, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())", [
                    $name, $slug, $module, "Allows user to {$action} {$module}"
                ]);
            }
        }

        // 3. Assign All Permissions to Super Administrator
        $superAdminRoleId = DB::select("SELECT id FROM roles WHERE slug = 'super-admin' LIMIT 1")[0]->id;
        $allPermIds = DB::select("SELECT id FROM permissions");
        foreach ($allPermIds as $p) {
            DB::insert("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (?, ?)", [
                $superAdminRoleId, $p->id
            ]);
        }

        // 4. Seed Default Admin User
        $adminEmail = 'admin@inventory.com';
        $existingAdmin = DB::select("SELECT id FROM users WHERE email = ? LIMIT 1", [$adminEmail]);
        if (empty($existingAdmin)) {
            $adminUuid = (string) Str::uuid();
            DB::insert("
                INSERT INTO users (uuid, name, email, password, phone, status, created_at, updated_at)
                VALUES (?, 'System Administrator', ?, ?, '+855 12 345 678', 'ACTIVE', NOW(), NOW())
            ", [
                $adminUuid,
                $adminEmail,
                Hash::make('password')
            ]);
            $adminUserId = DB::getPdo()->lastInsertId();
            DB::insert("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)", [$adminUserId, $superAdminRoleId]);
        }

        // 5. Seed Common Units
        $units = [
            ['code' => 'PCS', 'name_en' => 'Piece', 'name_km' => 'ដុំ'],
            ['code' => 'BOX', 'name_en' => 'Box', 'name_km' => 'ប្រអប់'],
            ['code' => 'PCK', 'name_en' => 'Pack', 'name_km' => 'កញ្ចប់'],
            ['code' => 'SET', 'name_en' => 'Set', 'name_km' => 'ឈុត'],
            ['code' => 'BTL', 'name_en' => 'Bottle', 'name_km' => 'ដប'],
            ['code' => 'KG',  'name_en' => 'Kilogram', 'name_km' => 'គីឡូក្រាម'],
            ['code' => 'LTR', 'name_en' => 'Liter', 'name_km' => 'លីត្រ'],
            ['code' => 'MTR', 'name_en' => 'Meter', 'name_km' => 'ម៉ែត្រ'],
            ['code' => 'OTH', 'name_en' => 'Other', 'name_km' => 'ផ្សេងៗ'],
        ];

        foreach ($units as $u) {
            DB::insert("INSERT IGNORE INTO units (code, name_en, name_km, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())", [
                $u['code'], $u['name_en'], $u['name_km']
            ]);
        }

        // 6. Seed Categories
        $categories = [
            ['code' => 'CAT-ELEC', 'name_en' => 'Electronics & IT', 'name_km' => 'អេឡិចត្រូនិច និងបច្ចេកវិទ្យា', 'desc' => 'Computers, servers, accessories'],
            ['code' => 'CAT-OFFC', 'name_en' => 'Office Supplies', 'name_km' => 'សម្ភារៈការិយាល័យ', 'desc' => 'Paper, pens, staples, consumables'],
            ['code' => 'CAT-FURN', 'name_en' => 'Office Furniture', 'name_km' => 'គ្រឿងសង្ហារិមការិយាល័យ', 'desc' => 'Desks, chairs, filing cabinets'],
            ['code' => 'CAT-TOOL', 'name_en' => 'Tools & Maintenance', 'name_km' => 'ឧបករណ៍ និងជួសជុល', 'desc' => 'Hardware, power tools, maintenance kits'],
        ];

        foreach ($categories as $c) {
            DB::insert("
                INSERT IGNORE INTO categories (uuid, code, name_en, name_km, description, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, 'ACTIVE', NOW(), NOW())
            ", [
                (string) Str::uuid(), $c['code'], $c['name_en'], $c['name_km'], $c['desc']
            ]);
        }

        // 7. Seed Brands
        $brands = [
            ['code' => 'BRD-DELL', 'name' => 'Dell', 'desc' => 'Dell Technologies'],
            ['code' => 'BRD-HP',   'name' => 'HP', 'desc' => 'HP Inc.'],
            ['code' => 'BRD-APPL', 'name' => 'Apple', 'desc' => 'Apple Inc.'],
            ['code' => 'BRD-LOGI', 'name' => 'Logitech', 'desc' => 'Logitech International'],
            ['code' => 'BRD-SAMS', 'name' => 'Samsung', 'desc' => 'Samsung Electronics'],
            ['code' => 'BRD-CANO', 'name' => 'Canon', 'desc' => 'Canon Inc.'],
        ];

        foreach ($brands as $b) {
            DB::insert("
                INSERT IGNORE INTO brands (uuid, code, name, description, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, 'ACTIVE', NOW(), NOW())
            ", [
                (string) Str::uuid(), $b['code'], $b['name'], $b['desc']
            ]);
        }

        // 8. Seed Departments
        $depts = [
            ['code' => 'DEP-IT',   'name_en' => 'IT & Software Support', 'name_km' => 'ផ្នែកព័ត៌មានវិទ្យា', 'desc' => 'Technology operations and hardware management'],
            ['code' => 'DEP-ADM',  'name_en' => 'Administration & Operations', 'name_km' => 'ផ្នែករដ្ឋបាល', 'desc' => 'General office management'],
            ['code' => 'DEP-FIN',  'name_en' => 'Finance & Accounting', 'name_km' => 'ផ្នែកហិរញ្ញវត្ថុ និងគណនេយ្យ', 'desc' => 'Budgeting and purchasing approvals'],
            ['code' => 'DEP-LOG',  'name_en' => 'Warehouse & Logistics', 'name_km' => 'ផ្នែកឃ្លាំង និងភស្តុភារ', 'desc' => 'Storage, receiving, and dispatching'],
        ];

        foreach ($depts as $d) {
            DB::insert("
                INSERT IGNORE INTO departments (uuid, code, name_en, name_km, description, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, 'ACTIVE', NOW(), NOW())
            ", [
                (string) Str::uuid(), $d['code'], $d['name_en'], $d['name_km'], $d['desc']
            ]);
        }

        // 9. Seed Warehouses & Locations
        $warehouses = [
            ['code' => 'WH-MAIN', 'name' => 'Phnom Penh Central Warehouse', 'phone' => '+855 23 888 111', 'addr' => 'Russian Blvd, Phnom Penh'],
            ['code' => 'WH-REG1', 'name' => 'Siem Reap Regional Depot', 'phone' => '+855 63 999 222', 'addr' => 'National Road 6, Siem Reap'],
        ];

        foreach ($warehouses as $w) {
            DB::insert("
                INSERT IGNORE INTO warehouses (uuid, code, name, phone, address, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, 'ACTIVE', NOW(), NOW())
            ", [
                (string) Str::uuid(), $w['code'], $w['name'], $w['phone'], $w['addr']
            ]);
        }

        $mainWhId = DB::select("SELECT id FROM warehouses WHERE code = 'WH-MAIN' LIMIT 1")[0]->id;

        $locations = [
            ['building' => 'Building A', 'floor' => 'Floor 1', 'room' => 'Room 101', 'rack' => 'Rack A', 'shelf' => 'Shelf 01', 'bin' => 'Bin 001', 'code' => 'LOC-A1-R01-B001'],
            ['building' => 'Building A', 'floor' => 'Floor 1', 'room' => 'Room 101', 'rack' => 'Rack A', 'shelf' => 'Shelf 02', 'bin' => 'Bin 002', 'code' => 'LOC-A1-R01-B002'],
            ['building' => 'Building B', 'floor' => 'Floor 2', 'room' => 'Room 205', 'rack' => 'Rack B', 'shelf' => 'Shelf 01', 'bin' => 'Bin 001', 'code' => 'LOC-B2-R01-B001'],
        ];

        foreach ($locations as $l) {
            DB::insert("
                INSERT IGNORE INTO warehouse_locations (warehouse_id, building, floor, room, rack, shelf, bin, code, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'ACTIVE', NOW(), NOW())
            ", [
                $mainWhId, $l['building'], $l['floor'], $l['room'], $l['rack'], $l['shelf'], $l['bin'], $l['code']
            ]);
        }

        // 10. Seed Suppliers
        $suppliers = [
            ['code' => 'SUP-DELL', 'name' => 'Dell Official Distributor Cambodia', 'contact' => 'Sokha Chan', 'phone' => '+855 12 888 777', 'email' => 'sales@dell-cambodia.com', 'tax' => 'K001-901234567'],
            ['code' => 'SUP-OFFC', 'name' => 'Phnom Penh Stationery & Supplies', 'contact' => 'Vannak Heng', 'phone' => '+855 16 555 444', 'email' => 'contact@pp-stationery.com', 'tax' => 'K002-801234568'],
        ];

        foreach ($suppliers as $s) {
            DB::insert("
                INSERT IGNORE INTO suppliers (uuid, code, name, contact_person, phone, email, tax_number, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'ACTIVE', NOW(), NOW())
            ", [
                (string) Str::uuid(), $s['code'], $s['name'], $s['contact'], $s['phone'], $s['email'], $s['tax']
            ]);
        }

        // 11. Seed Items & Initial Stock
        $catElecId = DB::select("SELECT id FROM categories WHERE code = 'CAT-ELEC' LIMIT 1")[0]->id;
        $catOffcId = DB::select("SELECT id FROM categories WHERE code = 'CAT-OFFC' LIMIT 1")[0]->id;
        $brdDellId = DB::select("SELECT id FROM brands WHERE code = 'BRD-DELL' LIMIT 1")[0]->id;
        $unitPcsId = DB::select("SELECT id FROM units WHERE code = 'PCS' LIMIT 1")[0]->id;
        $unitBoxId = DB::select("SELECT id FROM units WHERE code = 'BOX' LIMIT 1")[0]->id;

        $items = [
            [
                'code' => 'ITM-000001', 'barcode' => '8934567890123', 'name_en' => 'Dell Latitude 5420 Laptop i7 16GB 512GB', 'name_km' => 'កុំព្យូទ័រយួរដៃ Dell Latitude 5420',
                'category_id' => $catElecId, 'brand_id' => $brdDellId, 'unit_id' => $unitPcsId, 'item_type' => 'ASSET',
                'min_stock' => 5, 'max_stock' => 50, 'reorder_level' => 10, 'default_unit_cost' => 850.00, 'initial_qty' => 25
            ],
            [
                'code' => 'ITM-000002', 'barcode' => '8934567890124', 'name_en' => 'Double A A4 Copy Paper 80gsm (Box of 5 Reams)', 'name_km' => 'ក្រដាស A4 Double A 80gsm (ប្រអប់ ៥រាម)',
                'category_id' => $catOffcId, 'brand_id' => null, 'unit_id' => $unitBoxId, 'item_type' => 'CONSUMABLE',
                'min_stock' => 20, 'max_stock' => 200, 'reorder_level' => 30, 'default_unit_cost' => 22.50, 'initial_qty' => 85
            ],
            [
                'code' => 'ITM-000003', 'barcode' => '8934567890125', 'name_en' => 'Logitech Wireless Mouse M185', 'name_km' => 'កណ្ដុរឥតខ្សែ Logitech M185',
                'category_id' => $catElecId, 'brand_id' => null, 'unit_id' => $unitPcsId, 'item_type' => 'NON_CONSUMABLE',
                'min_stock' => 10, 'max_stock' => 100, 'reorder_level' => 15, 'default_unit_cost' => 12.00, 'initial_qty' => 40
            ],
        ];

        foreach ($items as $it) {
            DB::insert("
                INSERT IGNORE INTO items (
                    uuid, code, barcode, qr_code, name_en, name_km, category_id,
                    brand_id, unit_id, item_type, min_stock, max_stock, reorder_level,
                    default_unit_cost, status, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'ACTIVE', NOW(), NOW())
            ", [
                (string) Str::uuid(), $it['code'], $it['barcode'], $it['code'],
                $it['name_en'], $it['name_km'], $it['category_id'],
                $it['brand_id'], $it['unit_id'], $it['item_type'],
                $it['min_stock'], $it['max_stock'], $it['reorder_level'],
                $it['default_unit_cost']
            ]);

            $itemId = DB::select("SELECT id FROM items WHERE code = ? LIMIT 1", [$it['code']])[0]->id;
            $qty = $it['initial_qty'];
            $cost = $it['default_unit_cost'];

            // Initial Inventory
            DB::insert("
                INSERT IGNORE INTO inventories (
                    item_id, warehouse_id, location_id, quantity, reserved_quantity,
                    unit_cost, total_value, last_movement_date, created_at, updated_at
                ) VALUES (?, ?, NULL, ?, 0, ?, ?, NOW(), NOW(), NOW())
            ", [
                $itemId, $mainWhId, $qty, $cost, $qty * $cost
            ]);

            // Initial Inventory Transaction
            DB::insert("
                INSERT INTO inventory_transactions (
                    item_id, warehouse_id, location_id, transaction_type,
                    quantity_in, quantity_out, balance_after, unit_cost, total_cost,
                    reference_type, reference_id, user_id, date, note, created_at
                ) VALUES (?, ?, NULL, 'STOCK_IN', ?, 0, ?, ?, ?, 'INITIAL_SEED', NULL, 1, CURRENT_DATE, 'Initial stock balance seed', NOW())
            ", [
                $itemId, $mainWhId, $qty, $qty, $cost, $qty * $cost
            ]);
        }
    }
}
