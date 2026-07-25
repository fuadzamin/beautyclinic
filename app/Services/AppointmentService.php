<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Notification;
use App\Models\Treatment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AppointmentService
{
    private const CLINIC_OPEN  = '09:00';
    private const CLINIC_CLOSE = '18:00';

    public function getAvailableSlots(int $branchId, int $treatmentId, string $date): array
    {
        $treatment = Treatment::findOrFail($treatmentId);
        $duration  = $treatment->duration_minutes;

        $booked = Appointment::where('branch_id', $branchId)
            ->whereDate('appointment_date', $date)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->pluck('appointment_date')
            ->map(fn ($dt) => Carbon::parse($dt)->format('H:i'))
            ->toArray();

        $slots      = [];
        $slotStart  = strtotime("{$date} " . self::CLINIC_OPEN);
        $clinicEnd  = strtotime("{$date} " . self::CLINIC_CLOSE);

        for ($time = $slotStart; $time < $clinicEnd; $time += $duration * 60) {
            $slotTime = date('H:i', $time);

            // Skip past slots for today
            if (Carbon::parse("{$date} {$slotTime}")->isPast()) {
                continue;
            }

            if (!in_array($slotTime, $booked)) {
                $slots[] = [
                    'time'      => $slotTime,
                    'datetime'  => "{$date} {$slotTime}",
                    'available' => true,
                ];
            }
        }

        return $slots;
    }

    public function book(array $data): Appointment
    {
        $data['user_id'] = Auth::guard('api')->id();

        $appointment = Appointment::create($data);

        // Create notification for admin_klinik
        Notification::create([
            'staff_id' => null, // broadcast to all
            'title'    => 'New Appointment',
            'message'  => "New appointment from {$appointment->customer_name} for " .
                          Carbon::parse($appointment->appointment_date)->format('d M Y H:i'),
            'type'     => 'appointment',
        ]);

        return $appointment;
    }

    /**
     * Get appointments for admin with filtering
     */
    public function listAdmin(array $filters, ?int $branchId = null)
    {
        return Appointment::with(['treatment', 'staff', 'user', 'branch', 'transaction'])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->when($filters['status'] ?? null, fn ($q) => $q->where('status', $filters['status']))
            ->when($filters['date'] ?? null, fn ($q) => $q->whereDate('appointment_date', $filters['date']))
            ->when($filters['treatment_id'] ?? null, fn ($q) => $q->where('treatment_id', $filters['treatment_id']))
            ->when($filters['search'] ?? null, function ($q) use ($filters) {
                $q->where(function ($sub) use ($filters) {
                    $sub->where('customer_name', 'like', "%{$filters['search']}%")
                        ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$filters['search']}%"));
                });
            })
            ->orderByDesc('appointment_date')
            ->paginate($filters['per_page'] ?? 20);
    }

    /**
     * Cancel an appointment
     */
    public function cancel(Appointment $appointment, ?string $reason = null): bool
    {
        return $appointment->update([
            'status'              => 'cancelled',
            'cancelled_at'        => now(),
            'cancellation_reason' => $reason ?? 'Cancelled by system',
        ]);
    }
}
