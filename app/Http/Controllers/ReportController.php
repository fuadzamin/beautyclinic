<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $reportService) {}

    /**
     * GET /api/v1/admin/reports/sales
     */
    public function sales(Request $request): JsonResponse
    {
        $staff = Auth::guard('api_staff')->user();
        $filters = $request->all();

        // Enforce branch manager restriction
        if ($staff->role === 'branch_manager') {
            $filters['branch_id'] = $staff->branch_id;
        }

        $data = $this->reportService->getSalesReport($filters);

        return $this->success($data, 'Sales report retrieved');
    }

    /**
     * GET /api/v1/admin/reports/top-performing
     */
    public function topPerforming(Request $request): JsonResponse
    {
        $staff = Auth::guard('api_staff')->user();
        $filters = $request->all();

        if ($staff->role === 'branch_manager') {
            $filters['branch_id'] = $staff->branch_id;
        }

        $data = $this->reportService->getTopPerforming($filters);

        return $this->success($data, 'Top performing items retrieved');
    }

    /**
     * GET /api/v1/admin/reports/demographics
     */
    public function demographics(Request $request): JsonResponse
    {
        $staff = Auth::guard('api_staff')->user();
        $filters = $request->all();

        if ($staff->role === 'branch_manager') {
            $filters['branch_id'] = $staff->branch_id;
        }

        $data = $this->reportService->getDemographics($filters);

        return $this->success($data, 'Customer demographics retrieved');
    }
}
