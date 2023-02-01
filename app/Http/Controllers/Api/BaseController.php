<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller as Controller;

/**
 * Class BaseController
 *
 * @package App\Http\Controllers\Api
 */
class BaseController extends Controller
{
    /**
     * @param $result
     * @param  int  $statusCode
     *
     * @return JsonResponse
     */
    public function sendResponse($result, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json($result, $statusCode);
    }

    /**
     * @param  string  $message
     * @param  array  $errors
     * @param  int  $statusCode
     *
     * @return JsonResponse
     */
    public function sendError(
        string $message,
        array $errors = [],
        int $statusCode = Response::HTTP_BAD_REQUEST
    ): JsonResponse {
        $response = [
            'message' => $message,
            'errors' => $errors,
        ];

        return response()->json($response, $statusCode);
    }
}
