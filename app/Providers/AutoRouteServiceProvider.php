<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Eyf\Autoroute\Autoroute;
use App\Services\RouteService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

/**
 * Class AutoRouteServiceProvider
 *
 * @package App\Providers
 */
class AutoRouteServiceProvider extends ServiceProvider
{
    /**
     * @param  RouteService  $routeService
     * @param  Autoroute  $autoRoute
     *
     * @throws FileNotFoundException
     */
    public function boot(RouteService $routeService, Autoroute $autoRoute): void
    {
        $this->configureRateLimiting();

        $routesData = $routeService->getRoutesFiles();

        foreach ($routesData as $data) {
            $resultRoutes = $routeService->parseRoutesFromFile($data['files'], $data['parameters']);

            foreach ($resultRoutes as $route) {
                $autoRoute->create($route);
            }
        }
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for(
            'api',
            function (Request $request) {
                return Limit::perMinute(config('const.routes.rate_limiting.maxAttempts'))
                    ->by(optional($request->user())->id ?: $request->ip())
                ;
            }
        );
    }
}
