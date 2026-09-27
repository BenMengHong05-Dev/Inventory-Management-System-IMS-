<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Reports Hub / Financial Summary Overview.
     */
    public function index()
    {
        // Financial Summaries via raw SQL
        $inventoryValue = DB::select("
            SELECT COALESCE(SUM(total_value), 0) as total_inventory_cost,
                   COUNT(DISTINCT item_id) as total_items_count
            FROM inventories
        ")[0];

        $assetValue = DB::select("
            SELECT COALESCE(SUM(purchase_cost), 0) as total_cost,
                   COUNT(*) as asset_count
            FROM assets
            WHERE status != 'DISPOSED' AND deleted_at IS NULL
        ")[0];

        $poTotal = DB::select("
            SELECT COALESCE(SUM(total), 0) as total_spend,
                   COUNT(*) as order_count
            FROM purchase_orders
            WHERE status != 'CANCELLED'
        ")[0];

        $stockInVal = DB::select("
            SELECT COALESCE(SUM(sii.total_cost), 0) as total_in_value
            FROM stock_ins si
            JOIN stock_in_items sii ON si.id = sii.stock_in_id
            WHERE si.status = 'APPROVED'
        ")[0];

        $stockOutVal = DB::select("
            SELECT COALESCE(SUM(soi.total_value), 0) as total_out_value
            FROM stock_outs so
            JOIN stock_out_items soi ON so.id = soi.stock_out_id
            WHERE so.status = 'APPROVED'
        ")[0];

        $totalInventoryValue = $inventoryValue->total_inventory_cost;
        $totalAssetValue = $assetValue->total_cost;
        $totalPurchaseAmount = $poTotal->total_spend;
        $totalMaintenanceCost = DB::select("SELECT COALESCE(SUM(cost), 0) as val FROM maintenances WHERE status = 'COMPLETED'")[0]->val;
        $totalDisposalValue = DB::select("SELECT COALESCE(SUM(disposal_value), 0) as val FROM asset_disposals")[0]->val;

        // Inventory by Warehouse
        $invByWarehouse = DB::select("
            SELECT w.name as warehouse_name,
                   COALESCE(SUM(inv.quantity), 0) as total_qty,
                   COALESCE(SUM(inv.total_value), 0) as total_val
            FROM warehouses w
            LEFT JOIN inventories inv ON w.id = inv.warehouse_id
            WHERE w.deleted_at IS NULL
            GROUP BY w.id, w.name
        ");

        // Inventory by Category
        $invByCategory = DB::select("
            SELECT c.name_en as category_name, c.name_km as category_name_km,
                   COALESCE(SUM(inv.quantity), 0) as total_qty,
                   COALESCE(SUM(inv.total_value), 0) as total_val
            FROM categories c
            LEFT JOIN items i ON c.id = i.category_id AND i.deleted_at IS NULL
            LEFT JOIN inventories inv ON i.id = inv.item_id
            WHERE c.deleted_at IS NULL
            GROUP BY c.id, c.name_en, c.name_km
        ");

        return view('reports.index', compact(
            'inventoryValue',
            'assetValue',
            'poTotal',
            'stockInVal',
            'stockOutVal',
            'totalInventoryValue',
            'totalAssetValue',
            'totalPurchaseAmount',
            'totalMaintenanceCost',
            'totalDisposalValue',
            'invByWarehouse',
            'invByCategory'
        ));
    }

    public function inventory(Request $request)
    {
        return $this->inventoryReport($request);
    }

    public function exportInventory(Request $request)
    {
        $request->merge(['export' => 'csv']);
        return $this->inventoryReport($request);
    }

    public function assets(Request $request)
    {
        return $this->assetReport($request);
    }

    public function exportAssets(Request $request)
    {
        $request->merge(['export' => 'csv']);
        return $this->assetReport($request);
    }

    public function purchasing(Request $request)
    {
        return $this->purchaseReport($request);
    }

    public function exportPurchasing(Request $request)
    {
        $request->merge(['export' => 'csv']);
        return $this->purchaseReport($request);
    }

    /**
     * Inventory Valuation & Stock Reports.
     */
    public function inventoryReport(Request $request)
    {
        $warehouseId = $request->get('warehouse_id');
        $categoryId = $request->get('category_id');
        $status = $request->get('status');
        $export = $request->get('export');

        $sql = "
            SELECT inv.*, i.code as item_code, i.code as sku,
                   i.name_en as item_name, i.name_km as item_name_km,
                   i.reorder_level, i.min_stock, i.item_type,
                   w.name as warehouse_name, wl.code as location_code,
                   u.name_en as unit_name, u.name_km as unit_name_km,
                   c.name_en as category_name, c.name_km as category_name_km,
                   (inv.quantity - inv.reserved_quantity) as available_qty,
                   (inv.quantity - inv.reserved_quantity) as available_quantity,
                   inv.unit_cost as cost_price
            FROM inventories inv
            JOIN items i ON inv.item_id = i.id
            JOIN warehouses w ON inv.warehouse_id = w.id
            LEFT JOIN warehouse_locations wl ON inv.location_id = wl.id
            LEFT JOIN units u ON i.unit_id = u.id
            LEFT JOIN categories c ON i.category_id = c.id
            WHERE i.deleted_at IS NULL
        ";
        $params = [];

        if (!empty($warehouseId)) {
            $sql .= " AND inv.warehouse_id = ?";
            $params[] = $warehouseId;
        }

        if (!empty($categoryId)) {
            $sql .= " AND i.category_id = ?";
            $params[] = $categoryId;
        }

        if ($status === 'LOW') {
            $sql .= " AND inv.quantity <= i.reorder_level AND inv.quantity > 0";
        } elseif ($status === 'OUT') {
            $sql .= " AND inv.quantity <= 0";
        }

        $sql .= " ORDER BY w.name ASC, i.name_en ASC";

        if ($export === 'csv') {
            return $this->exportCsv("inventory_report_" . date('Ymd_His') . ".csv", [
                'Item Code', 'Item Name (EN)', 'Category', 'Warehouse', 'Location', 'Unit', 'Qty', 'Reserved', 'Available', 'Unit Cost', 'Total Value'
            ], DB::select($sql, $params), function ($row) {
                return [
                    $row->item_code,
                    $row->item_name,
                    $row->category_name ?? '-',
                    $row->warehouse_name,
                    $row->location_code ?? '-',
                    $row->unit_name ?? '-',
                    $row->quantity,
                    $row->reserved_quantity,
                    $row->available_qty,
                    $row->unit_cost,
                    $row->total_value,
                ];
            });
        }

        $data = raw_paginate($sql, $params, 25);
        $report = $data;
        $warehouses = DB::select("SELECT id, name FROM warehouses WHERE deleted_at IS NULL ORDER BY name ASC");
        $categories = DB::select("SELECT id, name_en, name_km, name_en as name FROM categories WHERE parent_id IS NULL AND deleted_at IS NULL ORDER BY name_en ASC");

        return view('reports.inventory', compact('data', 'report', 'warehouses', 'categories', 'warehouseId', 'categoryId', 'status'));
    }

    /**
     * Asset Reports.
     */
    public function assetReport(Request $request)
    {
        $categoryId = $request->get('category_id');
        $departmentId = $request->get('department_id');
        $status = $request->get('status');
        $condition = $request->get('condition');
        $export = $request->get('export');

        $sql = "
            SELECT a.*, a.asset_code as asset_tag, a.purchase_cost as current_value,
                   c.name_en as category_name, c.name_km as category_name_km,
                   b.name as brand_name,
                   d.name_en as department_name, d.name_km as department_name_km,
                   e.name_en as employee_name, e.name_km as employee_name_km,
                   e.name_en as custodian_name, e.name_km as custodian_name_km,
                   w.name as warehouse_name
            FROM assets a
            LEFT JOIN categories c ON a.category_id = c.id
            LEFT JOIN brands b ON a.brand_id = b.id
            LEFT JOIN departments d ON a.current_department_id = d.id
            LEFT JOIN employees e ON a.current_employee_id = e.id
            LEFT JOIN warehouses w ON a.warehouse_id = w.id
            WHERE a.deleted_at IS NULL
        ";
        $params = [];

        if (!empty($categoryId)) {
            $sql .= " AND a.category_id = ?";
            $params[] = $categoryId;
        }

        if (!empty($departmentId)) {
            $sql .= " AND a.current_department_id = ?";
            $params[] = $departmentId;
        }

        if (!empty($status) && $status !== 'all') {
            $sql .= " AND a.status = ?";
            $params[] = strtoupper($status);
        }

        if (!empty($condition)) {
            $sql .= " AND a.condition = ?";
            $params[] = $condition;
        }

        $sql .= " ORDER BY a.id DESC";

        if ($export === 'csv') {
            return $this->exportCsv("asset_report_" . date('Ymd_His') . ".csv", [
                'Asset Code', 'Asset Name', 'Category', 'Brand', 'Model', 'Serial #', 'Condition', 'Status', 'Department', 'Employee', 'Cost', 'Warranty Expiry'
            ], DB::select($sql, $params), function ($row) {
                return [
                    $row->asset_code,
                    $row->asset_name,
                    $row->category_name ?? '-',
                    $row->brand_name ?? '-',
                    $row->model ?? '-',
                    $row->serial_number ?? '-',
                    $row->condition,
                    $row->status,
                    $row->department_name ?? '-',
                    $row->employee_name ?? '-',
                    $row->purchase_cost,
                    $row->warranty_expiry ?? '-',
                ];
            });
        }

        $data = raw_paginate($sql, $params, 25);
        $report = $data;
        $categories = DB::select("SELECT id, name_en, name_km, name_en as name FROM categories WHERE deleted_at IS NULL ORDER BY name_en ASC");
        $departments = DB::select("SELECT id, name_en, name_km, name_en as name FROM departments WHERE deleted_at IS NULL ORDER BY name_en ASC");

        return view('reports.assets', compact('data', 'report', 'categories', 'departments', 'categoryId', 'departmentId', 'status', 'condition'));
    }

    /**
     * Purchasing Reports.
     */
    public function purchaseReport(Request $request)
    {
        $supplierId = $request->get('supplier_id');
        $status = $request->get('status');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $export = $request->get('export');

        $sql = "
            SELECT po.*, po.total as total_amount, s.name as supplier_name, w.name as warehouse_name,
                   u.name as creator_name,
                   (SELECT COUNT(*) FROM purchase_order_items poi WHERE poi.purchase_order_id = po.id) as item_count
            FROM purchase_orders po
            JOIN suppliers s ON po.supplier_id = s.id
            JOIN warehouses w ON po.warehouse_id = w.id
            LEFT JOIN users u ON po.created_by = u.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($supplierId)) {
            $sql .= " AND po.supplier_id = ?";
            $params[] = $supplierId;
        }

        if (!empty($status) && $status !== 'all') {
            $sql .= " AND po.status = ?";
            $params[] = strtoupper($status);
        }

        if (!empty($startDate)) {
            $sql .= " AND po.order_date >= ?";
            $params[] = $startDate;
        }

        if (!empty($endDate)) {
            $sql .= " AND po.order_date <= ?";
            $params[] = $endDate;
        }

        $sql .= " ORDER BY po.order_date DESC";

        if ($export === 'csv') {
            return $this->exportCsv("purchasing_report_" . date('Ymd_His') . ".csv", [
                'PO Number', 'Supplier', 'Warehouse', 'Order Date', 'Delivery Date', 'Subtotal', 'Tax', 'Discount', 'Total', 'Status'
            ], DB::select($sql, $params), function ($row) {
                return [
                    $row->po_number,
                    $row->supplier_name,
                    $row->warehouse_name,
                    $row->order_date,
                    $row->expected_delivery_date ?? '-',
                    $row->subtotal,
                    $row->tax_amount,
                    $row->discount_amount,
                    $row->total,
                    $row->status,
                ];
            });
        }

        $data = raw_paginate($sql, $params, 25);
        $report = $data;
        $suppliers = DB::select("SELECT id, name FROM suppliers WHERE deleted_at IS NULL ORDER BY name ASC");

        return view('reports.purchasing', compact('data', 'report', 'suppliers', 'supplierId', 'status', 'startDate', 'endDate'));
    }

    /**
     * Helper to stream CSV downloads cleanly.
     */
    private function exportCsv(string $filename, array $headers, array $rows, callable $transformer): StreamedResponse
    {
        return new StreamedResponse(function () use ($headers, $rows, $transformer) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $headers);

            foreach ($rows as $row) {
                fputcsv($handle, $transformer($row));
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
