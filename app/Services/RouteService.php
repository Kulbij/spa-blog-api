<?php

namespace App\Services;

use App\Handlers\RouteHandler;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

/**
 * Class RouteService
 *
 * @package App\Services
 */
class RouteService
{
    /**
     * @var RouteHandler
     */
    private RouteHandler $routeHandler;

    /**
     * RouteService constructor.
     *
     * @param  RouteHandler  $routeHandler
     */
    public function __construct(RouteHandler $routeHandler)
    {
        $this->routeHandler = $routeHandler;
    }

    /**
     * @param  int|null  $version
     *
     * @return array
     */
    public function availableApiRoutes(?int $version = null): array
    {
        return $this->routeHandler->getAvailableApiRoutesByVersion($version);
    }

    /**
     * @throws FileNotFoundException
     */
    public function getRoutesFiles(): array
    {
        return $this->routeHandler->getRoutesFiles();
    }

    /**
     * @param  array  $files
     * @param  array  $parameters
     *
     * @return array
     */
    public function parseRoutesFromFile(array $files, array $parameters = []): array
    {
        return $this->routeHandler->parseRoutesFromFile($files, $parameters);
    }
}
