<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Custom Blade directive for permission checking via Raw SQL
        Blade::if('hasPermission', function ($permissionSlug) {
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
        });
    }
}
