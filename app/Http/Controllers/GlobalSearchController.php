<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $q = trim($request->get('q'));
        if (empty($q)) {
            return view('search.results', [
                'q' => '',
                'items' => [],
                'assets' => [],
                'suppliers' => [],
                'employees' => [],
                'warehouses' => [],
                'orders' => [],
            ]);
        }

        $term = "%{$q}%";

        // 1. Items
        $items = DB::select("
            SELECT id, code, barcode, name_en, name_km, item_type
            FROM items
            WHERE (code LIKE ? OR barcode LIKE ? OR name_en LIKE ? OR name_km LIKE ?) AND deleted_at IS NULL
            LIMIT 10
        ", [$term, $term, $term, $term]);

        // 2. Assets
        $assets = DB::select("
            SELECT id, asset_code, asset_name, serial_number, barcode, status
            FROM assets
            WHERE (asset_code LIKE ? OR asset_name LIKE ? OR serial_number LIKE ? OR barcode LIKE ?) AND deleted_at IS NULL
            LIMIT 10
        ", [$term, $term, $term, $term]);

        // 3. Suppliers
        $suppliers = DB::select("
            SELECT id, code, name, contact_person, phone
            FROM suppliers
            WHERE (code LIKE ? OR name LIKE ? OR contact_person LIKE ? OR phone LIKE ?) AND deleted_at IS NULL
            LIMIT 10
        ", [$term, $term, $term, $term]);

        // 4. Employees
        $employees = DB::select("
            SELECT id, code, name_en, name_km, phone, email
            FROM employees
            WHERE (code LIKE ? OR name_en LIKE ? OR name_km LIKE ? OR phone LIKE ? OR email LIKE ?) AND deleted_at IS NULL
            LIMIT 10
        ", [$term, $term, $term, $term, $term]);

        // 5. Warehouses
        $warehouses = DB::select("
            SELECT id, code, name, phone
            FROM warehouses
            WHERE (code LIKE ? OR name LIKE ? OR phone LIKE ?) AND deleted_at IS NULL
            LIMIT 10
        ", [$term, $term, $term]);

        // 6. Orders / Requisitions
        $orders = DB::select("
            SELECT 'PO' as type, id, po_number as reference_number, order_date as date, status
            FROM purchase_orders
            WHERE po_number LIKE ?
            UNION ALL
            SELECT 'PR' as type, id, request_number as reference_number, request_date as date, status
            FROM purchase_requests
            WHERE request_number LIKE ?
            UNION ALL
            SELECT 'STOCK_IN' as type, id, stock_in_number as reference_number, date, status
            FROM stock_ins
            WHERE stock_in_number LIKE ?
            UNION ALL
            SELECT 'STOCK_OUT' as type, id, stock_out_number as reference_number, date, status
            FROM stock_outs
            WHERE stock_out_number LIKE ?
            LIMIT 10
        ", [$term, $term, $term, $term]);

        return view('search.results', compact('q', 'items', 'assets', 'suppliers', 'employees', 'warehouses', 'orders'));
    }
}
