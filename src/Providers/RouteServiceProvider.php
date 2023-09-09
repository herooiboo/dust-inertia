<?php

namespace Dust\Providers;

use ReflectionClass;
use ReflectionException;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Dust\Http\Router\Enum\RoutePath;
use Illuminate\Support\Facades\Route;
use Dust\Http\Router\Attributes\Guard;
use Dust\Http\Router\Attributes\Prefix;
use Illuminate\Cache\RateLimiting\Limit;
use Dust\Http\Router\Attributes\Middleware;
use Illuminate\Support\Facades\RateLimiter;
use Dust\Http\Router\Enum\Router as RouterEnum;
use Dust\Http\Exceptions\RouteDefinitionMissing;
use Dust\Http\Router\Attributes\Route as RouteAttribute;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/';

    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            foreach ($this->guards() as $guard => $config) {
                if ($config['routes']['type'] === RouterEnum::File) {
                    if ($config['routes']['path'] === RoutePath::Module) {
                        $this->registerModuleRoutes($config['prefix'], $config['middleware'], $config['file_name']);
                    } else {
                        $this->registerRootRoutes($config['prefix'], $config['middleware'], $config['file_name']);
                    }
                } else {
                    $this->registerAttributeRoutes($config);
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
        if (! $middleware) {
            $middleware = $prefix;
        }

        if (! $routesFileName) {
            $routesFileName = $prefix;
        }

        $path = base_path("routes/$routesFileName.php");
        if (! file_exists($path)) {
            return;
        }

        Route::prefix($prefix)
            ->middleware($middleware)
            ->group($path);
    }

    protected function registerModuleRoutes(string $prefix, string $middleware = null, string $routesFileName = null): void
    {
        if (! $middleware) {
            $middleware = $prefix;
        }

        if (! $routesFileName) {
            $routesFileName = $prefix;
        }

        $registrar = Route::prefix($prefix)->middleware($middleware);

        foreach (config('nebula.paths') as $path) {
            $modulesPath = app_path($path);
            if (! file_exists($modulesPath)) {
                return;
            }
            $modules = array_filter(scandir($modulesPath), fn ($module) => ! in_array($module, ['.', '..']));
            foreach ($modules as $module) {
                $routes = implode(DIRECTORY_SEPARATOR, [$modulesPath, $module, 'Http', 'Routes', "$routesFileName.php"]);
                if (file_exists($routes)) {
                    $registrar->group($routes);
                }
            }
        }
    }

    protected function guards(): array
    {
        $default = [
            'api' => [
                'routes' => [
                    'type' => RouterEnum::Attribute,
                    'path' => RoutePath::None, // should be none for route type Router::Attribute
                    'file_name' => null, // should be null for route type Router::Attribute
                ],
                'prefix' => 'api',
                'middleware' => 'api',
                'rate_limit_max' => 60,
            ],
            'test' => [
                'routes' => [
                    'type' => RouterEnum::File,
                    'path' => RoutePath::Root, // should not be none for route type Router::File
                    'file_name' => 'test', // should not be null for route type Router::File
                ],
                'prefix' => 'test',
                'middleware' => 'test',
                'rate_limit_max' => 0,
            ],
        ];

        return array_merge_recursive($default, $this->extendGuards());
    }

    protected function extendGuards(): array
    {
        return [];
    }

    /**
     * @throws ReflectionException
     * @throws RouteDefinitionMissing
     */
    protected function registerAttributeRoutes(array $config): void
    {
        foreach (config('nebula.modules.paths') as $path) {
            $modulesPath = app_path($path);
            if (! file_exists($modulesPath)) {
                return;
            }
            $modules = array_filter(scandir($modulesPath), fn ($module) => ! in_array($module, ['.', '..']));
            foreach ($modules as $module) {
                $controllersPath = implode(DIRECTORY_SEPARATOR, [$modulesPath, $module, 'Http', 'Controllers']);
                $controllers = $this->getFiles($controllersPath);
                foreach ($controllers as $controller) {
                    $this->registerControllerRoute(str_replace('.php', '', $controller), $config['prefix'], $config['middleware']);
                }
            }
        }
    }

    /**
     * @throws ReflectionException
     * @throws RouteDefinitionMissing
     */
    private function registerControllerRoute(string $controller, string $prefix, string $middleware): void
    {
        $reflectionClass = new ReflectionClass($controller);
        $action = $reflectionClass->getName();
        $method = null;
        $route = null;
        $name = null;

        $attributes = $reflectionClass->getAttributes();
        $routeMiddleware = [];

        foreach ($attributes as $attribute) {
            switch ($attribute) {
                case Guard::class:
                    $guard = $attribute->getArguments()['name'];
                    if ($guard !== $middleware) {
                        return;
                    }
                    break;
                case Prefix::class:
                    $subPrefix = $attribute->getArguments()['value'];
                    $prefix = ! empty($prefix) ? "$prefix/$subPrefix" : $subPrefix;
                    break;
                case Middleware::class:
                    $routeMiddleware = $attribute->getArguments()['list'];
                    break;
                case RouteAttribute::class:
                ['method' => $method, 'uri' => $route, 'name' => $name] = $attribute->getArguments();
            }
        }

        if (! $route || ! $method) {
            throw new RouteDefinitionMissing();
        }

        Route::prefix($prefix)
            ->middleware([$middleware, ...$routeMiddleware])
            ->group(function (Router $router) use ($method, $route, $action, $name) {
                $controllerRoute = $router->addRoute($method->value, $route, $action);
                if ($name) {
                    $controllerRoute->name($name);
                }
            });
    }

    private function getFiles(string $path): array
    {
        $files = [];
        $list = array_filter(scandir($path), fn ($f) => ! in_array($f, ['.', '..']));

        foreach ($list as $file) {
            $filePath = $path.DIRECTORY_SEPARATOR.$file;
            if (is_dir($filePath)) {
                $files = array_merge($this->getFiles($filePath));
            } else {
                $files[] = $file;
            }
        }

        return $files;
    }
}
