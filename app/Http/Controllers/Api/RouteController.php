<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\RouteService;
use Illuminate\Http\JsonResponse;

/**
 * Class RouteController
 *
 * @package App\Http\Controllers\Api
 */
class RouteController extends BaseController
{
    /**
     * @var RouteService
     */
    private RouteService $routeService;

    /**
     * RouteController constructor.
     *
     * @param  RouteService  $routeService
     */
    public function __construct(RouteService $routeService)
    {
        $this->routeService = $routeService;
    }

    /**
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $routes = $this->routeService->availableApiRoutes($request->query('ver'));

        return $this->sendResponse($routes);
    }
}
