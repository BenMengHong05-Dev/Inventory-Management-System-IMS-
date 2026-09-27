<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Auth & RBAC Tables
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->string('avatar')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'created_at']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('module');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->index('module');
        });

        Schema::create('user_roles', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->primary(['user_id', 'role_id']);
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->primary(['role_id', 'permission_id']);
        });

        // 2. Master Data Tables
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code')->unique();
            $table->string('name_en');
            $table->string('name_km');
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->text('description')->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->timestamps();
            $table->softDeletes();
            $table->index('status');
        });

        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->timestamps();
            $table->softDeletes();
            $table->index('status');
        });

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_km');
            $table->string('code')->unique();
            $table->timestamps();
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code')->unique();
            $table->string('name_en');
            $table->string('name_km');
            $table->unsignedBigInteger('manager_id')->nullable()->index();
            $table->text('description')->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->timestamps();
            $table->softDeletes();
            $table->index('status');
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code')->unique();
            $table->string('name_en');
            $table->string('name_km');
            $table->enum('gender', ['MALE', 'FEMALE', 'OTHER'])->default('MALE');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->string('position')->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->string('photo')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'department_id']);
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('tax_number')->nullable();
            $table->text('bank_info')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->timestamps();
            $table->softDeletes();
            $table->index('status');
        });

        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code')->unique();
            $table->string('name');
            $table->unsignedBigInteger('manager_id')->nullable()->index();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->timestamps();
            $table->softDeletes();
            $table->index('status');
        });

        Schema::create('warehouse_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->string('building')->nullable();
            $table->string('floor')->nullable();
            $table->string('room')->nullable();
            $table->string('rack')->nullable();
            $table->string('shelf')->nullable();
            $table->string('bin')->nullable();
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['warehouse_id', 'status']);
        });

        // 3. Item & Inventory Tables
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code')->unique();
            $table->string('barcode')->nullable()->unique();
            $table->string('qr_code')->nullable()->unique();
            $table->string('name_en');
            $table->string('name_km');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->unsignedBigInteger('sub_category_id')->nullable()->index();
            $table->foreignId('brand_id')->nullable()->constrained('brands')->onDelete('set null');
            $table->string('model')->nullable();
            $table->foreignId('unit_id')->nullable()->constrained('units')->onDelete('set null');
            $table->enum('item_type', ['CONSUMABLE', 'NON_CONSUMABLE', 'ASSET'])->default('CONSUMABLE');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->decimal('min_stock', 15, 2)->default(0);
            $table->decimal('max_stock', 15, 2)->default(0);
            $table->decimal('reorder_level', 15, 2)->default(0);
            $table->decimal('default_unit_cost', 15, 2)->default(0);
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['category_id', 'brand_id', 'status', 'item_type']);
        });

        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->foreignId('location_id')->nullable()->constrained('warehouse_locations')->onDelete('set null');
            $table->decimal('quantity', 15, 2)->default(0);
            $table->decimal('reserved_quantity', 15, 2)->default(0);
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->decimal('total_value', 15, 2)->default(0);
            $table->timestamp('last_movement_date')->nullable();
            $table->timestamps();
            $table->unique(['item_id', 'warehouse_id', 'location_id']);
            $table->index(['item_id', 'warehouse_id']);
        });

        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->foreignId('location_id')->nullable()->constrained('warehouse_locations');
            $table->enum('transaction_type', [
                'STOCK_IN', 'STOCK_OUT', 'TRANSFER_IN', 'TRANSFER_OUT',
                'ADJUSTMENT_IN', 'ADJUSTMENT_OUT', 'STOCK_TAKE_IN', 'STOCK_TAKE_OUT',
                'RETURN_IN', 'RETURN_OUT'
            ]);
            $table->decimal('quantity_in', 15, 2)->default(0);
            $table->decimal('quantity_out', 15, 2)->default(0);
            $table->decimal('balance_after', 15, 2);
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->date('date');
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['item_id', 'warehouse_id', 'transaction_type', 'date'], 'inv_trx_item_wh_type_date_idx');
            $table->index(['reference_type', 'reference_id']);
        });

        // 4. Stock In & Stock Out
        Schema::create('stock_ins', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('stock_in_number')->unique();
            $table->string('reference_number')->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->unsignedBigInteger('purchase_order_id')->nullable()->index();
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->foreignId('location_id')->nullable()->constrained('warehouse_locations');
            $table->date('date');
            $table->foreignId('received_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->enum('status', ['DRAFT', 'PENDING', 'APPROVED', 'CANCELLED'])->default('APPROVED');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['status', 'date', 'warehouse_id']);
        });

        Schema::create('stock_in_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_in_id')->constrained('stock_ins')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('items');
            $table->foreignId('unit_id')->nullable()->constrained('units');
            $table->decimal('quantity', 15, 2);
            $table->decimal('unit_cost', 15, 2);
            $table->decimal('total_cost', 15, 2);
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('serial_number')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_outs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('stock_out_number')->unique();
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->foreignId('employee_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->date('date');
            $table->string('purpose')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->foreignId('issued_by')->constrained('users');
            $table->enum('status', ['DRAFT', 'PENDING', 'APPROVED', 'CANCELLED'])->default('APPROVED');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['status', 'date', 'warehouse_id']);
        });

        Schema::create('stock_out_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_out_id')->constrained('stock_outs')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('items');
            $table->foreignId('unit_id')->nullable()->constrained('units');
            $table->decimal('quantity', 15, 2);
            $table->decimal('unit_cost', 15, 2);
            $table->decimal('total_value', 15, 2);
            $table->timestamps();
        });

        // 5. Stock Transfers & Adjustments
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('transfer_number')->unique();
            $table->foreignId('from_warehouse_id')->constrained('warehouses');
            $table->foreignId('from_location_id')->nullable()->constrained('warehouse_locations');
            $table->foreignId('to_warehouse_id')->constrained('warehouses');
            $table->foreignId('to_location_id')->nullable()->constrained('warehouse_locations');
            $table->date('transfer_date');
            $table->foreignId('requested_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->foreignId('received_by')->nullable()->constrained('users');
            $table->enum('status', ['DRAFT', 'PENDING', 'APPROVED', 'IN_TRANSIT', 'RECEIVED', 'COMPLETED', 'CANCELLED'])->default('PENDING');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['status', 'transfer_date']);
        });

        Schema::create('stock_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_transfer_id')->constrained('stock_transfers')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('items');
            $table->foreignId('unit_id')->nullable()->constrained('units');
            $table->decimal('quantity', 15, 2);
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('adjustment_number')->unique();
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->foreignId('location_id')->nullable()->constrained('warehouse_locations');
            $table->foreignId('item_id')->constrained('items');
            $table->enum('adjustment_type', ['INCREASE', 'DECREASE']);
            $table->decimal('system_quantity', 15, 2);
            $table->decimal('actual_quantity', 15, 2);
            $table->decimal('difference', 15, 2);
            $table->text('reason');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->foreignId('created_by')->constrained('users');
            $table->date('date');
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])->default('APPROVED');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['status', 'date', 'warehouse_id']);
        });

        // 6. Stock Takes
        Schema::create('stock_takes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('stock_take_number')->unique();
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->foreignId('location_id')->nullable()->constrained('warehouse_locations');
            $table->date('date');
            $table->enum('status', ['DRAFT', 'IN_PROGRESS', 'REVIEWED', 'APPROVED', 'CANCELLED'])->default('DRAFT');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['status', 'date', 'warehouse_id']);
        });

        Schema::create('stock_take_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_take_id')->constrained('stock_takes')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('items');
            $table->decimal('system_quantity', 15, 2);
            $table->decimal('counted_quantity', 15, 2)->nullable();
            $table->decimal('difference', 15, 2)->default(0);
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->decimal('difference_value', 15, 2)->default(0);
            $table->text('reason')->nullable();
            $table->timestamps();
        });

        // 7. Purchasing
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('request_number')->unique();
            $table->foreignId('department_id')->nullable()->constrained('departments');
            $table->foreignId('requester_id')->constrained('users');
            $table->date('request_date');
            $table->date('required_date')->nullable();
            $table->enum('priority', ['LOW', 'MEDIUM', 'HIGH', 'URGENT'])->default('MEDIUM');
            $table->string('purpose')->nullable();
            $table->enum('status', ['DRAFT', 'PENDING', 'APPROVED', 'REJECTED', 'CANCELLED', 'COMPLETED'])->default('PENDING');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['status', 'request_date']);
        });

        Schema::create('purchase_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')->constrained('purchase_requests')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('items');
            $table->decimal('quantity', 15, 2);
            $table->foreignId('unit_id')->nullable()->constrained('units');
            $table->decimal('estimated_unit_price', 15, 2)->default(0);
            $table->decimal('estimated_total', 15, 2)->default(0);
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('po_number')->unique();
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('purchase_request_id')->nullable()->constrained('purchase_requests')->onDelete('set null');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->date('order_date');
            $table->date('expected_delivery_date')->nullable();
            $table->string('payment_terms')->nullable();
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('status', ['DRAFT', 'PENDING', 'APPROVED', 'ORDERED', 'PARTIALLY_RECEIVED', 'RECEIVED', 'CANCELLED'])->default('DRAFT');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['status', 'order_date', 'supplier_id']);
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('items');
            $table->decimal('quantity', 15, 2);
            $table->decimal('received_quantity', 15, 2)->default(0);
            $table->foreignId('unit_id')->nullable()->constrained('units');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            $table->timestamps();
        });

        Schema::create('goods_receipts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('receipt_number')->unique();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->foreignId('location_id')->nullable()->constrained('warehouse_locations');
            $table->date('receipt_date');
            $table->foreignId('received_by')->constrained('users');
            $table->enum('status', ['RECEIVED', 'CANCELLED'])->default('RECEIVED');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['purchase_order_id', 'receipt_date']);
        });

        Schema::create('goods_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goods_receipt_id')->constrained('goods_receipts')->onDelete('cascade');
            $table->foreignId('purchase_order_item_id')->constrained('purchase_order_items');
            $table->foreignId('item_id')->constrained('items');
            $table->decimal('quantity_received', 15, 2);
            $table->decimal('unit_cost', 15, 2);
            $table->decimal('total_cost', 15, 2);
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('serial_number')->nullable();
            $table->timestamps();
        });

        // 8. Asset Management
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('asset_code')->unique();
            $table->string('asset_name');
            $table->foreignId('item_id')->nullable()->constrained('items')->onDelete('set null');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->foreignId('brand_id')->nullable()->constrained('brands')->onDelete('set null');
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable()->unique();
            $table->string('barcode')->nullable()->unique();
            $table->string('qr_code')->nullable()->unique();
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->onDelete('set null');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 15, 2)->default(0);
            $table->date('warranty_start')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->onDelete('set null');
            $table->foreignId('location_id')->nullable()->constrained('warehouse_locations')->onDelete('set null');
            $table->enum('condition', ['NEW', 'GOOD', 'FAIR', 'DAMAGED', 'CRITICAL'])->default('GOOD');
            $table->enum('status', ['IN_STOCK', 'ASSIGNED', 'IN_USE', 'MAINTENANCE', 'LOST', 'DAMAGED', 'DISPOSED', 'RETURNED'])->default('IN_STOCK');
            $table->foreignId('current_department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->foreignId('current_employee_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->string('image')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'condition', 'warranty_expiry', 'current_employee_id'], 'assets_status_cond_exp_emp_idx');
        });

        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('asset_id')->constrained('assets');
            $table->foreignId('employee_id')->nullable()->constrained('employees');
            $table->foreignId('department_id')->nullable()->constrained('departments');
            $table->foreignId('location_id')->nullable()->constrained('warehouse_locations');
            $table->date('assignment_date');
            $table->date('expected_return_date')->nullable();
            $table->date('actual_return_date')->nullable();
            $table->foreignId('assigned_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->text('note')->nullable();
            $table->string('signature_ack')->nullable();
            $table->timestamps();
            $table->index(['asset_id', 'employee_id']);
        });

        Schema::create('asset_assignment_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets');
            $table->unsignedBigInteger('previous_holder_id')->nullable();
            $table->unsignedBigInteger('new_holder_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->date('assignment_date');
            $table->date('return_date')->nullable();
            $table->string('action'); // ASSIGN, RETURN, TRANSFER
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index('asset_id');
        });

        Schema::create('asset_returns', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('asset_id')->constrained('assets');
            $table->foreignId('asset_assignment_id')->nullable()->constrained('asset_assignments');
            $table->foreignId('returned_by')->nullable()->constrained('employees');
            $table->date('return_date');
            $table->enum('return_condition', ['NEW', 'GOOD', 'FAIR', 'DAMAGED', 'CRITICAL'])->default('GOOD');
            $table->foreignId('received_by')->constrained('users');
            $table->foreignId('location_id')->nullable()->constrained('warehouse_locations');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index('asset_id');
        });

        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('maintenance_number')->unique();
            $table->foreignId('asset_id')->constrained('assets');
            $table->foreignId('requester_id')->constrained('users');
            $table->string('problem');
            $table->text('description')->nullable();
            $table->date('request_date');
            $table->date('start_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->string('technician')->nullable();
            $table->string('service_provider')->nullable();
            $table->decimal('cost', 15, 2)->default(0);
            $table->boolean('warranty_coverage')->default(false);
            $table->enum('status', ['PENDING', 'APPROVED', 'IN_PROGRESS', 'COMPLETED', 'CANCELLED'])->default('PENDING');
            $table->text('diagnosis')->nullable();
            $table->text('solution')->nullable();
            $table->text('parts_used')->nullable();
            $table->timestamps();
            $table->index(['asset_id', 'status']);
        });

        Schema::create('asset_disposals', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('disposal_number')->unique();
            $table->foreignId('asset_id')->constrained('assets');
            $table->enum('reason', ['DAMAGED', 'OLD', 'BEYOND_REPAIR', 'LOST', 'REPLACED', 'SOLD', 'DONATED', 'OTHER']);
            $table->date('disposal_date');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->string('disposal_method')->nullable();
            $table->decimal('disposal_value', 15, 2)->default(0);
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['asset_id', 'reason']);
        });

        Schema::create('item_returns', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('return_number')->unique();
            $table->foreignId('stock_out_id')->nullable()->constrained('stock_outs');
            $table->foreignId('item_id')->constrained('items');
            $table->decimal('returned_quantity', 15, 2);
            $table->string('condition')->default('GOOD');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->foreignId('location_id')->nullable()->constrained('warehouse_locations');
            $table->foreignId('returned_by')->nullable()->constrained('employees');
            $table->foreignId('received_by')->constrained('users');
            $table->date('return_date');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['item_id', 'return_date']);
        });

        // 9. Governance, Notifications & Audit Logs
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('type'); // LOW_STOCK, OUT_OF_STOCK, PO_APPROVAL, WARRANTY_EXPIRY, etc.
            $table->string('title');
            $table->text('message');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            $table->index(['user_id', 'is_read', 'created_at']);
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('action'); // LOGIN, CREATE, UPDATE, DELETE, APPROVE, REJECT, CANCEL, STOCK_IN, STOCK_OUT, etc.
            $table->string('module');
            $table->string('record_type');
            $table->unsignedBigInteger('record_id')->nullable();
            $table->longText('old_data')->nullable();
            $table->longText('new_data')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['user_id', 'module', 'action', 'created_at']);
            $table->index(['record_type', 'record_id']);
        });

        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->string('record_type'); // PURCHASE_REQUEST, PURCHASE_ORDER, GOODS_RECEIPT, ASSET, MAINTENANCE, DISPOSAL, SUPPLIER, STOCK_ADJUSTMENT
            $table->unsignedBigInteger('record_id');
            $table->string('file_name');
            $table->string('file_path');
            $table->unsignedBigInteger('file_size');
            $table->string('file_type');
            $table->foreignId('uploaded_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->index(['record_type', 'record_id']);
        });

        Schema::create('status_histories', function (Blueprint $table) {
            $table->id();
            $table->string('record_type');
            $table->unsignedBigInteger('record_id');
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->text('comment')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['record_type', 'record_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_histories');
        Schema::dropIfExists('attachments');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('item_returns');
        Schema::dropIfExists('asset_disposals');
        Schema::dropIfExists('maintenances');
        Schema::dropIfExists('asset_returns');
        Schema::dropIfExists('asset_assignment_histories');
        Schema::dropIfExists('asset_assignments');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('goods_receipt_items');
        Schema::dropIfExists('goods_receipts');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('purchase_request_items');
        Schema::dropIfExists('purchase_requests');
        Schema::dropIfExists('stock_take_items');
        Schema::dropIfExists('stock_takes');
        Schema::dropIfExists('stock_adjustments');
        Schema::dropIfExists('stock_transfer_items');
        Schema::dropIfExists('stock_transfers');
        Schema::dropIfExists('stock_out_items');
        Schema::dropIfExists('stock_outs');
        Schema::dropIfExists('stock_in_items');
        Schema::dropIfExists('stock_ins');
        Schema::dropIfExists('inventory_transactions');
        Schema::dropIfExists('inventories');
        Schema::dropIfExists('items');
        Schema::dropIfExists('warehouse_locations');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('units');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
