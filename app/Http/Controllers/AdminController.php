<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Models\Appointment;
use App\Models\Order;
use App\Models\Product;
use App\Models\Staff;
use App\Models\Notification;
use App\Services\DashboardService;
use App\Services\StaffService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
        private readonly StaffService $staffService
    ) {}

    // GET /api/v1/admin/dashboard
    public function dashboard(): JsonResponse
    {
        $staff = Auth::guard('api_staff')->user();

        $data = $this->dashboardService->getStats($staff->role, $staff->branch_id);

        return $this->success($data, 'Dashboard data retrieved');
    }

    // GET /api/v1/admin/staff
    public function staffIndex(): JsonResponse
    {
        $user = Auth::guard('api_staff')->user();
        
        $staff = $this->staffService->list(
            $user->role === 'branch_manager' ? $user->branch_id : null
        );

        return $this->success($staff, 'Staff list retrieved');
    }

    // POST /api/v1/admin/staff
    public function staffStore(StoreStaffRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = Auth::guard('api_staff')->user();

        if ($user->role === 'branch_manager') {
            $data['branch_id'] = $user->branch_id;
            if (in_array($data['role'], ['owner', 'branch_manager'])) {
                $data['role'] = 'admin_klinik';
            }
        }

        $staff = $this->staffService->create($data);

        return $this->success($staff, 'Staff created', 201);
    }

    // PUT /api/v1/admin/staff/:id
    public function staffUpdate(UpdateStaffRequest $request, Staff $staff): JsonResponse
    {
        $data = $request->validated();
        $user = Auth::guard('api_staff')->user();

        if ($user->role === 'branch_manager') {
            $data['branch_id'] = $user->branch_id;
            if (isset($data['role']) && in_array($data['role'], ['owner', 'branch_manager'])) {
                unset($data['role']);
            }
        }

        $updatedStaff = $this->staffService->update($staff, $data);

        return $this->success($updatedStaff, 'Staff updated');
    }

    // DELETE /api/v1/admin/staff/:id (soft delete)
    public function staffDestroy(Staff $staff): JsonResponse
    {
        $staff->update(['is_active' => false]);
        $staff->delete();

        return $this->success(null, 'Staff deactivated', 204);
    }

    // GET /api/v1/admin/notifications
    public function notifications(): JsonResponse
    {
        $staff    = Auth::guard('api_staff')->user();
        $notifs   = Notification::where(function ($q) use ($staff) {
                            $q->where('staff_id', $staff->id)->orWhereNull('staff_id');
                        })
                        ->orderByDesc('created_at')
                        ->paginate(20);
        $unread   = Notification::where(function ($q) use ($staff) {
                        $q->where('staff_id', $staff->id)->orWhereNull('staff_id');
                    })->unread()->count();

        return $this->success(['notifications' => $notifs, 'unread_count' => $unread], 'Notifications retrieved');
    }

    // PUT /api/v1/admin/notifications/:id/read
    public function markRead(Notification $notification): JsonResponse
    {
        $notification->update(['is_read' => true]);

        return $this->success(null, 'Notification marked as read');
    }

    // GET /api/v1/admin/2fa/setup
    public function setup2FA(): JsonResponse
    {
        $staff = Auth::guard('api_staff')->user();
        $data = $this->staffService->generate2FASecret($staff);

        return $this->success($data, '2FA setup data generated');
    }

    // POST /api/v1/admin/2fa/toggle
    // PUT /api/v1/admin/settings
    public function updateSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'clinic_name'      => 'sometimes|string|max:100',
            'clinic_logo'      => 'sometimes|image|max:2048',
            'whatsapp_number'  => 'sometimes|string|max:20',
            'address'          => 'sometimes|string',
            'operational_hours' => 'sometimes|array',
        ]);

        $path = storage_path('app/settings.json');
        $settings = [];
        if (file_exists($path)) {
            $settings = json_decode(file_get_contents($path), true) ?? [];
        }

        if ($request->hasFile('clinic_logo')) {
            $filePath = $request->file('clinic_logo')->store('settings', 'public');
            $validated['clinic_logo_url'] = url('storage/' . $filePath);
        }

        $settings = array_merge($settings, $validated);
        file_put_contents($path, json_encode($settings, JSON_PRETTY_PRINT));

        return $this->success($settings, 'Settings saved successfully');
    }

    public function toggle2FA(Request $request): JsonResponse
    {
        $request->validate([
            'code'   => 'required|string|size:6',
            'enable' => 'required|boolean'
        ]);

        $staff = Auth::guard('api_staff')->user();
        $success = $this->staffService->toggle2FA($staff, $request->code, $request->enable);

        if ($success) {
            return $this->success(null, $request->enable ? '2FA Enabled' : '2FA Disabled');
        }

        return $this->error('Invalid verification code', 422);
    }
}
