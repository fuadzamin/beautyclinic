<?php

namespace App\Http\Controllers;

use App\Http\Requests\RedeemPointsRequest;
use App\Services\LoyaltyPointService;
use Illuminate\Http\JsonResponse;

class LoyaltyController extends Controller
{
    public function __construct(private readonly LoyaltyPointService $loyaltyService) {}

    // POST /api/v1/loyalty/redeem
    public function redeem(RedeemPointsRequest $request): JsonResponse
    {
        $this->loyaltyService->redeemPoints(auth()->user(), $request->integer('points'));

        return $this->success(null, 'Points redeemed successfully');
    }
}
