<?php

namespace App\Handlers;

use Illuminate\Support\Str;
use App\Helpers\ArrayHelper;
use InvalidArgumentException;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use function str_replace;

/**
 * Class RouteParser
 *
 * @package App\Services
 */
class RouteHandler
{
    private const API_FOLDER = 'auto/api';
    private const API_SETTINGS_FOLDER = 'auto/settings';

    private const SETTINGS_FILE = 'settings.yaml';
    private const OVERRIDING_FILE = 'overriding.yaml';

    /**
     * @var string
     */
    private string $dir;

    /**
     * @var array|string[]
     */
    private array $enabledApiVersion;

    /**
     * @var string|null
     */
    private ?string $apiDomain;

    /**
     * RouteParser constructor.
     */
    public function __construct()
    {
        $this->dir = app()->basePath() . '/routes';
        $this->apiDomain = env('API_DOMAIN');
        $enabledApiVersion = env('API_ENABLED_VERSION');

        if (null === $this->apiDomain) {
            throw new InvalidArgumentException('API domain is invalid');
        }

        if (null === $enabledApiVersion) {
            throw new InvalidArgumentException('At least one api version must be active');
        }

        $this->enabledApiVersion = explode(',', $enabledApiVersion);
    }

    /**
     * @param  array  $files
     * @param  array  $parameters
     *
     * @return array
     */
    public function parseRoutesFromFile(array $files, array $parameters = []): array
    {
        if ($this->dir) {
            $files = array_map(function ($filename) {
                return "{$this->dir}/{$filename}";
            }, $files);
        }

        $resultRoutes = [];
        foreach ($files as $file) {
            $contents = Yaml::parseFile($file);

            $paths = $this->preparePath($contents['group']['paths'], $parameters);
            $contents['group']['paths'] = $paths['endpoints'];
            $contents['group']['middleware'] = array_merge($contents['group']['middleware'] ?? [],
                $paths['middlewares']);

            $contents = Yaml::dump($contents);

            foreach ($parameters as $key => $val) {
                $searchKey = str_replace('.methods', '', $key);
                $contents = str_replace('$' . $searchKey . '$', $val, $contents);
            }

            $resultRoutes[] = Yaml::parse($contents);
        }

        return $resultRoutes;
    }

    /**
     * @return array
     * @throws FileNotFoundException
     */
    public function getRoutesFiles(): array
    {
        $routes = [];
        foreach ($this->enabledApiVersion as $version) {
            $routes[] = $this->getRoutesByVersion($version);
        }

        return $routes;
    }

    /**
     * @param  int|null  $version
     *
     * @return array
     */
    public function getAvailableApiRoutesByVersion(?int $version): array
    {
        $routes = $this->getAllAvailableRoutes();

        if (null === $version) {
            return $routes;
        }

        $selectedVersion = "v$version";

        if (!in_array($selectedVersion, $this->enabledApiVersion, true)) {
            throw new NotFoundHttpException("Routes of version '{$version}' not found");
        }

        return $routes[$selectedVersion];
    }

    /**
     * @return array
     */
    private function getAllAvailableRoutes(): array
    {
        $allRoutes = Route::getRoutes();

        $routes = [];
        foreach ($allRoutes->getRoutesByName() as $key => $route) {
            foreach ($this->enabledApiVersion as $apiVersion) {
                if (str_contains($key, "api.{$apiVersion}")) {
                    $routeName = Str::camel(str_replace(["api.{$apiVersion}.", '.'], ['', '_'], $key));

                    $routes[][$apiVersion][$routeName] = str_replace("api/{$apiVersion}", '', $route->uri);
                }
            }
        }

        return array_merge_recursive(...$routes);
    }

    /**
     * @throws FileNotFoundException
     */
    private function getRoutesByVersion(string $version): array
    {
        $companyId = auth()->user()->company_id ?? '';

        $files = $this->getFiles($version);

        $parameters = [
            'api.domain' => $this->apiDomain,
            'api.version' => $version,
        ];

        $parameters = array_merge($parameters, $this->prepareParameters($files['settings']['default']));

        if (null !== $files['overriding']) {
            $globalOverriding = $files['overriding']['overriding']['global'] ?? [];
            $companyOverriding = $files['overriding']['overriding'][$companyId] ?? [];

            $overriding = array_merge($globalOverriding, $companyOverriding);

            if (!empty($overriding)) {
                $overridingParameters = $this->prepareParameters($overriding);
                $parameters = array_merge($parameters, $overridingParameters);
            }
        }

        return [
            'files' => $files['routes'],
            'parameters' => $parameters,
        ];
    }

