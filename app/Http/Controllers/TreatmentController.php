<?php

namespace App\Http\Controllers;

use App\Models\Treatment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TreatmentController extends Controller
{
    // GET /api/v1/treatments
    public function index(Request $request): JsonResponse
    {
        $query = Treatment::with('branches');

        if (!$request->boolean('show_all')) {
            $query->active();
        }

        $treatments = $query
            ->when($request->category, fn ($q) => $q->where('category', $request->category))
            ->when($request->branch_id, function ($q) use ($request) {
                $q->whereHas('branches', function ($q2) use ($request) {
                    $q2->where('branches.id', $request->branch_id);
                });
            })
            ->orderBy('name')
            ->get();

        return response()->json([
            'success'   => true,
            'data'      => $treatments,
            'message'   => 'Treatments retrieved',
            'timestamp' => now()->toISOString(),
        ]);
    }

    // GET /api/v1/treatments/:id
    public function show(Treatment $treatment): JsonResponse
    {
        return response()->json([
            'success'   => true,
            'data'      => $treatment->load('branches'),
            'message'   => 'Treatment retrieved',
            'timestamp' => now()->toISOString(),
        ]);
    }

    // POST /api/v1/admin/treatments (admin only)
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'benefits'         => 'nullable|string',
            'category'         => 'nullable|string|max:100',
            'price'            => 'required|integer|min:0',
            'duration_minutes' => 'required|integer|min:15',
            'image_url'        => 'nullable|url',
            'is_active'        => 'boolean',
        ]);

        $treatment = Treatment::create($request->except('branch_ids'));

        if ($request->has('branch_ids')) {
            $treatment->branches()->sync($request->array('branch_ids'));
        }

        return response()->json([
            'success'   => true,
            'data'      => $treatment->load('branches'),
            'message'   => 'Treatment created',
            'timestamp' => now()->toISOString(),
        ], 201);
    }

    // PUT /api/v1/admin/treatments/:id (admin only)
    public function update(Request $request, Treatment $treatment): JsonResponse
    {
        $request->validate([
            'name'             => 'sometimes|string|max:255',
            'price'            => 'sometimes|integer|min:0',
            'duration_minutes' => 'sometimes|integer|min:15',
            'is_active'        => 'sometimes|boolean',
        ]);

        $treatment->update($request->except('branch_ids'));

        if ($request->has('branch_ids')) {
            $treatment->branches()->sync($request->array('branch_ids'));
        }

        return response()->json([
            'success'   => true,
            'data'      => $treatment->fresh('branches'),
            'message'   => 'Treatment updated',
            'timestamp' => now()->toISOString(),
        ]);
    }

    // DELETE /api/v1/admin/treatments/:id
    public function destroy(Treatment $treatment): JsonResponse
    {
        $treatment->update(['is_active' => false]);

        return response()->json([
            'success'   => true,
            'message'   => 'Treatment deactivated',
            'timestamp' => now()->toISOString(),
        ], 204);
    }
}
