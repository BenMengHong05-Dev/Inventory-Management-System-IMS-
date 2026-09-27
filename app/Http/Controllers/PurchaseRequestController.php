<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseRequestController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $priority = $request->get('priority');

        $sql = "
            SELECT pr.*, d.name_en as department_name, u.name as requester_name, ap.name as approver_name,
                   (SELECT COUNT(*) FROM purchase_request_items pri WHERE pri.purchase_request_id = pr.id) as total_items,
                   (SELECT COALESCE(SUM(estimated_total), 0) FROM purchase_request_items pri WHERE pri.purchase_request_id = pr.id) as estimated_total
            FROM purchase_requests pr
            LEFT JOIN departments d ON pr.department_id = d.id
            JOIN users u ON pr.requester_id = u.id
            LEFT JOIN users ap ON pr.approved_by = ap.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (pr.request_number LIKE ? OR pr.purpose LIKE ?)";
            $term = "%{$search}%";
            $params[] = $term;
            $params[] = $term;
        }

        if (!empty($status)) {
            $sql .= " AND pr.status = ?";
            $params[] = $status;
        }

        if (!empty($priority)) {
            $sql .= " AND pr.priority = ?";
            $params[] = $priority;
        }

        $sql .= " ORDER BY pr.id DESC";

        $purchaseRequests = raw_paginate($sql, $params, 15);

        return view('purchasing.requests.index', compact('purchaseRequests', 'search', 'status', 'priority'));
    }

    public function create()
    {
        $nextNumber = generate_code('PR', 'purchase_requests', 'request_number');
        $departments = DB::select("SELECT id, name_en FROM departments WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name_en ASC");
        $items = DB::select("SELECT id, code, name_en, default_unit_cost, unit_id FROM items WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name_en ASC");
        $units = DB::select("SELECT id, name_en, code FROM units ORDER BY name_en ASC");

        return view('purchasing.requests.create', compact('nextNumber', 'departments', 'items', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'request_number' => 'required|string|max:50|unique:purchase_requests,request_number',
            'department_id' => 'nullable|integer',
            'request_date' => 'required|date',
            'required_date' => 'nullable|date|after_or_equal:request_date',
            'priority' => 'required|in:LOW,MEDIUM,HIGH,URGENT',
            'purpose' => 'nullable|string|max:255',
            'note' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|integer',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.estimated_unit_price' => 'nullable|numeric|min:0',
            'items.*.unit_id' => 'nullable|integer',
        ]);

        $userId = session('user_id');

        DB::transaction(function () use ($request, $userId) {
            $uuid = (string) Str::uuid();

            DB::insert("
                INSERT INTO purchase_requests (
                    uuid, request_number, department_id, requester_id, request_date,
                    required_date, priority, purpose, status, note, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'PENDING', ?, NOW(), NOW())
            ", [
                $uuid,
                strtoupper($request->request_number),
                $request->department_id ?: null,
                $userId,
                $request->request_date,
                $request->required_date ?: null,
                $request->priority,
                $request->purpose,
                $request->note
            ]);

            $prId = DB::getPdo()->lastInsertId();

            foreach ($request->items as $itemData) {
                $unitPrice = (float)($itemData['estimated_unit_price'] ?? 0);
                $qty = (float)$itemData['quantity'];
                $total = $qty * $unitPrice;

                DB::insert("
                    INSERT INTO purchase_request_items (
                        purchase_request_id, item_id, quantity, unit_id,
                        estimated_unit_price, estimated_total, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
                ", [
                    $prId,
                    $itemData['item_id'],
                    $qty,
                    $itemData['unit_id'] ?? null,
                    $unitPrice,
                    $total
                ]);
            }

            record_status_history('PurchaseRequest', $prId, null, 'PENDING', 'Purchase request submitted');
            audit_log('CREATE', 'Purchasing', 'PurchaseRequest', $prId, null, ['number' => $request->request_number]);
        });

        return redirect()->route('purchase-requests.index')->with('success', 'Purchase request created successfully.');
    }

    public function show($id)
    {
        $requests = DB::select("
            SELECT pr.*, d.name_en as department_name, u.name as requester_name, ap.name as approver_name
            FROM purchase_requests pr
            LEFT JOIN departments d ON pr.department_id = d.id
            JOIN users u ON pr.requester_id = u.id
            LEFT JOIN users ap ON pr.approved_by = ap.id
            WHERE pr.id = ?
            LIMIT 1
        ", [$id]);

        if (empty($requests)) {
            abort(404);
        }

        $purchaseRequest = $requests[0];

        $items = DB::select("
            SELECT pri.*, i.code as item_code, i.name_en as item_name, u.name_en as unit_name
            FROM purchase_request_items pri
            JOIN items i ON pri.item_id = i.id
            LEFT JOIN units u ON pri.unit_id = u.id
            WHERE pri.purchase_request_id = ?
        ", [$id]);

        $histories = DB::select("
            SELECT sh.*, u.name as user_name
            FROM status_histories sh
            LEFT JOIN users u ON sh.user_id = u.id
            WHERE sh.record_type = 'PurchaseRequest' AND sh.record_id = ?
            ORDER BY sh.id ASC
        ", [$id]);

        return view('purchasing.requests.show', compact('purchaseRequest', 'items', 'histories'));
    }

    /**
     * Approve or Reject PR with separation of duties enforcement.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:APPROVED,REJECTED,CANCELLED',
            'comment' => 'nullable|string|max:255',
        ]);

        $userId = session('user_id');
        $pr = DB::select("SELECT * FROM purchase_requests WHERE id = ? LIMIT 1", [$id]);
        if (empty($pr) || $pr[0]->status !== 'PENDING') {
            return back()->with('error', 'Purchase request is not in a pending state.');
        }

        // Separation of duties rule: Requester cannot approve their own PR
        if ($request->status === 'APPROVED' && $pr[0]->requester_id == $userId && !has_permission('super-admin')) {
            return back()->with('error', 'Separation of duties: You cannot approve your own purchase request.');
        }

        DB::update("
            UPDATE purchase_requests
            SET status = ?, approved_by = ?, updated_at = NOW()
            WHERE id = ?
        ", [$request->status, $userId, $id]);

        record_status_history('PurchaseRequest', $id, $pr[0]->status, $request->status, $request->comment);
        audit_log($request->status, 'Purchasing', 'PurchaseRequest', $id, null, ['comment' => $request->comment]);

        create_notification(
            $pr[0]->requester_id,
            'PR_STATUS',
            "Purchase Request {$request->status}",
            "Your purchase request [{$pr[0]->request_number}] was {$request->status}.",
            'PurchaseRequest',
            $id
        );

        return back()->with('success', "Purchase request marked as {$request->status}.");
    }
}
