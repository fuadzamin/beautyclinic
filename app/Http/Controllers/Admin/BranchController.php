<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index(): JsonResponse
    {
        $branches = Branch::orderBy('name')->get();

        return response()->json([
            'success'   => true,
            'data'      => $branches,
            'message'   => 'Branches retrieved',
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'address'   => 'required|string',
            'phone'     => 'required|string|max:20',
            'is_active' => 'boolean',
        ]);

        $branch = Branch::create($validated);

        return response()->json([
            'success'   => true,
            'data'      => $branch,
            'message'   => 'Branch created successfully',
        ], 201);
    }

    public function update(Request $request, Branch $branch): JsonResponse
    {
        $validated = $request->validate([
            'name'      => 'sometimes|string|max:255',
            'address'   => 'sometimes|string',
            'phone'     => 'sometimes|string|max:20',
            'is_active' => 'boolean',
        ]);

        $branch->update($validated);

        return response()->json([
            'success'   => true,
            'data'      => $branch->fresh(),
            'message'   => 'Branch updated successfully',
        ]);
    }

    public function destroy(Branch $branch): JsonResponse
    {
        $branch->update(['is_active' => false]);
        $branch->delete();

        return response()->json([
            'success'   => true,
            'message'   => 'Branch archived successfully',
        ], 204);
    }
}
