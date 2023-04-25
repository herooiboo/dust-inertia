<?php

namespace Dust\Providers;

use Illuminate\Foundation\Console\JobMakeCommand;
use Illuminate\Database\Console\Seeds\SeedCommand;
use Illuminate\Foundation\Console\CastMakeCommand;
use Illuminate\Foundation\Console\MailMakeCommand;
use Illuminate\Foundation\Console\TestMakeCommand;
use Illuminate\Foundation\Console\EventMakeCommand;
use Illuminate\Foundation\Console\ModelMakeCommand;
use Illuminate\Foundation\Console\PolicyMakeCommand;
use Illuminate\Foundation\Console\ConsoleMakeCommand;
use Illuminate\Foundation\Console\RequestMakeCommand;
use Illuminate\Routing\Console\ControllerMakeCommand;
use Illuminate\Routing\Console\MiddlewareMakeCommand;
use Dust\Console\Core\Commands\Dev\ServiceMakeCommand;
use Illuminate\Foundation\Console\ListenerMakeCommand;
use Illuminate\Foundation\Console\ObserverMakeCommand;
use Illuminate\Foundation\Console\ResourceMakeCommand;
use Dust\Console\Core\Commands\Dev\ResponseMakeCommand;
use Illuminate\Foundation\Console\ExceptionMakeCommand;
use Illuminate\Database\Console\Seeds\SeederMakeCommand;
use Dust\Console\Core\Commands\Dev\RepositoryMakeCommand;
use Illuminate\Foundation\Console\NotificationMakeCommand;
use Illuminate\Database\Console\Factories\FactoryMakeCommand;
use Illuminate\Foundation\Providers\ArtisanServiceProvider as BaseArtisanServiceProvider;

class ArtisanServiceProvider extends BaseArtisanServiceProvider
{
    public function register(): void
    {
        $this->devCommands['ResponseMake'] = ResponseMakeCommand::class;
        $this->devCommands['ServiceMake'] = ServiceMakeCommand::class;
        $this->devCommands['RepositoryMake'] = RepositoryMakeCommand::class;

        parent::register();
    }

    public function registerModelMakeCommand(): void
    {
        $this->app->singleton(ModelMakeCommand::class, function ($app) {
            return new \Dust\Console\Core\Commands\Dev\ModelMakeCommand($app['files']);
        });
    }

    public function registerControllerMakeCommand(): void
    {
        $this->app->singleton(ControllerMakeCommand::class, function ($app) {
            return new \Dust\Console\Core\Commands\Dev\ControllerMakeCommand($app['files']);
        });
    }

    public function registerTestMakeCommand(): void
    {
        $this->app->singleton(TestMakeCommand::class, function ($app) {
            return new \Dust\Console\Core\Commands\Dev\TestMakeCommand($app['files']);
        });
    }

    protected function registerCastMakeCommand(): void
    {
        $this->app->singleton(CastMakeCommand::class, function ($app) {
            return new \Dust\Console\Core\Commands\Dev\CastMakeCommand($app['files']);
        });
    }

    protected function registerConsoleMakeCommand(): void
    {
        $this->app->singleton(ConsoleMakeCommand::class, function ($app) {
            return new \Dust\Console\Core\Commands\Dev\ConsoleMakeCommand($app['files']);
        });
    }

    protected function registerEventMakeCommand(): void
    {
        $this->app->singleton(EventMakeCommand::class, function ($app) {
            return new \Dust\Console\Core\Commands\Dev\EventMakeCommand($app['files']);
        });
    }

    protected function registerExceptionMakeCommand(): void
    {
        $this->app->singleton(ExceptionMakeCommand::class, function ($app) {
            return new \Dust\Console\Core\Commands\Dev\ExceptionMakeCommand($app['files']);
        });
    }

    protected function registerFactoryMakeCommand(): void
    {
        $this->app->singleton(FactoryMakeCommand::class, function ($app) {
            return new \Dust\Console\Core\Commands\Dev\FactoryMakeCommand($app['files']);
        });
    }

    protected function registerJobMakeCommand(): void
    {
        $this->app->singleton(JobMakeCommand::class, function ($app) {
            return new \Dust\Console\Core\Commands\Dev\JobMakeCommand($app['files']);
        });
    }

    protected function registerListenerMakeCommand(): void
    {
        $this->app->singleton(ListenerMakeCommand::class, function ($app) {
            return new \Dust\Console\Core\Commands\Dev\ListenerMakeCommand($app['files']);
        });
    }

    protected function registerMailMakeCommand(): void
    {
        $this->app->singleton(MailMakeCommand::class, function ($app) {
            return new \Dust\Console\Core\Commands\Dev\MailMakeCommand($app['files']);
        });
    }

    protected function registerMiddlewareMakeCommand(): void
    {
        $this->app->singleton(MiddlewareMakeCommand::class, function ($app) {
            return new \Dust\Console\Core\Commands\Dev\MiddlewareMakeCommand($app['files']);
        });
    }

    protected function registerNotificationMakeCommand(): void
    {
        $this->app->singleton(NotificationMakeCommand::class, function ($app) {
            return new \Dust\Console\Core\Commands\Dev\NotificationMakeCommand($app['files']);
        });
    }

    protected function registerRequestMakeCommand(): void
    {
        $this->app->singleton(RequestMakeCommand::class, function ($app) {
            return new \Dust\Console\Core\Commands\Dev\RequestMakeCommand($app['files']);
        });
    }

    protected function registerResourceMakeCommand(): void
    {
        $this->app->singleton(ResourceMakeCommand::class, function ($app) {
            return new \Dust\Console\Core\Commands\Dev\ResourceMakeCommand($app['files']);
        });
    }

    protected function registerSeederMakeCommand(): void
    {
        $this->app->singleton(SeederMakeCommand::class, function ($app) {
            return new \Dust\Console\Core\Commands\Dev\SeederMakeCommand($app['files']);
        });
    }

    protected function registerPolicyMakeCommand(): void
    {
        $this->app->singleton(PolicyMakeCommand::class, function ($app) {
            return new \Dust\Console\Core\Commands\Dev\PolicyMakeCommand($app['files']);
        });
    }

    protected function registerResponseMakeCommand(): void
    {
        $this->app->singleton(ResponseMakeCommand::class, function ($app) {
            return new ResponseMakeCommand($app['files']);
        });
    }

    protected function registerServiceMakeCommand(): void
    {
        $this->app->singleton(ServiceMakeCommand::class, function ($app) {
            return new ServiceMakeCommand($app['files']);
        });
    }

    protected function registerRepositoryMakeCommand(): void
    {
        $this->app->singleton(RepositoryMakeCommand::class, function ($app) {
            return new RepositoryMakeCommand($app['files']);
        });
    }

    protected function registerObserverMakeCommand(): void
    {
        $this->app->singleton(ObserverMakeCommand::class, function ($app) {
            return new \Dust\Console\Core\Commands\Dev\ObserverMakeCommand($app['files']);
        });
    }

    protected function registerSeedCommand(): void
    {
        $this->app->singleton(SeedCommand::class, function ($app) {
            return new \Dust\Console\Core\Commands\Database\SeedCommand($app['db']);
        });
    }
}
