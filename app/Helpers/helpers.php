<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

if (!function_exists('audit_log')) {
    /**
     * Record an immutable audit log via raw SQL (safe & privacy-preserving).
     */
    function audit_log(string $action, string $module, string $recordType, ?int $recordId = null, $oldData = null, $newData = null): void
    {
        if (config('app.disable_audit_logs', false) || env('DISABLE_AUDIT_LOGS', false)) {
            return;
        }

        try {
            $userId = session('user_id');
            // Anonymize IP and agent for user privacy & security
            $ip = '127.0.0.1';
            $userAgent = 'Secure IMS';

            DB::insert("
                INSERT INTO audit_logs (user_id, action, module, record_type, record_id, old_data, new_data, ip_address, user_agent, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ", [
                $userId,
                strtoupper($action),
                $module,
                $recordType,
                $recordId,
                $oldData ? json_encode($oldData) : null,
                $newData ? json_encode($newData) : null,
                $ip,
                $userAgent
            ]);
        } catch (\Throwable $e) {
            // Safe fallback
        }
    }
}

if (!function_exists('record_status_history')) {
    /**
     * Record a status transition history via raw SQL.
     */
    function record_status_history(string $recordType, int $recordId, ?string $fromStatus, string $toStatus, ?string $comment = null): void
    {
        try {
            $userId = session('user_id');
            DB::insert("
                INSERT INTO status_histories (record_type, record_id, from_status, to_status, user_id, comment, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ", [$recordType, $recordId, $fromStatus, $toStatus, $userId, $comment]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Status history failed: ' . $e->getMessage());
        }
    }
}

if (!function_exists('create_notification')) {
    /**
     * Create an in-app notification via raw SQL.
     */
    function create_notification(?int $userId, string $type, string $title, string $message, ?string $refType = null, ?int $refId = null): void
    {
        try {
            DB::insert("
                INSERT INTO notifications (uuid, user_id, type, title, message, reference_type, reference_id, is_read, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, 0, NOW(), NOW())
            ", [
                (string) Str::uuid(),
                $userId,
                $type,
                $title,
                $message,
                $refType,
                $refId
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Notification creation failed: ' . $e->getMessage());
        }
    }
}

if (!function_exists('has_permission')) {
    /**
     * Check if the currently authenticated user has a specific permission via raw SQL.
     */
    function has_permission(string $permissionSlug): bool
    {
        $userId = session('user_id');
        if (!$userId) {
            return false;
        }

        // Check if user is Super Admin
        $isAdmin = DB::select("
            SELECT 1 FROM user_roles ur
            JOIN roles r ON ur.role_id = r.id
            WHERE ur.user_id = ? AND r.slug = 'super-admin'
            LIMIT 1
        ", [$userId]);

        if (!empty($isAdmin)) {
            return true;
        }

        // Check specific permission
        $hasPerm = DB::select("
            SELECT 1 FROM user_roles ur
            JOIN role_permissions rp ON ur.role_id = rp.role_id
            JOIN permissions p ON rp.permission_id = p.id
            WHERE ur.user_id = ? AND p.slug = ?
            LIMIT 1
        ", [$userId, $permissionSlug]);

        return !empty($hasPerm);
    }
}

if (!function_exists('current_user')) {
    /**
     * Fetch the currently authenticated user record via raw SQL.
     */
    function current_user()
    {
        $userId = session('user_id');
        if (!$userId) {
            return null;
        }

        $users = DB::select("SELECT * FROM users WHERE id = ? AND deleted_at IS NULL LIMIT 1", [$userId]);
        return !empty($users) ? $users[0] : null;
    }
}

if (!function_exists('generate_code')) {
    /**
     * Generate unique auto-incremented entity code (e.g. ITM-000001, WH-000001).
     */
    function generate_code(string $prefix, string $table, string $column = 'code'): string
    {
        $result = DB::select("SELECT MAX(id) as max_id FROM {$table}");
        $nextId = (!empty($result) && $result[0]->max_id) ? ((int)$result[0]->max_id + 1) : 1;
        return $prefix . '-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('raw_paginate')) {
    /**
     * Execute raw SQL with pagination, returning a standard LengthAwarePaginator.
     */
    function raw_paginate(string $selectSql, array $bindings = [], int $perPage = 15, string $pageName = 'page')
    {
        $page = (int) request()->get($pageName, 1);
        if ($page < 1) {
            $page = 1;
        }

        // Generate count query by wrapping the query
        $countSql = "SELECT COUNT(*) as aggregate FROM ({$selectSql}) as count_table";
        $totalResult = DB::select($countSql, $bindings);
        $total = !empty($totalResult) ? (int)$totalResult[0]->aggregate : 0;

        // Add Limit & Offset to select query
        $offset = ($page - 1) * $perPage;
        $paginatedSql = "{$selectSql} LIMIT {$perPage} OFFSET {$offset}";
        $items = DB::select($paginatedSql, $bindings);

        return new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
                'pageName' => $pageName,
            ]
        );
    }
}

if (!function_exists('format_currency')) {
    /**
     * Format number as currency.
     */
    function format_currency($amount, string $currency = '$'): string
    {
        return $currency . ' ' . number_format((float)$amount, 2);
    }
}

if (!function_exists('t')) {
    /**
     * Localized string helper with fallback to messages.php dictionary.
     */
    function t(string $key, $replace = [], ?string $locale = null): string
    {
        $cleanKey = str_starts_with($key, 'messages.') ? substr($key, 9) : $key;
        $lookupKey = "messages.{$cleanKey}";

        $trans = (string) __(
            $lookupKey, 
            is_array($replace) ? $replace : [], 
            $locale ?: app()->getLocale()
        );

        if ($trans === $lookupKey) {
            $rawTrans = (string) __(
                $key, 
                is_array($replace) ? $replace : [], 
                $locale ?: app()->getLocale()
            );
            if ($rawTrans !== $key && $rawTrans !== $lookupKey) {
                return $rawTrans;
            }
            if (is_string($replace)) {
                return $replace;
            }
            // Clean readable fallback
            return ucwords(str_replace('_', ' ', $cleanKey));
        }

        return $trans;
    }
}

if (!function_exists('localized_name')) {
    /**
     * Get localized name of an entity (e.g. category, brand, unit, item, department).
     * Returns name_km when locale is Khmer, otherwise name_en or name.
     */
    function localized_name($entity, string $field = 'name'): string
    {
        if (!$entity) {
            return '';
        }
        $locale = app()->getLocale();
        $isObj = is_object($entity);
        $kmField = $field . '_km';
        $enField = $field . '_en';

        if ($locale === 'km') {
            $val = $isObj ? ($entity->$kmField ?? null) : ($entity[$kmField] ?? null);
            if (!empty($val)) {
                return (string) $val;
            }
        }

        $val = $isObj ? ($entity->$enField ?? null) : ($entity[$enField] ?? null);
        if (!empty($val)) {
            return (string) $val;
        }

        $val = $isObj ? ($entity->$field ?? null) : ($entity[$field] ?? null);
        if (!empty($val)) {
            return (string) $val;
        }

        return '';
    }
}


