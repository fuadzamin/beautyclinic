<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReceiptSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReceiptSettingController extends Controller
{
    /**
     * GET /api/v1/admin/receipt-settings
     * Get setting for the authenticated user's branch (or global if owner)
     */
    public function show(Request $request): JsonResponse
    {
        $branchId = $request->query('branch_id');
        $setting  = ReceiptSetting::forBranch($branchId ? (int) $branchId : null);

        return response()->json([
            'success'   => true,
            'data'      => $setting,
            'message'   => 'Receipt settings retrieved',
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * PUT /api/v1/admin/receipt-settings
     * Save or update settings for a branch
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'branch_id'             => 'nullable|exists:branches,id',
            'clinic_name'           => 'required|string|max:100',
            'tagline'               => 'nullable|string|max:200',
            'address'               => 'nullable|string|max:300',
            'phone'                 => 'nullable|string|max:30',
            'email'                 => 'nullable|email|max:100',
            'logo_url'              => 'nullable|string',
            'show_treatment'        => 'boolean',
            'show_products'         => 'boolean',
            'show_discount'         => 'boolean',
            'show_payment_method'   => 'boolean',
            'show_cashier_name'     => 'boolean',
            'show_appointment_date' => 'boolean',
            'footer_message'        => 'nullable|string|max:500',
            'social_instagram'      => 'nullable|string|max:100',
            'social_whatsapp'       => 'nullable|string|max:20',
            'website'               => 'nullable|string|max:100',
            'auto_print'            => 'boolean',
        ]);

        $branchId = $request->input('branch_id');

        $setting = ReceiptSetting::updateOrCreate(
            ['branch_id' => $branchId],
            $request->except('branch_id')
        );

        return response()->json([
            'success'   => true,
            'data'      => $setting->fresh(),
            'message'   => 'Receipt settings saved',
            'timestamp' => now()->toISOString(),
        ]);
    }
}
