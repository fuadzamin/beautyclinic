<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Branch;
use Illuminate\Http\JsonResponse;

class BranchController extends Controller
{
    public function index(): JsonResponse
    {
        $branches = Branch::where('is_active', true)->get();
        return response()->json([
            'success' => true,
            'data' => $branches
        ]);
    }
}
