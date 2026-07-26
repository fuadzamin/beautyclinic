<?php

namespace App\Services;

use App\Models\LoyaltyPoint;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class LoyaltyPointService
{
    public function redeemPoints(User $user, int $pointsToRedeem): bool
    {
        if ($user->loyalty_points < $pointsToRedeem) {
            throw ValidationException::withMessages([
                'points' => 'Insufficient loyalty points.',
            ]);
        }

        $user->loyalty_points -= $pointsToRedeem;
        $user->save();

        LoyaltyPoint::create([
            'user_id'       => $user->id,
            'points_earned' => -$pointsToRedeem, // Negative for redemption
            'total_points'  => $user->loyalty_points,
            'source'        => 'point_redemption',
            'source_id'     => null, // No specific source ID for redemption
        ]);

        return true;
    }
}

