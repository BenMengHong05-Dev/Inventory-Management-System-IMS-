<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * View real-time inventory quantities by item and warehouse/location.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $warehouseId = $request->get('warehouse_id');
        $categoryId = $request->get('category_id');
        $stockStatus = $request->get('stock_status'); // ALL, LOW, OUT, NORMAL

        $sql = "
            SELECT inv.*,
                   i.code as item_code, i.name_en as item_name, i.name_km as item_name_km,
                   i.min_stock, i.max_stock, i.reorder_level, i.item_type,
                   w.name as warehouse_name,
                   wl.code as location_code,
                   u.name_en as unit_name, u.name_km as unit_name_km,
                   c.name_en as category_name, c.name_km as category_name_km,
                   (inv.quantity - inv.reserved_quantity) as available_quantity
            FROM inventories inv
            JOIN items i ON inv.item_id = i.id
            JOIN warehouses w ON inv.warehouse_id = w.id
            LEFT JOIN warehouse_locations wl ON inv.location_id = wl.id
            LEFT JOIN units u ON i.unit_id = u.id
            LEFT JOIN categories c ON i.category_id = c.id
            WHERE i.deleted_at IS NULL
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (i.code LIKE ? OR i.name_en LIKE ? OR i.name_km LIKE ? OR i.barcode LIKE ?)";
            $term = "%{$search}%";
            $params = array_merge($params, [$term, $term, $term, $term]);
        }

        if (!empty($warehouseId)) {
            $sql .= " AND inv.warehouse_id = ?";
            $params[] = $warehouseId;
        }

        if (!empty($categoryId)) {
            $sql .= " AND i.category_id = ?";
            $params[] = $categoryId;
        }

        if ($stockStatus === 'OUT') {
            $sql .= " AND inv.quantity <= 0";
        } elseif ($stockStatus === 'LOW') {
            $sql .= " AND inv.quantity <= i.reorder_level AND inv.quantity > 0";
        } elseif ($stockStatus === 'NORMAL') {
            $sql .= " AND inv.quantity > i.reorder_level";
        }

        $sql .= " ORDER BY inv.id DESC";

        $inventories = raw_paginate($sql, $params, 15);

        $warehouses = DB::select("SELECT id, name FROM warehouses WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name ASC");
        $categories = DB::select("SELECT id, name_en, name_km FROM categories WHERE parent_id IS NULL AND status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name_en ASC");

        return view('inventory.index', compact('inventories', 'warehouses', 'categories', 'search', 'warehouseId', 'categoryId', 'stockStatus'));
    }

    /**
     * Low stock alerts and reorder suggestions (Module 28).
     */
    public function lowStock(Request $request)
    {
        $sql = "
            SELECT i.id, i.code, i.name_en, i.name_km, i.min_stock, i.reorder_level, i.default_unit_cost,
                   COALESCE(SUM(inv.quantity), 0) as current_stock,
                   COALESCE(SUM(inv.reserved_quantity), 0) as reserved_stock,
                   u.name_en as unit_name,
                   c.name_en as category_name,
                   -- Suggested reorder: (min_stock * 2) - current_stock
                   CASE
                       WHEN COALESCE(SUM(inv.quantity), 0) <= i.reorder_level THEN (i.reorder_level * 2) - COALESCE(SUM(inv.quantity), 0)
                       ELSE 0
                   END as suggested_order_qty
            FROM items i
            LEFT JOIN inventories inv ON i.id = inv.item_id
            LEFT JOIN units u ON i.unit_id = u.id
            LEFT JOIN categories c ON i.category_id = c.id
            WHERE i.deleted_at IS NULL
            GROUP BY i.id, i.code, i.name_en, i.name_km, i.min_stock, i.reorder_level, i.default_unit_cost, u.name_en, c.name_en
            HAVING current_stock <= i.reorder_level
            ORDER BY current_stock ASC
        ";

        $lowStockItems = DB::select($sql);

        return view('inventory.low_stock', compact('lowStockItems'));
    }

    /**
     * Centralized Inventory Transaction History (Module 17).
     * Never delete transaction history.
     */
    public function history(Request $request)
    {
        $itemId = $request->get('item_id');
        $warehouseId = $request->get('warehouse_id');
        $type = $request->get('type');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $sql = "
            SELECT it.*,
                   i.code as item_code, i.name_en as item_name,
                   w.name as warehouse_name,
                   wl.code as location_code,
                   u.name as user_name
            FROM inventory_transactions it
            JOIN items i ON it.item_id = i.id
            JOIN warehouses w ON it.warehouse_id = w.id
            LEFT JOIN warehouse_locations wl ON it.location_id = wl.id
            LEFT JOIN users u ON it.user_id = u.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($itemId)) {
            $sql .= " AND it.item_id = ?";
            $params[] = $itemId;
        }

        if (!empty($warehouseId)) {
            $sql .= " AND it.warehouse_id = ?";
            $params[] = $warehouseId;
        }

        if (!empty($type)) {
            $sql .= " AND it.transaction_type = ?";
            $params[] = $type;
        }

        if (!empty($startDate)) {
            $sql .= " AND it.date >= ?";
            $params[] = $startDate;
        }

        if (!empty($endDate)) {
            $sql .= " AND it.date <= ?";
            $params[] = $endDate;
        }

        $sql .= " ORDER BY it.id DESC";

        $transactions = raw_paginate($sql, $params, 20);

        $warehouses = DB::select("SELECT id, name FROM warehouses WHERE deleted_at IS NULL ORDER BY name ASC");
        $items = DB::select("SELECT id, code, name_en FROM items WHERE deleted_at IS NULL ORDER BY name_en ASC");

        return view('inventory.history', compact('transactions', 'warehouses', 'items', 'itemId', 'warehouseId', 'type', 'startDate', 'endDate'));
    }
}
