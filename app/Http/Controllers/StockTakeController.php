<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockTakeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $warehouseId = $request->get('warehouse_id');
        $status = $request->get('status');

        $sql = "
            SELECT st.*, w.name as warehouse_name, wl.code as location_code,
                   u.name as creator_name, ap.name as approver_name,
                   (SELECT COUNT(*) FROM stock_take_items sti WHERE sti.stock_take_id = st.id) as total_items,
                   (SELECT COALESCE(SUM(difference_value), 0) FROM stock_take_items sti WHERE sti.stock_take_id = st.id) as total_variance_value
            FROM stock_takes st
            JOIN warehouses w ON st.warehouse_id = w.id
            LEFT JOIN warehouse_locations wl ON st.location_id = wl.id
            JOIN users u ON st.created_by = u.id
            LEFT JOIN users ap ON st.approved_by = ap.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND st.stock_take_number LIKE ?";
            $params[] = "%{$search}%";
        }

        if (!empty($warehouseId)) {
            $sql .= " AND st.warehouse_id = ?";
            $params[] = $warehouseId;
        }

        if (!empty($status)) {
            $sql .= " AND st.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY st.id DESC";

        $stockTakes = raw_paginate($sql, $params, 15);
        $warehouses = DB::select("SELECT id, name FROM warehouses WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name ASC");

        return view('stock_takes.index', compact('stockTakes', 'warehouses', 'search', 'warehouseId', 'status'));
    }

    public function create()
    {
        $nextNumber = generate_code('STK', 'stock_takes', 'stock_take_number');
        $warehouses = DB::select("SELECT id, name FROM warehouses WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name ASC");
        $locations = DB::select("SELECT id, warehouse_id, code FROM warehouse_locations WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY code ASC");

        return view('stock_takes.create', compact('nextNumber', 'warehouses', 'locations'));
    }

    /**
     * Start stock take: freeze / snapshot expected quantities from inventories table.
     */
    public function store(Request $request)
    {
        $request->validate([
            'stock_take_number' => 'required|string|max:50|unique:stock_takes,stock_take_number',
            'warehouse_id' => 'required|integer',
            'location_id' => 'nullable|integer',
            'date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        $userId = session('user_id');

        DB::transaction(function () use ($request, $userId) {
            $uuid = (string) Str::uuid();

            // 1. Create Stock Take Header
            DB::insert("
                INSERT INTO stock_takes (
                    uuid, stock_take_number, warehouse_id, location_id, date,
                    status, created_by, note, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, 'IN_PROGRESS', ?, ?, NOW(), NOW())
            ", [
                $uuid,
                strtoupper($request->stock_take_number),
                $request->warehouse_id,
                $request->location_id ?: null,
                $request->date,
                $userId,
                $request->note
            ]);

            $stockTakeId = DB::getPdo()->lastInsertId();

            // 2. Snapshot current expected quantities from inventories table
            $invSql = "
                SELECT inv.item_id, inv.quantity, inv.unit_cost
                FROM inventories inv
                WHERE inv.warehouse_id = ?
            ";
            $invParams = [$request->warehouse_id];

            if ($request->location_id) {
                $invSql .= " AND inv.location_id = ?";
                $invParams[] = $request->location_id;
            }

            $currentStock = DB::select($invSql, $invParams);

            foreach ($currentStock as $row) {
                DB::insert("
                    INSERT INTO stock_take_items (
                        stock_take_id, item_id, system_quantity, counted_quantity,
                        difference, unit_cost, difference_value, created_at, updated_at
                    ) VALUES (?, ?, ?, NULL, 0, ?, 0, NOW(), NOW())
                ", [
                    $stockTakeId,
                    $row->item_id,
                    $row->quantity,
                    $row->unit_cost
                ]);
            }

            audit_log('CREATE', 'StockTake', 'StockTake', $stockTakeId, null, [
                'number' => $request->stock_take_number,
                'items_snapshotted' => count($currentStock)
            ]);
        });

        return redirect()->route('stock-takes.index')->with('success', 'Stock Take initiated and inventory snapshot created. Ready for counting.');
    }

    public function show($id)
    {
        $stockTakes = DB::select("
            SELECT st.*, w.name as warehouse_name, wl.code as location_code,
                   u.name as creator_name, ap.name as approver_name
            FROM stock_takes st
            JOIN warehouses w ON st.warehouse_id = w.id
            LEFT JOIN warehouse_locations wl ON st.location_id = wl.id
            JOIN users u ON st.created_by = u.id
            LEFT JOIN users ap ON st.approved_by = ap.id
            WHERE st.id = ?
            LIMIT 1
        ", [$id]);

        if (empty($stockTakes)) {
            abort(404);
        }

        $stockTake = $stockTakes[0];

        $items = DB::select("
            SELECT sti.*, i.code as item_code, i.name_en as item_name, u.name_en as unit_name
            FROM stock_take_items sti
            JOIN items i ON sti.item_id = i.id
            LEFT JOIN units u ON i.unit_id = u.id
            WHERE sti.stock_take_id = ?
            ORDER BY i.name_en ASC
        ", [$id]);

        return view('stock_takes.show', compact('stockTake', 'items'));
    }

    /**
     * Submit physical counts and calculate variances.
     */
    public function updateCounts(Request $request, $id)
    {
        $request->validate([
            'counts' => 'required|array',
            'counts.*.item_id' => 'required|integer',
            'counts.*.counted_quantity' => 'required|numeric|min:0',
            'counts.*.reason' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $id) {
            foreach ($request->counts as $countData) {
                $itemId = $countData['item_id'];
                $countedQty = (float) $countData['counted_quantity'];
                $reason = $countData['reason'] ?? null;

                $existing = DB::select("
                    SELECT system_quantity, unit_cost FROM stock_take_items
                    WHERE stock_take_id = ? AND item_id = ?
                ", [$id, $itemId]);

                if (!empty($existing)) {
                    $systemQty = (float) $existing[0]->system_quantity;
                    $unitCost = (float) $existing[0]->unit_cost;
                    $diff = $countedQty - $systemQty;
                    $diffValue = $diff * $unitCost;

                    DB::update("
                        UPDATE stock_take_items
                        SET counted_quantity = ?, difference = ?, difference_value = ?, reason = ?, updated_at = NOW()
                        WHERE stock_take_id = ? AND item_id = ?
                    ", [$countedQty, $diff, $diffValue, $reason, $id, $itemId]);
                }
            }

            DB::update("
                UPDATE stock_takes
                SET status = 'REVIEWED', updated_at = NOW()
                WHERE id = ?
            ", [$id]);

            audit_log('UPDATE_COUNTS', 'StockTake', 'StockTake', $id);
        });

        return back()->with('success', 'Physical counts recorded and variances calculated. Awaiting supervisor approval.');
    }

    /**
     * Approve stock take and apply reconciliation adjustments to inventories.
     */
    public function approve(Request $request, $id)
    {
        $userId = session('user_id');

        $stockTakes = DB::select("SELECT * FROM stock_takes WHERE id = ? LIMIT 1", [$id]);
        if (empty($stockTakes) || !in_array($stockTakes[0]->status, ['IN_PROGRESS', 'REVIEWED'])) {
            return back()->with('error', 'Stock take is not in an approvable state.');
        }

        $stockTake = $stockTakes[0];

        // Separation of duties rule: Cannot approve own request if configured
        if ($stockTake->created_by == $userId && !has_permission('super-admin')) {
            return back()->with('error', 'Separation of duties: You cannot approve your own stock take.');
        }

        $items = DB::select("SELECT * FROM stock_take_items WHERE stock_take_id = ?", [$id]);

        DB::transaction(function () use ($stockTake, $items, $userId, $id) {
            foreach ($items as $item) {
                if ($item->counted_quantity === null) {
                    continue;
                }

                $diff = (float) $item->difference;
                if ($diff != 0) {
                    // Update inventory quantity
                    DB::update("
                        UPDATE inventories
                        SET quantity = ?, total_value = ? * unit_cost, last_movement_date = NOW(), updated_at = NOW()
                        WHERE item_id = ? AND warehouse_id = ?
                    ", [$item->counted_quantity, $item->counted_quantity, $item->item_id, $stockTake->warehouse_id]);

                    // Insert transaction record
                    $txType = $diff > 0 ? 'STOCK_TAKE_IN' : 'STOCK_TAKE_OUT';
                    $qtyIn = $diff > 0 ? $diff : 0;
                    $qtyOut = $diff < 0 ? abs($diff) : 0;

                    DB::insert("
                        INSERT INTO inventory_transactions (
                            item_id, warehouse_id, location_id, transaction_type,
                            quantity_in, quantity_out, balance_after, unit_cost, total_cost,
                            reference_type, reference_id, user_id, date, note, created_at
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'STOCK_TAKE', ?, ?, CURRENT_DATE, ?, NOW())
                    ", [
                        $item->item_id,
                        $stockTake->warehouse_id,
                        $stockTake->location_id,
                        $txType,
                        $qtyIn,
                        $qtyOut,
                        $item->counted_quantity,
                        $item->unit_cost,
                        abs($diff) * $item->unit_cost,
                        $id,
                        $userId,
                        'Stock take reconciliation: ' . ($item->reason ?: 'Variance adjusted')
                    ]);
                }
            }

            DB::update("
                UPDATE stock_takes
                SET status = 'APPROVED', approved_by = ?, updated_at = NOW()
                WHERE id = ?
            ", [$userId, $id]);

            audit_log('APPROVE', 'StockTake', 'StockTake', $id);
        });

        return back()->with('success', 'Stock take approved and inventory reconciled.');
    }
}
