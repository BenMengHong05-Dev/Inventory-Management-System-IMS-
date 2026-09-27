<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockTakeController;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\GoodsReceiptController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetAssignmentController;
use App\Http\Controllers\AssetReturnController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\DisposalController;
use App\Http\Controllers\ItemReturnController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\BarcodeQrController;
use App\Http\Controllers\GlobalSearchController;

// Language Switcher (both locale.switch and lang.switch)
Route::get('lang/{lang}', [AuthController::class, 'switchLanguage'])->name('lang.switch');
Route::get('locale/{locale}', [AuthController::class, 'switchLanguage'])->name('locale.switch');

// Authentication Routes (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login', [AuthController::class, 'showLogin']);
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Authenticated Routes
Route::middleware(\App\Http\Middleware\AuthenticateCustom::class)->group(function () {
    // Auth Actions
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [AuthController::class, 'changePassword'])->name('profile.password');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Global Search (both global.search and search.global)
    Route::get('/search', [GlobalSearchController::class, 'search'])->name('search.global');
    Route::get('/global-search', [GlobalSearchController::class, 'search'])->name('global.search');

    // Scanner / Barcode Lookup
    Route::get('/scan/lookup', [BarcodeQrController::class, 'scanLookup'])->name('scan.lookup');

    // Items Resource
    Route::resource('items', ItemController::class);

    // Inventory & Stock
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/low-stock', [InventoryController::class, 'lowStock'])->name('low_stock');
        Route::get('/low_stock', [InventoryController::class, 'lowStock'])->name('low-stock');
        Route::get('/history', [InventoryController::class, 'history'])->name('history');

        // Item Returns
        Route::get('/returns', [ItemReturnController::class, 'index'])->name('returns.index');
        Route::get('/returns/create', [ItemReturnController::class, 'create'])->name('returns.create');
        Route::post('/returns', [ItemReturnController::class, 'store'])->name('returns.store');
    });
    // Item Returns
    Route::prefix('item-returns')->name('item-returns.')->group(function () {
        Route::get('/', [ItemReturnController::class, 'index'])->name('index');
        Route::get('/create', [ItemReturnController::class, 'create'])->name('create');
        Route::post('/', [ItemReturnController::class, 'store'])->name('store');
    });

    // Stock In
    Route::resource('stock-in', StockInController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('stock_in', StockInController::class)->only(['index', 'create', 'store', 'show']);

    // Stock Out
    Route::resource('stock-out', StockOutController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('stock_out', StockOutController::class)->only(['index', 'create', 'store', 'show']);

    // Stock Transfers
    Route::prefix('stock-transfers')->name('stock-transfers.')->group(function () {
        Route::get('/', [StockTransferController::class, 'index'])->name('index');
        Route::get('/create', [StockTransferController::class, 'create'])->name('create');
        Route::post('/', [StockTransferController::class, 'store'])->name('store');
        Route::get('/{id}', [StockTransferController::class, 'show'])->name('show');
        Route::post('/{id}/dispatch', [StockTransferController::class, 'dispatch'])->name('dispatch');
        Route::post('/{id}/ship', [StockTransferController::class, 'dispatch'])->name('ship');
        Route::post('/{id}/receive', [StockTransferController::class, 'receive'])->name('receive');
        Route::post('/{id}/cancel', [StockTransferController::class, 'dispatch'])->name('cancel');
    });
    Route::prefix('stock_transfers')->name('stock_transfers.')->group(function () {
        Route::get('/', [StockTransferController::class, 'index'])->name('index');
        Route::get('/create', [StockTransferController::class, 'create'])->name('create');
        Route::post('/', [StockTransferController::class, 'store'])->name('store');
        Route::get('/{id}', [StockTransferController::class, 'show'])->name('show');
        Route::post('/{id}/dispatch', [StockTransferController::class, 'dispatch'])->name('dispatch');
        Route::post('/{id}/ship', [StockTransferController::class, 'dispatch'])->name('ship');
        Route::post('/{id}/receive', [StockTransferController::class, 'receive'])->name('receive');
        Route::post('/{id}/cancel', [StockTransferController::class, 'dispatch'])->name('cancel');
    });

    // Stock Adjustments
    Route::prefix('stock-adjustments')->name('stock-adjustments.')->group(function () {
        Route::get('/', [StockAdjustmentController::class, 'index'])->name('index');
        Route::get('/create', [StockAdjustmentController::class, 'create'])->name('create');
        Route::post('/', [StockAdjustmentController::class, 'store'])->name('store');
        Route::post('/{id}/approve', [StockAdjustmentController::class, 'approve'])->name('approve');
    });
    Route::prefix('stock_adjustments')->name('stock_adjustments.')->group(function () {
        Route::get('/', [StockAdjustmentController::class, 'index'])->name('index');
        Route::get('/create', [StockAdjustmentController::class, 'create'])->name('create');
        Route::post('/', [StockAdjustmentController::class, 'store'])->name('store');
        Route::post('/{id}/approve', [StockAdjustmentController::class, 'approve'])->name('approve');
    });

    // Stock Takes / Audits
    Route::prefix('stock-takes')->name('stock-takes.')->group(function () {
        Route::get('/', [StockTakeController::class, 'index'])->name('index');
        Route::get('/create', [StockTakeController::class, 'create'])->name('create');
        Route::post('/', [StockTakeController::class, 'store'])->name('store');
        Route::get('/{id}', [StockTakeController::class, 'show'])->name('show');
        Route::post('/{id}/update-counts', [StockTakeController::class, 'updateCounts'])->name('update-counts');
        Route::post('/{id}/record', [StockTakeController::class, 'updateCounts'])->name('record');
        Route::post('/{id}/approve', [StockTakeController::class, 'approve'])->name('approve');
        Route::post('/{id}/reconcile', [StockTakeController::class, 'approve'])->name('reconcile');
    });
    Route::prefix('stock_takes')->name('stock_takes.')->group(function () {
        Route::get('/', [StockTakeController::class, 'index'])->name('index');
        Route::get('/create', [StockTakeController::class, 'create'])->name('create');
        Route::post('/', [StockTakeController::class, 'store'])->name('store');
        Route::get('/{id}', [StockTakeController::class, 'show'])->name('show');
        Route::post('/{id}/update-counts', [StockTakeController::class, 'updateCounts'])->name('update-counts');
        Route::post('/{id}/record', [StockTakeController::class, 'updateCounts'])->name('record');
        Route::post('/{id}/approve', [StockTakeController::class, 'approve'])->name('approve');
        Route::post('/{id}/reconcile', [StockTakeController::class, 'approve'])->name('reconcile');
    });

    // Purchasing - Requests
    Route::prefix('purchase-requests')->name('purchase-requests.')->group(function () {
        Route::get('/', [PurchaseRequestController::class, 'index'])->name('index');
        Route::get('/create', [PurchaseRequestController::class, 'create'])->name('create');
        Route::post('/', [PurchaseRequestController::class, 'store'])->name('store');
        Route::get('/{id}', [PurchaseRequestController::class, 'show'])->name('show');
        Route::post('/{id}/status', [PurchaseRequestController::class, 'updateStatus'])->name('status');
        Route::post('/{id}/approve', [PurchaseRequestController::class, 'updateStatus'])->name('approve');
        Route::post('/{id}/reject', [PurchaseRequestController::class, 'updateStatus'])->name('reject');
    });

    // Purchasing - Orders
    Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
        Route::get('/', [PurchaseOrderController::class, 'index'])->name('index');
        Route::get('/create', [PurchaseOrderController::class, 'create'])->name('create');
        Route::post('/', [PurchaseOrderController::class, 'store'])->name('store');
        Route::get('/{id}', [PurchaseOrderController::class, 'show'])->name('show');
    });

    // Purchasing - Goods Receipts
    Route::prefix('goods-receipts')->name('goods-receipts.')->group(function () {
        Route::get('/', [GoodsReceiptController::class, 'index'])->name('index');
        Route::get('/create', [GoodsReceiptController::class, 'create'])->name('create');
        Route::post('/', [GoodsReceiptController::class, 'store'])->name('store');
        Route::get('/{id}', [GoodsReceiptController::class, 'show'])->name('show');
    });

    // Legacy Purchasing aliases
    Route::prefix('purchasing')->name('purchasing.')->group(function () {
        Route::prefix('requests')->name('requests.')->group(function () {
            Route::get('/', [PurchaseRequestController::class, 'index'])->name('index');
            Route::get('/create', [PurchaseRequestController::class, 'create'])->name('create');
            Route::post('/', [PurchaseRequestController::class, 'store'])->name('store');
            Route::get('/{id}', [PurchaseRequestController::class, 'show'])->name('show');
            Route::post('/{id}/status', [PurchaseRequestController::class, 'updateStatus'])->name('status');
            Route::post('/{id}/approve', [PurchaseRequestController::class, 'updateStatus'])->name('approve');
            Route::post('/{id}/reject', [PurchaseRequestController::class, 'updateStatus'])->name('reject');
        });
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [PurchaseOrderController::class, 'index'])->name('index');
            Route::get('/create', [PurchaseOrderController::class, 'create'])->name('create');
            Route::post('/', [PurchaseOrderController::class, 'store'])->name('store');
            Route::get('/{id}', [PurchaseOrderController::class, 'show'])->name('show');
        });
        Route::prefix('receipts')->name('receipts.')->group(function () {
            Route::get('/', [GoodsReceiptController::class, 'index'])->name('index');
            Route::get('/create', [GoodsReceiptController::class, 'create'])->name('create');
            Route::post('/', [GoodsReceiptController::class, 'store'])->name('store');
            Route::get('/{id}', [GoodsReceiptController::class, 'show'])->name('show');
        });
    });

    // Asset Assignments & Returns
    Route::prefix('asset-assignments')->name('asset-assignments.')->group(function () {
        Route::get('/', [AssetAssignmentController::class, 'index'])->name('index');
        Route::get('/create', [AssetAssignmentController::class, 'create'])->name('create');
        Route::post('/', [AssetAssignmentController::class, 'store'])->name('store');
    });
    Route::prefix('asset-returns')->name('asset-returns.')->group(function () {
        Route::get('/', [AssetReturnController::class, 'index'])->name('index');
        Route::get('/create', [AssetReturnController::class, 'create'])->name('create');
        Route::post('/', [AssetReturnController::class, 'store'])->name('store');
    });

    // Maintenance & Disposals
    Route::prefix('maintenance')->name('maintenance.')->group(function () {
        Route::get('/', [MaintenanceController::class, 'index'])->name('index');
        Route::get('/create', [MaintenanceController::class, 'create'])->name('create');
        Route::post('/', [MaintenanceController::class, 'store'])->name('store');
        Route::get('/{id}', [MaintenanceController::class, 'show'])->name('show');
        Route::post('/{id}/status', [MaintenanceController::class, 'updateStatus'])->name('update_status');
    });
    Route::prefix('disposals')->name('disposals.')->group(function () {
        Route::get('/', [DisposalController::class, 'index'])->name('index');
        Route::get('/create', [DisposalController::class, 'create'])->name('create');
        Route::post('/', [DisposalController::class, 'store'])->name('store');
    });

    // Assets Management
    Route::prefix('assets')->name('assets.')->group(function () {
        Route::get('/warranty', [AssetController::class, 'warranty'])->name('warranty');

        // Asset Assignments
        Route::prefix('assignments')->name('assignments.')->group(function () {
            Route::get('/', [AssetAssignmentController::class, 'index'])->name('index');
            Route::get('/create', [AssetAssignmentController::class, 'create'])->name('create');
            Route::post('/', [AssetAssignmentController::class, 'store'])->name('store');
        });

        // Asset Returns
        Route::prefix('returns')->name('returns.')->group(function () {
            Route::get('/', [AssetReturnController::class, 'index'])->name('index');
            Route::get('/create', [AssetReturnController::class, 'create'])->name('create');
            Route::post('/', [AssetReturnController::class, 'store'])->name('store');
        });

        // Asset Maintenance
        Route::prefix('maintenance')->name('maintenance.')->group(function () {
            Route::get('/', [MaintenanceController::class, 'index'])->name('index');
            Route::get('/create', [MaintenanceController::class, 'create'])->name('create');
            Route::post('/', [MaintenanceController::class, 'store'])->name('store');
            Route::get('/{id}', [MaintenanceController::class, 'show'])->name('show');
            Route::post('/{id}/status', [MaintenanceController::class, 'updateStatus'])->name('update_status');
        });

        // Asset Disposals
        Route::prefix('disposals')->name('disposals.')->group(function () {
            Route::get('/', [DisposalController::class, 'index'])->name('index');
            Route::get('/create', [DisposalController::class, 'create'])->name('create');
            Route::post('/', [DisposalController::class, 'store'])->name('store');
        });

        // Asset Resource
        Route::get('/', [AssetController::class, 'index'])->name('index');
        Route::get('/create', [AssetController::class, 'create'])->name('create');
        Route::post('/', [AssetController::class, 'store'])->name('store');
        Route::get('/{id}', [AssetController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [AssetController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AssetController::class, 'update'])->name('update');
        Route::delete('/{id}', [AssetController::class, 'destroy'])->name('destroy');
    });

    // Master Data
    Route::prefix('master')->name('master.')->group(function () {
        Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);
        Route::resource('brands', BrandController::class)->except(['create', 'show', 'edit']);
        Route::resource('units', UnitController::class)->except(['create', 'show', 'edit']);
        Route::resource('suppliers', SupplierController::class);
        Route::resource('warehouses', WarehouseController::class)->except(['create', 'edit']);
        Route::resource('locations', LocationController::class)->except(['create', 'show', 'edit']);
        Route::resource('departments', DepartmentController::class)->except(['create', 'show', 'edit']);
        Route::resource('employees', EmployeeController::class);
    });
    // Master data sidebar aliases
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
    Route::get('/units', [UnitController::class, 'index'])->name('units.index');
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');
    Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
    Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');

    // Reports Hub
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('/inventory/export', [ReportController::class, 'exportInventory'])->name('inventory.export');
        Route::get('/assets', [ReportController::class, 'assets'])->name('assets');
        Route::get('/assets/export', [ReportController::class, 'exportAssets'])->name('assets.export');
        Route::get('/purchasing', [ReportController::class, 'purchasing'])->name('purchasing');
        Route::get('/purchasing/export', [ReportController::class, 'exportPurchasing'])->name('purchasing.export');
    });

    // Admin & Settings
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);

        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RolePermissionController::class, 'index'])->name('index');
            Route::post('/', [RolePermissionController::class, 'storeRole'])->name('store');
            Route::get('/{id}/permissions', [RolePermissionController::class, 'edit'])->name('edit');
            Route::put('/{id}/permissions', [RolePermissionController::class, 'updatePermissions'])->name('update');
            Route::delete('/{id}', [RolePermissionController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('mark_read');
            Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark_all_read');
        });

        Route::prefix('audit-logs')->name('audit_logs.')->group(function () {
            Route::get('/', [AuditLogController::class, 'index'])->name('index');
            Route::get('/{id}', [AuditLogController::class, 'show'])->name('show');
        });
    });
    // Admin sidebar & direct route aliases
    Route::resource('users', UserController::class)->except(['show']);
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RolePermissionController::class, 'index'])->name('index');
        Route::post('/', [RolePermissionController::class, 'store'])->name('store');
        Route::get('/{id}/permissions', [RolePermissionController::class, 'edit'])->name('edit');
        Route::put('/{id}/permissions', [RolePermissionController::class, 'update'])->name('update');
        Route::delete('/{id}', [RolePermissionController::class, 'destroy'])->name('destroy');
    });
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [NotificationController::class, 'getUnread'])->name('notifications.unread');
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::match(['get', 'post'], '/audit-logs/clear', [AuditLogController::class, 'clearAll'])->name('audit-logs.clear');
    Route::match(['get', 'post'], '/admin/audit-logs/clear', [AuditLogController::class, 'clearAll'])->name('admin.audit_logs.clear');

    // Barcode & QR Code Printable Sheets
    Route::prefix('labels')->name('labels.')->group(function () {
        Route::get('/items', [BarcodeQrController::class, 'printItemLabels'])->name('items');
        Route::get('/assets', [BarcodeQrController::class, 'printAssetLabels'])->name('assets');
    });
});
