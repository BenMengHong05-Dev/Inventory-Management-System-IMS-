<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Dashboard'); ?> - <?php echo e(t('app_name')); ?></title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Google Sans"', '"Plus Jakarta Sans"', '"Noto Sans Khmer"', '"Kantumruy Pro"', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            500: '#0284c7',
                            600: '#0369a1',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Bootstrap Icons (Fallback) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- JsBarcode & QRCode.js -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Google Fonts: Google Sans + Noto Sans Khmer -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Noto+Sans+Khmer:wght@400;500;600;700&family=Kantumruy+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        *, html, body, input, select, textarea, button, p, span, a, label, h1, h2, h3, h4, h5, h6, table, th, td {
            font-family: 'Google Sans', 'Plus Jakarta Sans', 'Noto Sans Khmer', 'Kantumruy Pro', system-ui, sans-serif !important;
        }
        :lang(km), [lang="km"] {
            font-family: 'Noto Sans Khmer', 'Kantumruy Pro', 'Google Sans', sans-serif !important;
            line-height: 1.6;
        }
        body {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background-color: #f6f8fb;
            background-image: 
                radial-gradient(at 0% 0%, rgba(224, 242, 254, 0.45) 0px, transparent 45%),
                radial-gradient(at 100% 0%, rgba(243, 232, 255, 0.35) 0px, transparent 40%),
                radial-gradient(at 50% 100%, rgba(224, 231, 255, 0.3) 0px, transparent 50%);
            background-attachment: fixed;
            color: #1e293b;
        }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* ── Lucide Icon Standards ──────────────────────── */
        [data-lucide], svg.lucide {
            display: inline-block;
            vertical-align: middle;
            width: 1.15rem;
            height: 1.15rem;
            stroke-width: 2;
            flex-shrink: 0;
        }

        /* ── Dark Rounded Sidebar (Simple, Rounded & Modern) ── */
        aside.dark-sidebar {
            background: #080d1a; /* Deep night-slate background */
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }
        .sidebar-item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 10px 14px; border-radius: 16px;
            font-size: 0.83rem; font-weight: 500; color: #94a3b8;
            transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer; text-decoration: none; white-space: nowrap;
            position: relative;
        }
        .sidebar-item:hover {
            background: rgba(255, 255, 255, 0.05);
            color: #f8fafc;
            transform: translateX(2px);
        }
        .sidebar-item.active {
            background: linear-gradient(135deg, rgba(14, 40, 68, 0.95), rgba(12, 74, 110, 0.45));
            color: #38bdf8 !important;
            font-weight: 700;
            border: 1px solid rgba(56, 189, 248, 0.45);
            box-shadow: none !important;
        }
        .sidebar-item.active .sidebar-icon {
            color: #38bdf8 !important;
        }
        .sidebar-icon {
            width: 20px; height: 20px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; color: #64748b; transition: color 0.18s ease;
        }
        .sidebar-item:hover .sidebar-icon { color: #e2e8f0; }
        
        /* Rounded Submenu Inset Container */
        .sidebar-submenu {
            background: #0a1122;
            border-radius: 18px;
            padding: 6px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            margin: 4px 2px 6px 2px;
            box-shadow: none !important;
        }
        .sidebar-sub-item {
            display: flex; align-items: center; gap: 8px;
            padding: 8px 12px; border-radius: 12px;
            font-size: 0.79rem; font-weight: 500; color: #8e9bb0;
            transition: all 0.18s ease; text-decoration: none; position: relative;
        }
        .sidebar-sub-item:hover {
            background: rgba(255, 255, 255, 0.06);
            color: #f8fafc;
            transform: translateX(2px);
        }
        .sidebar-sub-item.active {
            background: #17243c;
            color: #ffffff !important;
            font-weight: 600;
            border: 1px solid rgba(255,255,255,0.06);
        }

        /* ── Modern Clear Glass Cards (Crystal Glass Aesthetic) ── */
        .card, .glass-card {
            background: rgba(255, 255, 255, 0.82) !important;
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.95) !important;
            outline: 1px solid rgba(226, 232, 240, 0.7);
            box-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.04) !important;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* ── Data Tables (Modern Clear Glass) ────────── */
        .data-table { 
            width: 100%; 
            text-align: left; 
            border-collapse: separate; 
            border-spacing: 0; 
        }
        .data-table th {
            padding: 12px 18px;
            font-size: 0.72rem; 
            font-weight: 700; 
            color: #64748b;
            text-transform: uppercase; 
            letter-spacing: 0.05em;
            background: rgba(248, 250, 252, 0.78);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            white-space: nowrap;
        }
        .data-table th:first-child { 
            border-top-left-radius: 20px; 
            padding-left: 22px;
        }
        .data-table th:last-child { 
            border-top-right-radius: 20px; 
            padding-right: 22px;
        }
        .data-table td {
            padding: 13px 18px; 
            font-size: 0.82rem; 
            color: #334155;
            border-bottom: 1px solid rgba(241, 245, 249, 0.85);
            vertical-align: middle;
            background: transparent;
        }
        .data-table td:first-child { padding-left: 22px; }
        .data-table td:last-child { padding-right: 22px; }
        .data-table tbody tr { 
            transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1); 
            background: transparent; 
        }
        .data-table tbody tr:hover { 
            background: rgba(255, 255, 255, 0.95); 
        }
        .data-table tbody tr:last-child td { 
            border-bottom: none; 
        }

        /* ── Modern Glass Action Buttons ─────────── */
        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 10px;
            border: 1px solid transparent;
            font-size: 0.85rem;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
            text-decoration: none;
            box-shadow: none !important;
        }
        .btn-action-view {
            background: rgba(241, 245, 249, 0.8);
            color: #64748b;
            border-color: rgba(226, 232, 240, 0.8);
        }
        .btn-action-view:hover {
            background: #ffffff;
            color: #0284c7;
            border-color: #bae6fd;
            transform: translateY(-1px);
        }
        .btn-action-edit {
            background: rgba(238, 242, 255, 0.8);
            color: #4f46e5;
            border-color: rgba(224, 231, 255, 0.9);
        }
        .btn-action-edit:hover {
            background: #eef2ff;
            color: #4338ca;
            border-color: #c7d2fe;
            transform: translateY(-1px);
        }
        .btn-action-delete {
            background: rgba(254, 242, 242, 0.85);
            color: #ef4444;
            border-color: rgba(254, 226, 226, 0.9);
        }
        .btn-action-delete:hover {
            background: #fee2e2;
            color: #dc2626;
            border-color: #fca5a5;
            transform: translateY(-1px);
        }

        /* ── Forms (Small, Rounded & Crisp - No Shadows) ─── */
        .form-label {
            display: block; font-size: 0.74rem; font-weight: 700;
            color: #475569; margin-bottom: 4px;
        }
        .form-label.required::after { content: " *"; color: #ef4444; }
        .form-input {
            width: 100%; border-radius: 14px;
            border: 1px solid #cbd5e1;
            background: #fff;
            padding: 8px 14px; font-size: 0.82rem; color: #1e293b;
            transition: all 0.18s ease;
            outline: none; box-shadow: none !important;
        }
        .form-input:focus {
            border-color: #0284c7;
            box-shadow: none !important;
        }
        textarea.form-input { resize: vertical; min-height: 70px; }
        select, .form-select, select.form-input {
            appearance: none; -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 12px center; background-repeat: no-repeat;
            background-size: 14px; padding-right: 32px !important; cursor: pointer;
        }
        select:focus, .form-select:focus { border-color: #0284c7; box-shadow: none !important; outline: none; }

        /* ── Buttons (Pill & Rounded Modern - Flat) ───────── */
        .btn-primary, .btn-secondary, .btn-danger, .btn-dark {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            padding: 8px 18px; font-size: 0.78rem; font-weight: 700;
            border-radius: 9999px;
            border: none; cursor: pointer; white-space: nowrap;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1); user-select: none;
            box-shadow: none !important;
        }
        .btn-primary { background: #0284c7; color: #fff; }
        .btn-primary:hover { background: #0369a1; transform: translateY(-1px); }
        .btn-primary:active { transform: translateY(0); opacity: 0.92; }

        .btn-dark { background: #0f172a; color: #fff; }
        .btn-dark:hover { background: #1e293b; transform: translateY(-1px); }
        
        .btn-secondary { background: #fff; color: #334155; border: 1px solid #cbd5e1; }
        .btn-secondary:hover { background: #f8fafc; border-color: #94a3b8; color: #0f172a; transform: translateY(-1px); }
        
        .btn-danger { background: #ef4444; color: #fff; }
        .btn-danger:hover { background: #dc2626; transform: translateY(-1px); }
        
        /* ── Global Shadow Elimination Across All Pages ──── */
        *, ::before, ::after {
            --tw-shadow: 0 0 #0000 !important;
            --tw-shadow-colored: 0 0 #0000 !important;
        }
        .card, .shadow, .shadow-xs, .shadow-sm, .shadow-md, .shadow-lg, .shadow-xl, .shadow-2xl,
        aside, header, button, .sidebar-item, .sidebar-submenu, .btn-primary, .btn-secondary, .btn-danger, .btn-dark,
        .form-input, .form-select, select, input, textarea, div, span, table {
            box-shadow: none !important;
        }
        .card:hover, .btn-primary:hover, .btn-secondary:hover, .btn-danger:hover, .sidebar-item:hover, .sidebar-item.active {
            box-shadow: none !important;
        }
        
        .btn-icon {
            display: inline-flex; align-items: center; justify-content: center;
            width: 32px; height: 32px; border-radius: 10px; border: none; cursor: pointer;
            font-size: 0.85rem; color: #64748b; background: transparent;
            transition: all 0.18s ease;
        }
        .btn-icon:hover { background: #f1f5f9; color: #0f172a; }

        /* ── Status Dots ────────────────────────────────── */
        .status-dot-completed { display: inline-flex; align-items: center; gap: 6px; font-weight: 600; font-size: 0.74rem; color: #10b981; }
        .status-dot-completed::before { content: ""; width: 7px; height: 7px; border-radius: 50%; background: #10b981; }
        
        .status-dot-pending { display: inline-flex; align-items: center; gap: 6px; font-weight: 600; font-size: 0.74rem; color: #ef4444; }
        .status-dot-pending::before { content: ""; width: 7px; height: 7px; border-radius: 50%; background: #ef4444; }

        /* ── Ultra-Smooth Dialog & Modal Animations ─────────────── */
        @keyframes modalBackdropIn {
            0% { opacity: 0; backdrop-filter: blur(0px); -webkit-backdrop-filter: blur(0px); }
            100% { opacity: 1; backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }
        }
        @keyframes modalBackdropOut {
            0% { opacity: 1; backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }
            100% { opacity: 0; backdrop-filter: blur(0px); -webkit-backdrop-filter: blur(0px); }
        }
        @keyframes modalCardIn {
            0% { 
                opacity: 0; 
                transform: scale(0.91) translateY(20px); 
                filter: blur(4px);
            }
            70% {
                transform: scale(1.008) translateY(-2px);
                filter: blur(0px);
            }
            100% { 
                opacity: 1; 
                transform: scale(1) translateY(0); 
                filter: blur(0px);
            }
        }
        @keyframes modalCardOut {
            0% { opacity: 1; transform: scale(1) translateY(0); }
            100% { opacity: 0; transform: scale(0.93) translateY(14px); }
        }

        .smooth-dialog,
        div[id$="_modal"]:not(.hidden),
        .modal-overlay:not(.hidden),
        .modal-backdrop:not(.hidden) {
            animation: modalBackdropIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards !important;
        }

        .smooth-dialog > div,
        div[id$="_modal"]:not(.hidden) > div,
        .modal-overlay:not(.hidden) > div,
        .modal-backdrop:not(.hidden) > div,
        .modal-box {
            animation: modalCardIn 0.34s cubic-bezier(0.16, 1, 0.3, 1) forwards !important;
            transform-origin: center center;
            will-change: transform, opacity;
        }

        .modal-closing {
            animation: modalBackdropOut 0.22s cubic-bezier(0.16, 1, 0.3, 1) forwards !important;
        }
        .modal-closing > div,
        .modal-closing .modal-box {
            animation: modalCardOut 0.22s cubic-bezier(0.16, 1, 0.3, 1) forwards !important;
        }

        [x-cloak] { display: none !important; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body class="text-slate-800 antialiased flex h-screen overflow-hidden" x-data="{ sidebarOpen: true }">

    <!-- 1. DARK ROUNDED MODERN SIDEBAR (Matching User Screenshot) -->
    <aside :class="sidebarOpen ? 'w-64' : 'w-[72px]'" 
           class="dark-sidebar flex flex-col transition-all duration-300 z-30 no-print flex-shrink-0">
        
        <!-- Brand Header (Blue Logo Icon + Title) -->
        <div class="h-16 flex items-center justify-between px-4 border-b border-white/5 flex-shrink-0">
            <a href="<?php echo e(route('dashboard')); ?>" class="flex items-center gap-3 overflow-hidden min-w-0">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-white flex-shrink-0 bg-gradient-to-tr from-sky-400 via-blue-600 to-indigo-600">
                    <i data-lucide="package" class="w-5 h-5"></i>
                </div>
                <div x-show="sidebarOpen" class="overflow-hidden min-w-0">
                    <div class="font-extrabold text-white text-[15px] leading-tight tracking-tight">IMS System</div>
                    <div class="text-[11px] text-slate-400 font-medium"><?php echo e(app()->getLocale() == 'km' ? 'ប្រព័ន្ធគ្រប់គ្រងស្តុក' : 'Inventory System'); ?></div>
                </div>
            </a>
            <button @click="sidebarOpen = !sidebarOpen" 
                    class="w-7 h-7 flex items-center justify-center rounded-lg border border-slate-700/70 text-slate-400 hover:text-white hover:border-slate-500 transition flex-shrink-0"
                    title="Toggle Sidebar">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="18" x="3" y="3" rx="2"/>
                    <path d="M15 3v18"/>
                </svg>
            </button>
        </div>

        <!-- Navigation Links Stack (Simple, Sleek, Rounded & Consolidated) -->
        <nav class="flex-1 overflow-y-auto py-3 px-3 space-y-1.5 custom-scrollbar">

            <!-- 1. DASHBOARD -->
            <a href="<?php echo e(route('dashboard')); ?>" 
               class="sidebar-item <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>"
               title="<?php echo e(t('dashboard')); ?>">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="sidebar-icon">
                        <i data-lucide="layout-dashboard"></i>
                    </div>
                    <span x-show="sidebarOpen" class="truncate font-medium"><?php echo e(t('dashboard')); ?></span>
                </div>
            </a>

            <!-- 2. INVENTORY & STOCK -->
            <div x-data="{ open: <?php echo e(request()->routeIs('items.*') || request()->routeIs('inventory.*') || request()->routeIs('stock-in.*') || request()->routeIs('stock_in.*') || request()->routeIs('stock-out.*') || request()->routeIs('stock_out.*') || request()->routeIs('stock-transfers.*') || request()->routeIs('stock_transfers.*') || request()->routeIs('stock-adjustments.*') || request()->routeIs('stock_adjustments.*') ? 'true' : 'false'); ?> }">
                <button @click="open = !open" 
                        class="sidebar-item w-full justify-between <?php echo e(request()->routeIs('items.*') || request()->routeIs('inventory.*') || request()->routeIs('stock-in.*') || request()->routeIs('stock-out.*') || request()->routeIs('stock-transfers.*') || request()->routeIs('stock-adjustments.*') ? 'text-sky-400 font-bold' : ''); ?>"
                        title="<?php echo e(t('inventory')); ?>">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="sidebar-icon">
                            <i data-lucide="boxes"></i>
                        </div>
                        <span x-show="sidebarOpen" class="truncate text-left font-medium"><?php echo e(t('inventory')); ?></span>
                    </div>
                    <span x-show="sidebarOpen" class="text-slate-500 transition-transform duration-200 inline-flex items-center justify-center flex-shrink-0" :class="open ? 'rotate-180' : ''">
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </span>
                </button>
                <div x-show="open && sidebarOpen" x-collapse class="sidebar-submenu space-y-0.5">
                    <a href="<?php echo e(route('items.index')); ?>" class="sidebar-sub-item <?php echo e(request()->routeIs('items.*') || request()->routeIs('inventory.index') ? 'active' : ''); ?>">
                        <span class="text-slate-500">•</span>
                        <span><?php echo e(app()->getLocale() == 'km' ? 'ទំនិញ និងស្តុកបច្ចុប្បន្ន' : 'Items & Current Stock'); ?></span>
                    </a>
                    <a href="<?php echo e(route('stock-in.index')); ?>" class="sidebar-sub-item <?php echo e(request()->routeIs('stock-in.*') || request()->routeIs('stock_in.*') ? 'active' : ''); ?>">
                        <span class="text-slate-500">•</span>
                        <span><?php echo e(app()->getLocale() == 'km' ? 'ការនាំទំនិញចូល (Stock In)' : 'Stock In'); ?></span>
                    </a>
                    <a href="<?php echo e(route('stock-out.index')); ?>" class="sidebar-sub-item <?php echo e(request()->routeIs('stock-out.*') || request()->routeIs('stock_out.*') ? 'active' : ''); ?>">
                        <span class="text-slate-500">•</span>
                        <span><?php echo e(app()->getLocale() == 'km' ? 'ការដកទំនិញចេញ (Stock Out)' : 'Stock Out'); ?></span>
                    </a>
                    <a href="<?php echo e(route('stock-transfers.index')); ?>" class="sidebar-sub-item <?php echo e(request()->routeIs('stock-transfers.*') || request()->routeIs('stock-adjustments.*') ? 'active' : ''); ?>">
                        <span class="text-slate-500">•</span>
                        <span><?php echo e(app()->getLocale() == 'km' ? 'ផ្ទេរ និងកែតម្រូវស្តុក' : 'Transfers & Adjustments'); ?></span>
                    </a>
                    <a href="<?php echo e(route('inventory.low-stock')); ?>" class="sidebar-sub-item <?php echo e(request()->routeIs('inventory.low*') ? 'active' : ''); ?>">
                        <span class="text-slate-500">•</span>
                        <span class="flex items-center justify-between w-full">
                            <span><?php echo e(t('low_stock')); ?></span>
                            <span class="w-2 h-2 rounded-full bg-amber-400 flex-shrink-0"></span>
                        </span>
                    </a>
                </div>
            </div>

            <!-- 3. PHYSICAL ASSETS -->
            <div x-data="{ open: <?php echo e(request()->routeIs('assets.*') || request()->routeIs('maintenance.*') ? 'true' : 'false'); ?> }">
                <button @click="open = !open" 
                        class="sidebar-item w-full justify-between <?php echo e(request()->routeIs('assets.*') || request()->routeIs('maintenance.*') ? 'text-sky-400 font-bold' : ''); ?>"
                        title="<?php echo e(t('assets')); ?>">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="sidebar-icon">
                            <i data-lucide="laptop"></i>
                        </div>
                        <span x-show="sidebarOpen" class="truncate text-left font-medium"><?php echo e(t('assets')); ?></span>
                    </div>
                    <span x-show="sidebarOpen" class="text-slate-500 transition-transform duration-200 inline-flex items-center justify-center flex-shrink-0" :class="open ? 'rotate-180' : ''">
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </span>
                </button>
                <div x-show="open && sidebarOpen" x-collapse class="sidebar-submenu space-y-0.5">
                    <a href="<?php echo e(route('assets.index')); ?>" class="sidebar-sub-item <?php echo e(request()->routeIs('assets.index') ? 'active' : ''); ?>">
                        <span class="text-slate-500">•</span>
                        <span><?php echo e(t('asset_list')); ?></span>
                    </a>
                    <a href="<?php echo e(route('maintenance.index')); ?>" class="sidebar-sub-item <?php echo e(request()->routeIs('maintenance.*') || request()->routeIs('assets.warranty') ? 'active' : ''); ?>">
                        <span class="text-slate-500">•</span>
                        <span><?php echo e(app()->getLocale() == 'km' ? 'ការថែទាំ & ការធានា' : 'Maintenance & Warranty'); ?></span>
                    </a>
                </div>
            </div>

            <!-- 4. PURCHASING -->
            <div x-data="{ open: <?php echo e(request()->routeIs('purchase-requests.*') || request()->routeIs('purchase-orders.*') || request()->routeIs('goods-receipts.*') ? 'true' : 'false'); ?> }">
                <button @click="open = !open" 
                        class="sidebar-item w-full justify-between <?php echo e(request()->routeIs('purchase-requests.*') || request()->routeIs('purchase-orders.*') || request()->routeIs('goods-receipts.*') ? 'text-sky-400 font-bold' : ''); ?>"
                        title="<?php echo e(t('purchasing')); ?>">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="sidebar-icon">
                            <i data-lucide="shopping-bag"></i>
                        </div>
                        <span x-show="sidebarOpen" class="truncate text-left font-medium"><?php echo e(t('purchasing')); ?></span>
                    </div>
                    <span x-show="sidebarOpen" class="text-slate-500 transition-transform duration-200 inline-flex items-center justify-center flex-shrink-0" :class="open ? 'rotate-180' : ''">
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </span>
                </button>
                <div x-show="open && sidebarOpen" x-collapse class="sidebar-submenu space-y-0.5">
                    <a href="<?php echo e(route('purchase-requests.index')); ?>" class="sidebar-sub-item <?php echo e(request()->routeIs('purchase-requests.*') || request()->routeIs('purchase-orders.*') ? 'active' : ''); ?>">
                        <span class="text-slate-500">•</span>
                        <span><?php echo e(app()->getLocale() == 'km' ? 'សំណើទិញ & បញ្ជាទិញ' : 'Requisitions & POs'); ?></span>
                    </a>
                    <a href="<?php echo e(route('goods-receipts.index')); ?>" class="sidebar-sub-item <?php echo e(request()->routeIs('goods-receipts.*') ? 'active' : ''); ?>">
                        <span class="text-slate-500">•</span>
                        <span><?php echo e(t('goods_receiving')); ?></span>
                    </a>
                </div>
            </div>

            <!-- 5. REPORTS & STATS -->
            <a href="<?php echo e(route('reports.index')); ?>" 
               class="sidebar-item <?php echo e(request()->routeIs('reports.*') ? 'active' : ''); ?>"
               title="<?php echo e(t('reports')); ?>">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="sidebar-icon">
                        <i data-lucide="bar-chart-3"></i>
                    </div>
                    <span x-show="sidebarOpen" class="truncate font-medium"><?php echo e(t('reports')); ?></span>
                </div>
            </a>

            <!-- 6. MASTER DATA & SYSTEM ADMINISTRATION (Combined Cleanly) -->
            <div x-data="{ open: <?php echo e(request()->is('categories*') || request()->is('brands*') || request()->is('units*') || request()->is('suppliers*') || request()->is('warehouses*') || request()->is('locations*') || request()->is('departments*') || request()->is('employees*') || request()->routeIs('users.*') || request()->routeIs('roles.*') || request()->routeIs('audit-logs.*') ? 'true' : 'false'); ?> }">
                <button @click="open = !open" 
                        class="sidebar-item w-full justify-between <?php echo e(request()->is('categories*') || request()->is('brands*') || request()->is('units*') || request()->is('suppliers*') || request()->is('warehouses*') || request()->routeIs('users.*') || request()->routeIs('roles.*') || request()->routeIs('audit-logs.*') ? 'text-sky-400 font-bold' : ''); ?>"
                        title="<?php echo e(app()->getLocale() == 'km' ? 'ការកំណត់ & ប្រព័ន្ធ' : 'Settings & System'); ?>">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="sidebar-icon">
                            <i data-lucide="settings"></i>
                        </div>
                        <span x-show="sidebarOpen" class="truncate text-left font-medium"><?php echo e(app()->getLocale() == 'km' ? 'ការកំណត់ & ទិន្នន័យ' : 'Settings & Data'); ?></span>
                    </div>
                    <span x-show="sidebarOpen" class="text-slate-500 transition-transform duration-200 inline-flex items-center justify-center flex-shrink-0" :class="open ? 'rotate-180' : ''">
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </span>
                </button>
                <div x-show="open && sidebarOpen" x-collapse class="sidebar-submenu space-y-0.5">
                    <a href="<?php echo e(route('categories.index')); ?>" class="sidebar-sub-item <?php echo e(request()->is('categories*') || request()->is('brands*') || request()->is('units*') ? 'active' : ''); ?>">
                        <span class="text-slate-500">•</span>
                        <span><?php echo e(app()->getLocale() == 'km' ? 'ទិន្នន័យគោល (ប្រភេទ/ឯកតា)' : 'Master Data (Categories/Units)'); ?></span>
                    </a>
                    <a href="<?php echo e(route('warehouses.index')); ?>" class="sidebar-sub-item <?php echo e(request()->is('warehouses*') || request()->is('locations*') || request()->is('suppliers*') ? 'active' : ''); ?>">
                        <span class="text-slate-500">•</span>
                        <span><?php echo e(app()->getLocale() == 'km' ? 'ឃ្លាំង & អ្នកផ្គត់ផ្គង់' : 'Warehouses & Suppliers'); ?></span>
                    </a>
                    <a href="<?php echo e(route('users.index')); ?>" class="sidebar-sub-item <?php echo e(request()->routeIs('users.*') || request()->routeIs('roles.*') ? 'active' : ''); ?>">
                        <span class="text-slate-500">•</span>
                        <span><?php echo e(app()->getLocale() == 'km' ? 'អ្នកប្រើប្រាស់ & សិទ្ធិ' : 'Users & Permissions'); ?></span>
                    </a>
                    <a href="<?php echo e(route('audit-logs.index')); ?>" class="sidebar-sub-item <?php echo e(request()->routeIs('audit-logs.*') ? 'active' : ''); ?>">
                        <span class="text-slate-500">•</span>
                        <span><?php echo e(app()->getLocale() == 'km' ? 'កំណត់ហេតុប្រព័ន្ធ (Audit)' : 'System Audit Trail'); ?></span>
                    </a>
                </div>
            </div>

            <!-- Divider -->
            <div class="my-2 mx-1 border-t border-white/5"></div>

            <!-- 7. UNIFIED COMBINED TRASH & RECYCLE BIN HUB -->
            <a href="<?php echo e(route('disposals.index')); ?>" 
               class="sidebar-item <?php echo e(request()->routeIs('disposals.*') || request()->routeIs('assets.disposals.*') ? 'active' : ''); ?>"
               title="<?php echo e(app()->getLocale() == 'km' ? 'ធុងសំរាម (Recycle Bin & Disposals)' : 'Recycle Bin & Disposals'); ?>">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="sidebar-icon text-rose-400">
                        <i data-lucide="trash-2"></i>
                    </div>
                    <span x-show="sidebarOpen" class="truncate font-medium text-slate-300"><?php echo e(app()->getLocale() == 'km' ? 'ធុងសំរាម & កម្ទេច' : 'Disposals & Trash'); ?></span>
                </div>
                <span x-show="sidebarOpen" class="px-2 py-0.5 rounded-full text-[10px] bg-rose-500/15 text-rose-300 font-bold border border-rose-500/25">
                    <?php echo e(app()->getLocale() == 'km' ? 'កម្ទេច' : 'Trash'); ?>

                </span>
            </a>

        </nav>

        <!-- User Footer Card (Matching User Screenshot) -->
        <div class="p-3 border-t border-white/5 flex-shrink-0 bg-[#070c18]">
            <!-- Collapsed view -->
            <div x-show="!sidebarOpen" class="flex flex-col items-center gap-2">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-sm text-white bg-gradient-to-tr from-blue-600 to-indigo-500">
                    <?php echo e(mb_substr(session('user_name', 'S'), 0, 1)); ?>

                </div>
                <form action="<?php echo e(route('logout')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="w-7 h-7 flex items-center justify-center text-slate-400 hover:text-white rounded-lg transition" title="<?php echo e(t('logout')); ?>">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
            <!-- Expanded view -->
            <div x-show="sidebarOpen" class="p-2.5 rounded-2xl bg-[#0d1629] border border-white/5 flex items-center justify-between gap-2.5">
                <div class="flex items-center gap-2.5 overflow-hidden">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-sm text-white flex-shrink-0 bg-gradient-to-tr from-blue-600 to-indigo-500">
                        <?php echo e(mb_substr(session('user_name', 'S'), 0, 1)); ?>

                    </div>
                    <div class="overflow-hidden min-w-0 leading-tight">
                        <p class="text-xs font-bold text-white truncate"><?php echo e(session('user_name', 'System Administrator')); ?></p>
                        <p class="text-[10px] text-slate-400 font-medium truncate"><?php echo e(session('user_email', 'admin@inventory.com')); ?></p>
                    </div>
                </div>
                <form action="<?php echo e(route('logout')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="w-7 h-7 flex items-center justify-center text-slate-400 hover:text-white rounded-lg transition flex-shrink-0" title="<?php echo e(t('logout')); ?>">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- 2. MAIN APPLICATION CONTENT WRAPPER -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <!-- TOP NAVBAR (Clean, Rounded & Compact - Flat) -->
        <header class="h-16 bg-white border-b border-slate-200/80 flex items-center justify-between px-6 z-20 no-print flex-shrink-0">
            
            <!-- Global Search Form -->
            <form action="<?php echo e(route('global.search')); ?>" method="GET" class="w-80 md:w-96">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </span>
                    <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="<?php echo e(app()->getLocale() == 'km' ? 'ស្វែងរកទំនិញ, ទ្រព្យ, បាកូដ, SKU...' : 'Search items, assets, barcodes, orders, SKUs...'); ?>"
                           class="w-full pl-10 pr-4 py-2 bg-slate-100/90 border border-slate-200/60 rounded-full text-xs focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none transition-all">
                </div>
            </form>

            <div class="flex items-center gap-3">
                <!-- Barcode Scan Shortcut Modal Button -->
                <button @click="$dispatch('open-scan-modal')" 
                        class="p-2 text-slate-500 hover:text-sky-600 hover:bg-slate-100 rounded-full transition" 
                        title="<?php echo e(app()->getLocale() == 'km' ? 'ស្កេនបាកូដ / QR' : 'Scan Barcode / QR'); ?>">
                    <i data-lucide="scan-line" class="w-4 h-4"></i>
                </button>

                <!-- Language Switcher with Country Flag Icons -->
                <?php if (isset($component)) { $__componentOriginal8d3bff7d7383a45350f7495fc470d934 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d3bff7d7383a45350f7495fc470d934 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.language-switcher','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('language-switcher'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8d3bff7d7383a45350f7495fc470d934)): ?>
<?php $attributes = $__attributesOriginal8d3bff7d7383a45350f7495fc470d934; ?>
<?php unset($__attributesOriginal8d3bff7d7383a45350f7495fc470d934); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8d3bff7d7383a45350f7495fc470d934)): ?>
<?php $component = $__componentOriginal8d3bff7d7383a45350f7495fc470d934; ?>
<?php unset($__componentOriginal8d3bff7d7383a45350f7495fc470d934); ?>
<?php endif; ?>

                <!-- Notification Bell Dropdown -->
                <div x-data="{ open: false, unreadCount: 0, items: [] }"
                     x-init="fetch('<?php echo e(route('notifications.unread')); ?>').then(r => r.json()).then(d => { unreadCount = d.unread_count || 0; items = d.notifications || [] })"
                     class="relative">
                    <button @click="open = !open" class="relative p-2 text-slate-500 hover:text-sky-600 hover:bg-slate-100 rounded-full transition">
                        <i data-lucide="bell" class="w-4 h-4"></i>
                        <span x-show="unreadCount > 0" x-text="unreadCount" class="absolute top-1 right-1 w-4 h-4 bg-rose-500 text-white rounded-full text-[9px] flex items-center justify-center font-bold"></span>
                    </button>
                    <div x-show="open" @click.outside="open = false" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                         class="absolute right-0 mt-2 w-80 bg-white rounded-3xl border border-slate-200 py-3 z-50">
                        <div class="px-4 py-2 border-b border-slate-100 flex items-center justify-between">
                            <span class="font-bold text-xs text-slate-800"><?php echo e(t('notifications')); ?></span>
                            <a href="<?php echo e(route('notifications.index')); ?>" class="text-[11px] text-sky-600 hover:underline font-semibold"><?php echo e(t('view_all')); ?></a>
                        </div>
                        <div class="divide-y divide-slate-50 max-h-64 overflow-y-auto">
                            <template x-for="n in items" :key="n.id">
                                <div class="px-4 py-2.5 hover:bg-slate-50 text-xs transition">
                                    <p class="font-bold text-slate-800" x-text="n.title"></p>
                                    <p class="text-slate-500 mt-0.5 text-[11px]" x-text="n.message"></p>
                                </div>
                            </template>
                            <div x-show="items.length === 0" class="px-4 py-6 text-center text-xs text-slate-400">
                                <?php echo e(t('no_notifications')); ?>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Profile Dropdown Pill -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center gap-2 focus:outline-none p-1 rounded-full hover:bg-slate-100 transition">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-sky-500 to-blue-600 text-white flex items-center justify-center font-black text-xs">
                            <?php echo e(mb_substr(session('user_name', 'S'), 0, 1)); ?>

                        </div>
                    </button>
                    <div x-show="open" @click.outside="open = false" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-2xl border border-slate-200 py-1.5 text-xs z-50">
                        <div class="px-4 py-2 border-b border-slate-100">
                            <p class="font-bold text-slate-800 text-xs"><?php echo e(session('user_name', 'System Administrator')); ?></p>
                            <p class="text-[11px] text-slate-400 truncate"><?php echo e(session('user_email', 'admin@inventory.com')); ?></p>
                        </div>
                        <a href="<?php echo e(route('profile')); ?>" class="flex items-center gap-2 px-4 py-2 text-slate-700 hover:bg-slate-50 font-medium transition">
                            <i data-lucide="user" class="w-3.5 h-3.5 text-slate-400"></i>
                            <?php echo e(t('profile')); ?>

                        </a>
                        <form action="<?php echo e(route('logout')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="w-full flex items-center gap-2 text-left px-4 py-2 text-rose-600 hover:bg-rose-50 font-semibold transition">
                                <i data-lucide="log-out" class="w-3.5 h-3.5 text-rose-500"></i>
                                <?php echo e(t('logout')); ?>

                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- MAIN SCROLLABLE CONTENT AREA -->
        <main class="flex-1 overflow-y-auto p-6">
            <!-- Flash Message Alerts -->
            <?php if(session('success')): ?>
                <div class="mb-4 flex items-center p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold rounded-2xl" role="alert">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 mr-2.5 flex-shrink-0"></i>
                    <span><?php echo e(session('success')); ?></span>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="mb-4 flex items-center p-3.5 bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold rounded-2xl" role="alert">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 mr-2.5 flex-shrink-0"></i>
                    <span><?php echo e(session('error')); ?></span>
                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="mb-4 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-medium" role="alert">
                    <div class="flex items-center mb-1 font-bold">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-600 mr-2 flex-shrink-0"></i>
                        <span>Please correct the errors below:</span>
                    </div>
                    <ul class="list-disc list-inside pl-6 space-y-0.5 text-xs text-rose-700">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($err); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- View Content Injection -->
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>

    <!-- SCANNER MODAL (Smooth Animation & Rounded) -->
    <div x-data="{ open: false, scanCode: '' }"
         @open-scan-modal.window="open = true; setTimeout(() => $refs.scanInput.focus(), 150)"
         x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 backdrop-blur-none"
         x-transition:enter-end="opacity-100 backdrop-blur-md"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 backdrop-blur-md"
         x-transition:leave-end="opacity-0 backdrop-blur-none"
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-md p-4"
         x-cloak>
        <div @click.outside="open = false" 
             x-transition:enter="transition cubic-bezier(0.16, 1, 0.3, 1) duration-300"
             x-transition:enter-start="opacity-0 scale-90 translate-y-6"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-90 translate-y-6"
             class="bg-white rounded-3xl max-w-md w-full p-6 border border-slate-100 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="scan-line" class="w-5 h-5 text-sky-600"></i>
                    Scan Barcode / QR Code
                </h3>
                <button @click="open = false" class="p-1 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <form action="<?php echo e(route('scan.lookup')); ?>" method="GET" class="space-y-4">
                <p class="text-xs text-slate-500">Scan an item or asset barcode/QR code with a handheld scanner or enter the code manually:</p>
                <input x-ref="scanInput" type="text" name="code" x-model="scanCode" placeholder="Scan or type code here..."
                       class="form-input font-mono text-sm">
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="open = false" class="btn-secondary text-xs"><?php echo e(t('cancel')); ?></button>
                    <button type="submit" class="btn-primary text-xs">Find Record</button>
                </div>
            </form>
        </div>
    </div>

    <!-- QUICK UNIFIED TRASH & RECYCLE BIN DIALOG (Ultra-Smooth Animation & Rounded) -->
    <div x-data="{ open: false }"
         @open-trash-modal.window="open = true; $nextTick(() => refreshLucide())"
         x-show="open" 
         x-transition:enter="transition cubic-bezier(0.16, 1, 0.3, 1) duration-300"
         x-transition:enter-start="opacity-0 backdrop-blur-none"
         x-transition:enter-end="opacity-100 backdrop-blur-md"
         x-transition:leave="transition cubic-bezier(0.16, 1, 0.3, 1) duration-200"
         x-transition:leave-start="opacity-100 backdrop-blur-md"
         x-transition:leave-end="opacity-0 backdrop-blur-none"
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-md p-4"
         x-cloak>
        <div @click.outside="open = false" 
             x-transition:enter="transition cubic-bezier(0.16, 1, 0.3, 1) duration-300"
             x-transition:enter-start="opacity-0 scale-90 translate-y-6"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition cubic-bezier(0.16, 1, 0.3, 1) duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-90 translate-y-6"
             class="bg-white rounded-3xl max-w-lg w-full p-6 border border-slate-100 space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3.5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0 border border-rose-100">
                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 leading-tight">ធុងសំរាម & ការបោះបង់ (Trash & Recycle Bin)</h3>
                        <p class="text-[11px] text-slate-500 font-medium">គ្រប់គ្រងទិន្នន័យដែលបានលុប និងការកម្ទេចទ្រព្យសម្បត្តិ</p>
                    </div>
                </div>
                <button @click="open = false" class="p-1.5 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-2 gap-3 text-left">
                <a href="<?php echo e(route('disposals.index')); ?>" 
                   class="p-4 rounded-2xl bg-slate-50 hover:bg-rose-50/70 border border-slate-200/80 hover:border-rose-200 transition group flex flex-col justify-between">
                    <div class="w-9 h-9 rounded-xl bg-white text-rose-500 flex items-center justify-center mb-3 group-hover:scale-110 transition">
                        <i data-lucide="archive-restore" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-800 group-hover:text-rose-700 block">ទ្រព្យសម្បត្តិបោះបង់</span>
                        <span class="text-[10px] text-slate-400">មើលបញ្ជីកម្ទេច & បោះបង់</span>
                    </div>
                </a>
                <a href="<?php echo e(route('disposals.create')); ?>" 
                   class="p-4 rounded-2xl bg-slate-50 hover:bg-sky-50/70 border border-slate-200/80 hover:border-sky-200 transition group flex flex-col justify-between">
                    <div class="w-9 h-9 rounded-xl bg-white text-sky-500 flex items-center justify-center mb-3 group-hover:scale-110 transition">
                        <i data-lucide="plus-circle" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-800 group-hover:text-sky-700 block">កត់ត្រាការបោះបង់ថ្មី</span>
                        <span class="text-[10px] text-slate-400">បោះបង់ទ្រព្យសម្បត្តិខូច</span>
                    </div>
                </a>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2.5 text-slate-600">
                    <i data-lucide="shield-alert" class="w-4 h-4 text-amber-500 flex-shrink-0"></i>
                    <span class="text-[11px] font-medium">កំណត់ហេតុលុបទិន្នន័យត្រូវបានកត់ត្រាទុកក្នុង Audit Logs</span>
                </div>
                <a href="<?php echo e(route('audit-logs.index')); ?>" class="text-[11px] font-bold text-sky-600 hover:text-sky-700 whitespace-nowrap">
                    មើល Logs →
                </a>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                <button type="button" @click="open = false" class="btn-secondary text-xs">បិទ (Close)</button>
                <a href="<?php echo e(route('disposals.index')); ?>" class="btn-primary text-xs bg-rose-600 hover:bg-rose-700">
                    <i data-lucide="external-link" class="w-3.5 h-3.5 mr-1"></i> ទៅកាន់ធុងសំរាមពេញលេញ
                </a>
            </div>
        </div>
    </div>

    <!-- GLOBAL LUCIDE & MODAL ANIMATION ENGINE -->
    <script>
        function refreshLucide() {
            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }
        }
        document.addEventListener('DOMContentLoaded', refreshLucide);
        document.addEventListener('alpine:initialized', refreshLucide);

        let lucideDebounce = null;
        const lucideObserver = new MutationObserver(() => {
            clearTimeout(lucideDebounce);
            lucideDebounce = setTimeout(refreshLucide, 20);
        });
        lucideObserver.observe(document.body, { childList: true, subtree: true });

        // Universal Smooth Modal Handlers
        window.openModal = function(id) {
            const modal = typeof id === 'string' ? document.getElementById(id) : id;
            if (!modal) return;
            modal.classList.remove('modal-closing');
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            refreshLucide();
            setTimeout(() => {
                const input = modal.querySelector('input:not([type=hidden]), select, textarea');
                if (input) input.focus();
            }, 60);
        };

        window.closeModal = function(id) {
            const modal = typeof id === 'string' ? document.getElementById(id) : id;
            if (!modal) return;
            modal.classList.add('modal-closing');
            setTimeout(() => {
                modal.classList.remove('modal-closing');
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }, 200);
        };

        document.addEventListener('click', (e) => {
            if (e.target && e.target.matches && e.target.matches('div[id$="_modal"]:not(.hidden), .modal-overlay:not(.hidden)')) {
                window.closeModal(e.target);
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const activeModals = document.querySelectorAll('div[id$="_modal"]:not(.hidden), .modal-overlay:not(.hidden)');
                activeModals.forEach(m => window.closeModal(m));
            }
        });
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\invertory_managament_system\resources\views/layouts/app.blade.php ENDPATH**/ ?>