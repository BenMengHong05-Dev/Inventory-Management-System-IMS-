<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $module = $request->get('module');
        $action = $request->get('action');
        $userId = $request->get('user_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $sql = "
            SELECT a.*, u.name as user_name, u.email as user_email
            FROM audit_logs a
            LEFT JOIN users u ON a.user_id = u.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (a.record_type LIKE ? OR a.ip_address LIKE ? OR a.old_data LIKE ? OR a.new_data LIKE ?)";
            $term = "%{$search}%";
            $params = array_merge($params, [$term, $term, $term, $term]);
        }

        if (!empty($module)) {
            $sql .= " AND a.module = ?";
            $params[] = $module;
        }

        if (!empty($action)) {
            $sql .= " AND a.action = ?";
            $params[] = $action;
        }

        if (!empty($userId)) {
            $sql .= " AND a.user_id = ?";
            $params[] = $userId;
        }

        if (!empty($startDate)) {
            $sql .= " AND DATE(a.created_at) >= ?";
            $params[] = $startDate;
        }

        if (!empty($endDate)) {
            $sql .= " AND DATE(a.created_at) <= ?";
            $params[] = $endDate;
        }

        $sql .= " ORDER BY a.id DESC";

        $auditLogs = raw_paginate($sql, $params, 20);
        $logs = $auditLogs;

        $modules = DB::select("SELECT DISTINCT module FROM audit_logs ORDER BY module ASC");
        $actions = DB::select("SELECT DISTINCT action FROM audit_logs ORDER BY action ASC");
        $users = DB::select("SELECT id, name FROM users WHERE deleted_at IS NULL ORDER BY name ASC");

        return view('admin.audit_logs.index', compact('auditLogs', 'logs', 'modules', 'actions', 'users', 'search', 'module', 'action', 'userId', 'startDate', 'endDate'));
    }

    public function show($id)
    {
        $logs = DB::select("
            SELECT a.*, u.name as user_name, u.email as user_email
            FROM audit_logs a
            LEFT JOIN users u ON a.user_id = u.id
            WHERE a.id = ?
            LIMIT 1
        ", [$id]);

        if (empty($logs)) {
            abort(404);
        }

        $log = $logs[0];

        return view('admin.audit_logs.show', compact('log'));
    }

    /**
     * Clear/delete all audit log records.
     */
    public function clearAll()
    {
        DB::statement("TRUNCATE TABLE audit_logs");
        return redirect()->back()->with('success', app()->getLocale() == 'km' ? 'កំណត់ត្រាសកម្មភាពទាំងអស់ត្រូវបានលុបចេញដោយជោគជ័យ។' : 'All audit logs cleared successfully.');
    }
}
