<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Http\Requests\UpdateAppointmentStatusRequest;
use App\Http\Requests\CancelAppointmentRequest;
use App\Http\Requests\GetAvailableSlotsRequest;
use App\Models\Appointment;
use App\Models\Treatment;
use App\Services\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function __construct(private readonly AppointmentService $appointmentService) {}

    // GET /api/v1/appointments/available-slots
    public function availableSlots(GetAvailableSlotsRequest $request): JsonResponse
    {
        $slots = $this->appointmentService->getAvailableSlots(
            $request->integer('branch_id'),
            $request->integer('treatment_id'),
            $request->string('date')
        );

        return $this->success($slots, 'Available slots retrieved');
    }

    // POST /api/v1/appointments
    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $appointment = $this->appointmentService->book($request->validated());

        return $this->success($appointment->load('treatment'), 'Appointment booked successfully', 201);
    }

    // GET /api/v1/appointments/:id
    public function show(Appointment $appointment): JsonResponse
    {
        return $this->success($appointment->load(['treatment', 'staff', 'user']), 'Appointment retrieved');
    }

    // GET /api/v1/user/appointments (customer's own appointments)
    public function myAppointments(): JsonResponse
    {
        $appointments = auth()->user()->appointments()
            ->with(['treatment', 'branch'])
            ->orderBy('appointment_date', 'desc')
            ->get();

        return $this->success($appointments, 'Appointments retrieved');
    }

    public function myLoyaltyPoints(): JsonResponse
    {
        $points = auth()->user()->loyaltyPoints()
            ->orderBy('id', 'desc')
            ->get();

        return $this->success($points, 'Loyalty points history retrieved');
    }

    // PUT /api/v1/appointments/:id (reschedule / add notes)
    public function update(UpdateAppointmentRequest $request, Appointment $appointment): JsonResponse
    {
        $appointment->update($request->validated());

        return $this->success($appointment->fresh('treatment'), 'Appointment updated');
    }

    // DELETE /api/v1/appointments/:id (cancel)
    public function destroy(CancelAppointmentRequest $request, Appointment $appointment): JsonResponse
    {
        $this->appointmentService->cancel($appointment, $request->reason);

        return $this->success(null, 'Appointment cancelled');
    }

    // ──────── Admin endpoints ────────

    public function adminIndex(Request $request): JsonResponse
    {
        $staff = Auth::guard('api_staff')->user();

        $appointments = $this->appointmentService->listAdmin(
            $request->all(),
            $staff?->branch_id
        );

        return $this->success($appointments, 'Appointments retrieved');
    }

    // PUT /api/v1/admin/appointments/:id/status
    public function updateStatus(UpdateAppointmentStatusRequest $request, Appointment $appointment): JsonResponse
    {
        $appointment->update($request->validated());

        return $this->success($appointment->fresh(), 'Status updated');
    }
}
