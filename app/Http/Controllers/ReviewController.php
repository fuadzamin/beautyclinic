<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Product;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(private readonly ReviewService $reviewService) {}

    // GET /api/v1/products/{product}/reviews
    public function index(Product $product): JsonResponse
    {
        $reviews = $product->reviews()->with('user:id,name')->latest()->paginate(10);
        return $this->success($reviews, 'Reviews retrieved');
    }

    // POST /api/v1/reviews
    public function store(StoreReviewRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $review = $this->reviewService->createReview($data);

        return $this->success($review->load('user:id,name'), 'Review created', 201);
    }
}
