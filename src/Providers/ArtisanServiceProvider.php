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
use App\Console\Core\Commands\Dev\ServiceMakeCommand;
use Illuminate\Foundation\Console\ConsoleMakeCommand;
use Illuminate\Foundation\Console\RequestMakeCommand;
use Illuminate\Routing\Console\ControllerMakeCommand;
use Illuminate\Routing\Console\MiddlewareMakeCommand;
use App\Console\Core\Commands\Dev\ResponseMakeCommand;
use Illuminate\Foundation\Console\ListenerMakeCommand;
use Illuminate\Foundation\Console\ObserverMakeCommand;
use Illuminate\Foundation\Console\ResourceMakeCommand;
use Illuminate\Foundation\Console\ExceptionMakeCommand;
use App\Console\Core\Commands\Dev\RepositoryMakeCommand;
use Illuminate\Database\Console\Seeds\SeederMakeCommand;
use Illuminate\Foundation\Console\NotificationMakeCommand;
use Illuminate\Database\Console\Factories\FactoryMakeCommand;
use Illuminate\Foundation\Providers\ArtisanServiceProvider as BaseArtisanServiceProvider;

class ArtisanServiceProvider extends BaseArtisanServiceProvider
{
    public function register()
    {
        $this->devCommands['ResponseMake'] = ResponseMakeCommand::class;
        $this->devCommands['ServiceMake'] = ServiceMakeCommand::class;
        $this->devCommands['RepositoryMake'] = RepositoryMakeCommand::class;

        parent::register();
    }

    public function registerModelMakeCommand()
    {
        $this->app->singleton(ModelMakeCommand::class, function ($app) {
            return new \App\Console\Core\Commands\Dev\ModelMakeCommand($app['files']);
        });
    }

    public function registerControllerMakeCommand()
    {
        $this->app->singleton(ControllerMakeCommand::class, function ($app) {
            return new \App\Console\Core\Commands\Dev\ControllerMakeCommand($app['files']);
        });
    }

    public function registerTestMakeCommand()
    {
        $this->app->singleton(TestMakeCommand::class, function ($app) {
            return new \App\Console\Core\Commands\Dev\TestMakeCommand($app['files']);
        });
    }

    protected function registerCastMakeCommand()
    {
        $this->app->singleton(CastMakeCommand::class, function ($app) {
            return new \App\Console\Core\Commands\Dev\CastMakeCommand($app['files']);
        });
    }

    protected function registerConsoleMakeCommand()
    {
        $this->app->singleton(ConsoleMakeCommand::class, function ($app) {
            return new \App\Console\Core\Commands\Dev\ConsoleMakeCommand($app['files']);
        });
    }

    protected function registerEventMakeCommand()
    {
        $this->app->singleton(EventMakeCommand::class, function ($app) {
            return new \App\Console\Core\Commands\Dev\EventMakeCommand($app['files']);
        });
    }

    protected function registerExceptionMakeCommand()
    {
        $this->app->singleton(ExceptionMakeCommand::class, function ($app) {
            return new \App\Console\Core\Commands\Dev\ExceptionMakeCommand($app['files']);
        });
    }

    protected function registerFactoryMakeCommand()
    {
        $this->app->singleton(FactoryMakeCommand::class, function ($app) {
            return new \App\Console\Core\Commands\Dev\FactoryMakeCommand($app['files']);
        });
    }

    protected function registerJobMakeCommand()
    {
        $this->app->singleton(JobMakeCommand::class, function ($app) {
            return new \App\Console\Core\Commands\Dev\JobMakeCommand($app['files']);
        });
    }

    protected function registerListenerMakeCommand()
    {
        $this->app->singleton(ListenerMakeCommand::class, function ($app) {
            return new \App\Console\Core\Commands\Dev\ListenerMakeCommand($app['files']);
        });
    }

    protected function registerMailMakeCommand()
    {
        $this->app->singleton(MailMakeCommand::class, function ($app) {
            return new \App\Console\Core\Commands\Dev\MailMakeCommand($app['files']);
        });
    }

    protected function registerMiddlewareMakeCommand()
    {
        $this->app->singleton(MiddlewareMakeCommand::class, function ($app) {
            return new \App\Console\Core\Commands\Dev\MiddlewareMakeCommand($app['files']);
        });
    }

    protected function registerNotificationMakeCommand()
    {
        $this->app->singleton(NotificationMakeCommand::class, function ($app) {
            return new \App\Console\Core\Commands\Dev\NotificationMakeCommand($app['files']);
        });
    }

    protected function registerRequestMakeCommand()
    {
        $this->app->singleton(RequestMakeCommand::class, function ($app) {
            return new \App\Console\Core\Commands\Dev\RequestMakeCommand($app['files']);
        });
    }

    protected function registerResourceMakeCommand()
    {
        $this->app->singleton(ResourceMakeCommand::class, function ($app) {
            return new \App\Console\Core\Commands\Dev\ResourceMakeCommand($app['files']);
        });
    }

    protected function registerSeederMakeCommand()
    {
        $this->app->singleton(SeederMakeCommand::class, function ($app) {
            return new \App\Console\Core\Commands\Dev\SeederMakeCommand($app['files']);
        });
    }

    protected function registerPolicyMakeCommand()
    {
        $this->app->singleton(PolicyMakeCommand::class, function ($app) {
            return new \App\Console\Core\Commands\Dev\PolicyMakeCommand($app['files']);
        });
    }

    protected function registerResponseMakeCommand()
    {
        $this->app->singleton(ResponseMakeCommand::class, function ($app) {
            return new ResponseMakeCommand($app['files']);
        });
    }

    protected function registerServiceMakeCommand()
    {
        $this->app->singleton(ServiceMakeCommand::class, function ($app) {
            return new ServiceMakeCommand($app['files']);
        });
    }

    protected function registerRepositoryMakeCommand()
    {
        $this->app->singleton(RepositoryMakeCommand::class, function ($app) {
            return new RepositoryMakeCommand($app['files']);
        });
    }

    protected function registerObserverMakeCommand()
    {
        $this->app->singleton(ObserverMakeCommand::class, function ($app) {
            return new \App\Console\Core\Commands\Dev\ObserverMakeCommand($app['files']);
        });
    }

    protected function registerSeedCommand()
    {
        $this->app->singleton(SeedCommand::class, function ($app) {
            return new \App\Console\Core\Commands\Database\SeedCommand($app['db']);
        });
    }
}
