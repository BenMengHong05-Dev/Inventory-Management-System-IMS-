<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarcodeQrController extends Controller
{
    /**
     * Print barcode labels for items.
     */
    public function printItemLabels(Request $request)
    {
        $itemIds = $request->get('item_ids', []);
        if (empty($itemIds) && $request->has('item_id')) {
            $itemIds = [$request->get('item_id')];
        }

        if (empty($itemIds)) {
            return back()->with('error', 'Please select at least one item to print labels.');
        }

        $placeholders = implode(',', array_fill(0, count($itemIds), '?'));
        $items = DB::select("
            SELECT id, code, code as sku, barcode, name_en, name_km, name_en as name, 
                   default_unit_cost, default_unit_cost as cost_price, default_unit_cost as selling_price
            FROM items
            WHERE id IN ({$placeholders}) AND deleted_at IS NULL
        ", $itemIds);

        return view('labels.item_labels', compact('items'));
    }

    /**
     * Print QR code labels for assets.
     */
    public function printAssetLabels(Request $request)
    {
        $assetIds = $request->get('asset_ids', []);
        if (empty($assetIds) && $request->has('asset_id')) {
            $assetIds = [$request->get('asset_id')];
        }

        if (empty($assetIds)) {
            return back()->with('error', 'Please select at least one asset to print labels.');
        }

        $placeholders = implode(',', array_fill(0, count($assetIds), '?'));
        $assets = DB::select("
            SELECT id, asset_code, asset_code as asset_tag, asset_name, asset_name as name, 
                   serial_number, barcode, qr_code
            FROM assets
            WHERE id IN ({$placeholders}) AND deleted_at IS NULL
        ", $assetIds);

        return view('labels.asset_labels', compact('assets'));
    }

    /**
     * Scan lookup resolver: redirects to Item or Asset detail.
     */
    public function scanLookup(Request $request)
    {
        $code = trim($request->get('code'));
        if (empty($code)) {
            return back()->with('error', 'Please provide a barcode or QR code.');
        }

        // 1. Check Items by barcode or code
        $item = DB::select("
            SELECT id FROM items
            WHERE (barcode = ? OR code = ?) AND deleted_at IS NULL
            LIMIT 1
        ", [$code, $code]);

        if (!empty($item)) {
            return redirect()->route('items.show', $item[0]->id);
        }

        // 2. Check Assets by asset_code, barcode, serial_number, or qr_code
        $asset = DB::select("
            SELECT id FROM assets
            WHERE (asset_code = ? OR barcode = ? OR serial_number = ? OR qr_code = ?) AND deleted_at IS NULL
            LIMIT 1
        ", [$code, $code, $code, $code]);

        if (!empty($asset)) {
            return redirect()->route('assets.show', $asset[0]->id);
        }

        return back()->with('error', "No item or asset found matching code: {$code}");
    }
}
