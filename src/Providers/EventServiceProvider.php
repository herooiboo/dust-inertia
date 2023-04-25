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
        return array_reduce(app_modules(), function ($listeners, $module) {
            $moduleListeners = get_module_path($module, ['Manager', 'Listeners']);
            if (file_exists($moduleListeners)) {
                $listeners[] = $moduleListeners;
            }

            return $listeners;
        }, []);
    }
}
