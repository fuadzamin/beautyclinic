<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Return a success JSON response.
     *
     * @param  mixed  $data
     * @param  string|null  $message
     * @param  int  $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success($data = null, string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'success'   => true,
            'data'      => $data,
            'message'   => $message,
            'timestamp' => now()->toISOString(),
        ], $code);
    }

    /**
     * Return an error JSON response.
     *
     * @param  string  $message
     * @param  int  $code
     * @param  mixed  $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error(string $message, int $code = 400, $data = null): JsonResponse
    {
        return response()->json([
            'success'   => false,
            'message'   => $message,
            'data'      => $data,
            'timestamp' => now()->toISOString(),
        ], $code);
    }
}