    /**
     * @throws FileNotFoundException
     */
    private function getFiles(string $version): array
    {
        try {
            $overriding = Yaml::parse(Storage::disk('routes')
                ->get(self::API_SETTINGS_FOLDER . '/' . $version . '/' . self::OVERRIDING_FILE));
        } catch (FileNotFoundException $exception) {
            $overriding = null;
        }

        return [
            'routes' => Storage::disk('routes')
                ->allFiles(self::API_FOLDER . '/' . $version),
            'settings' => Yaml::parse(Storage::disk('routes')
                ->get(self::API_SETTINGS_FOLDER . '/' . $version . '/' . self::SETTINGS_FILE)),
            'overriding' => $overriding,
        ];
    }

    /**
     * @param  array  $parameters
     *
     * @return array
     */
    private function prepareParameters(array $parameters): array
    {
        $result = [];
        foreach ($parameters as $key => $parameter) {
            $result[] = ArrayHelper::flatten($parameter, $key);
        }

        return array_merge_recursive(...$result);
    }

    /**
     * @param  array  $paths
     * @param  array  $parameters
     *
     * @return array
     */
    private function preparePath(array $paths, array $parameters): array
    {
        if (empty($paths)) {
            return [];
        }

        $newPaths = [];

        $isUserAuth = null !== auth()->user();
        if (!app()->environment('testing')) {
            foreach ($parameters as $key => $parameter) {
                if (str_ends_with($key, '.auth')) {
                    $searchKey = str_replace('.methods', '', $key);
                    $url = '$' . str_replace('.auth', '.url', $searchKey) . '$';

                    if ($parameter && !$isUserAuth) {
                        unset($paths[$url]);
                    }

                    if (!$parameter && $isUserAuth) {
                        unset($paths[$url]);
                    }
                }

                if (str_contains($key, '.disable') && $parameter) {
                    $url = '$' . str_replace('.disable', '.url', $key) . '$';

                    unset($paths[$url]);
                }
            }
        }

        $newPaths['middlewares'] = [];
        $newPaths['endpoints'] = [];
        foreach ($paths as $key => $path) {
            if (!str_contains($key, '.auth')) {
                $searchKey = str_replace('$', '', $key);
                $parentKey = strtok($searchKey, '.');
                $searchKey = str_replace($parentKey, $parentKey . '.methods', $searchKey);

                $newKey = str_replace($key, $parameters[$searchKey], $key);

                preg_match('/^([A-Za-z]+\.[A-Za-z]+)*/', $searchKey, $pathMiddlewareKey);

                $pathMiddlewareKey = array_unique(array_filter($pathMiddlewareKey));
                $pathMiddlewareKey = $pathMiddlewareKey[0] ?? '';
                $groupKey = strtok($pathMiddlewareKey, '.');

                $parameters = collect($parameters);

                $pathMiddlewares = $parameters->filter(function ($value, $key) use ($pathMiddlewareKey) {
                    return preg_match("/{$pathMiddlewareKey}.middleware.[0-9]/", $key);
                })->values()->all();

                $globalMiddlewares = $parameters->filter(function ($value, $key) use ($groupKey) {
                    return preg_match("/{$groupKey}.middleware.[0-9]/", $key);
                })->values()->all();

                $path[array_key_first($path)]['middleware'] =
                    array_unique(
                        array_filter(
                            array_merge($path[array_key_first($path)]['middleware'] ?? [], $pathMiddlewares)
                        )
                    );

                $newPaths['middlewares'] = $globalMiddlewares;
                $newPaths['endpoints'][$newKey][] = $path;
            }
        }

        foreach ($newPaths['endpoints'] as $key => $endpoint) {
            $newPaths['endpoints'][$key] = array_merge_recursive(...$endpoint);
        }

        return $newPaths;
    }
}
