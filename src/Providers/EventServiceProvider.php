<?php

namespace Dust\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return true;
    }

    protected function discoverEventsWithin(): array
    {
        return $this->moduleListener();
    }

    protected function moduleListener(): array
    {
        return array_reduce(config('nebula.modules.paths'), function($listeners, $path) {
            $path = app_path($path);
            if(!file_exists($path)) {
                return $listeners;
            }

            $modules = array_filter(scandir($path), fn ($module) => ! in_array($module, ['.', '..']));
            return [
                ...$listeners,
                ...array_reduce($modules, function ($listeners, $module) use ($path) {
                    $moduleListeners = implode(DIRECTORY_SEPARATOR, [$path, ucfirst($module), 'Core', 'Listeners']);
                    if (file_exists($moduleListeners)) {
                        $listeners[] = $moduleListeners;
                    }

                    return $listeners;
                }, [])
            ];
        }, []);
    }
}
