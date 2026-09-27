<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Standardized JSON success response.
     */
    protected function jsonSuccess($data = null, string $message = 'Operation successful', $pagination = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => null,
            'pagination' => $pagination
        ], $code);
    }

    /**
     * Standardized JSON error response.
     */
    protected function jsonError(string $message = 'Operation failed', $errors = null, int $code = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => is_array($errors) ? $errors : ($errors ? ['error' => [$errors]] : null)
        ], $code);
    }
}
