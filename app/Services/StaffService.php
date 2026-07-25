<?php

namespace App\Services;

use App\Models\Staff;
use Illuminate\Support\Facades\Hash;
use App\Helpers\ActivityLogger;

class StaffService
{
    public function list(?int $branchId = null)
    {
        return Staff::with('branch')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->withTrashed()
            ->orderBy('name')
            ->get();
    }

    public function create(array $data): Staff
    {
        $staff = Staff::create([
            ...$data,
            'password' => Hash::make($data['password']),
        ]);

        ActivityLogger::log('staff_created', 'Staff', $staff->id, ['email' => $staff->email]);

        return $staff;
    }

    public function update(Staff $staff, array $data): Staff
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $staff->update($data);
        
        ActivityLogger::log('staff_updated', 'Staff', $staff->id);

        return $staff->fresh();
    }

    public function generate2FASecret(Staff $staff): array
    {
        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        
        if (!$staff->two_fa_secret) {
            $staff->update([
                'two_fa_secret' => $google2fa->generateSecretKey()
            ]);
        }

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name', 'Beauty Clinic'),
            $staff->email,
            $staff->two_fa_secret
        );

        return [
            'secret' => $staff->two_fa_secret,
            'qr_url' => $qrCodeUrl
        ];
    }

    public function toggle2FA(Staff $staff, string $code, bool $enable): bool
    {
        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        $valid = $google2fa->verifyKey($staff->two_fa_secret, $code);

        if ($valid) {
            $staff->update(['two_fa_enabled' => $enable]);
            
            ActivityLogger::log(
                $enable ? '2fa_enabled' : '2fa_disabled',
                'Staff',
                $staff->id
            );
            
            return true;
        }

        return false;
    }
}
