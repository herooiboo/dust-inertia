<?php

namespace Dust\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/';

    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            foreach ($this->guards() as $config) {
                if ($config['path'] === 'module') {
                    $this->registerModuleRoutes($config['prefix'], $config['middleware'], $config['file_name']);
                } else {
                    $this->registerRootRoutes($config['prefix'], $config['middleware'], $config['file_name']);
                }
            }
        });
    }

    protected function configureRateLimiting(): void
    {
        foreach ($this->guards() as $guard => $config) {
            if ($config['rate_limit_max']) {
                RateLimiter::for($guard, function (Request $request) use ($config) {
                    return Limit::perMinute($config['rate_limit_max'])->by($request->user()?->id ?: $request->ip());
                });
            }
        }
    }

    protected function registerRootRoutes(string $prefix, string $middleware = null, string $routesFileName = null): void
    {
        if (!$middleware) {
            $middleware = $prefix;
        }

        if (!$routesFileName) {
            $routesFileName = $prefix;
        }

        $path = base_path("routes/$routesFileName.php");
        if (!file_exists($path)) {
            return;
        }

        Route::prefix($prefix)
            ->middleware($middleware)
            ->group($path);
    }

    protected function registerModuleRoutes(string $prefix, string $middleware = null, string $routesFileName = null): void
    {
        if (!$middleware) {
            $middleware = $prefix;
        }

        if (!$routesFileName) {
            $routesFileName = $prefix;
        }

        $registrar = Route::prefix($prefix)->middleware($middleware);

        $modulesPath = modules_path();
        $modules = array_filter(scandir($modulesPath), fn($module) => !in_array($module, ['.', '..']));
        foreach ($modules as $module) {
            $apiRoutes = implode(DIRECTORY_SEPARATOR, [$modulesPath, $module, 'Http', 'Routes', "$routesFileName.php"]);
            if (file_exists($apiRoutes)) {
                $registrar->group($apiRoutes);
            }
        }
    }

    protected function guards(): array
    {
        $default = [
            'api' => [
                'path' => 'module',
                'prefix' => 'api',
                'middleware' => 'api',
                'file_name' => 'api',
                'rate_limit_max' => 60,
            ],
            'test' => [
                'path' => 'root',
                'prefix' => 'test',
                'middleware' => 'test',
                'file_name' => 'test',
                'rate_limit_max' => 0,
            ],
        ];

        return array_merge_recursive($default, $this->extendGuards());
    }

    protected function extendGuards(): array
    {
        return [];
    }
}
