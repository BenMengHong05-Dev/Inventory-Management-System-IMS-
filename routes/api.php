<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// Public Scanner / Barcode Lookup Endpoint
Route::get('/lookup/{code}', function ($code) {
    // Check Items by barcode or SKU
    $item = DB::selectOne("SELECT i.*, c.name as category_name, u.name as unit_name, 
                           COALESCE(SUM(inv.available_quantity), 0) as total_available
                           FROM items i 
                           LEFT JOIN categories c ON i.category_id = c.id
                           LEFT JOIN units u ON i.unit_id = u.id
                           LEFT JOIN inventory inv ON i.id = inv.item_id
                           WHERE (i.barcode = ? OR i.sku = ?) AND i.deleted_at IS NULL
                           GROUP BY i.id", [$code, $code]);

    if ($item) {
        return response()->json([
            'found' => true,
            'type' => 'item',
            'data' => $item
        ]);
    }

    // Check Assets by asset_tag or serial_number
    $asset = DB::selectOne("SELECT a.*, c.name as category_name, d.name as department_name, 
                            e.name as custodian_name, l.name as location_name
                            FROM assets a 
                            LEFT JOIN categories c ON a.category_id = c.id
                            LEFT JOIN departments d ON a.department_id = d.id
                            LEFT JOIN employees e ON a.custodian_id = e.id
                            LEFT JOIN locations l ON a.location_id = l.id
                            WHERE (a.asset_tag = ? OR a.serial_number = ?) AND a.deleted_at IS NULL", [$code, $code]);

    if ($asset) {
        return response()->json([
            'found' => true,
            'type' => 'asset',
            'data' => $asset
        ]);
    }

    return response()->json([
        'found' => false,
        'message' => 'No item or asset matching code ' . $code
    ], 404);
});

// Items API
Route::get('/items', function (Request $request) {
    $search = $request->query('q');
    $query = "SELECT i.id, i.sku, i.barcode, i.name, i.cost_price, i.selling_price, i.status, c.name as category_name 
              FROM items i 
              LEFT JOIN categories c ON i.category_id = c.id 
              WHERE i.deleted_at IS NULL";
    $bindings = [];

    if ($search) {
        $query .= " AND (i.name LIKE ? OR i.sku LIKE ? OR i.barcode LIKE ?)";
        $term = "%{$search}%";
        $bindings = [$term, $term, $term];
    }

    $query .= " ORDER BY i.name ASC LIMIT 50";
    $items = DB::select($query, $bindings);

    return response()->json([
        'success' => true,
        'data' => $items
    ]);
});

// Inventory Stock Levels API
Route::get('/inventory', function (Request $request) {
    $warehouseId = $request->query('warehouse_id');
    $query = "SELECT inv.*, i.sku, i.name as item_name, w.name as warehouse_name 
              FROM inventory inv 
              JOIN items i ON inv.item_id = i.id 
              JOIN warehouses w ON inv.warehouse_id = w.id 
              WHERE i.deleted_at IS NULL";
    $bindings = [];

    if ($warehouseId) {
        $query .= " AND inv.warehouse_id = ?";
        $bindings[] = $warehouseId;
    }

    $query .= " ORDER BY w.name, i.name LIMIT 100";
    $stock = DB::select($query, $bindings);

    return response()->json([
        'success' => true,
        'data' => $stock
    ]);
});

// Assets API
Route::get('/assets', function (Request $request) {
    $status = $request->query('status');
    $query = "SELECT a.id, a.asset_tag, a.name, a.serial_number, a.status, a.current_value, 
                     c.name as category_name, e.name as custodian_name 
              FROM assets a 
              LEFT JOIN categories c ON a.category_id = c.id 
              LEFT JOIN employees e ON a.custodian_id = e.id 
              WHERE a.deleted_at IS NULL";
    $bindings = [];

    if ($status) {
        $query .= " AND a.status = ?";
        $bindings[] = $status;
    }

    $query .= " ORDER BY a.asset_tag ASC LIMIT 100";
    $assets = DB::select($query, $bindings);

    return response()->json([
        'success' => true,
        'data' => $assets
    ]);
});
