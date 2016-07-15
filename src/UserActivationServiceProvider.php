<?php

namespace Kitano\UserActivation;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Kitano\UserActivation\Observers\UserActivationObserver;
use Kitano\UserActivation\Services\UserActivationService;

class UserActivationServiceProvider extends BaseServiceProvider
{
    /** Indicates if loading of the provider is deferred. @var bool */
    protected $defer = false;

    /** The Authentication model @var   */
    protected $model;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $config_path = __DIR__ . '/config/user_activation.php';
        $views_path  = __DIR__ . '/resources/views';
        $lang_path   = __DIR__ . '/resources/lang';
        $db_path     = __DIR__ . '/database/migrations';
        $auth_path   = __DIR__ . '/resources/views/auth';
        $wlcm_path   = __DIR__ . '/resources/views/welcome.blade.php';
        $emls_path   = __DIR__ . '/resources/views/emails';

        if (! $this->app->routesAreCached()) {
            require __DIR__.'/routes/routes.php';
        }

        $this->publishes(
            [
                $config_path => config_path('user_activation.php'),
                //$views_path  => resource_path('views/vendor/activation'),
                $lang_path   => resource_path('lang'),
                $db_path     => database_path('migrations'),
                $auth_path   => resource_path('views/auth'),
                $wlcm_path   => resource_path('views/welcome.blade.php'),
                $emls_path   => resource_path('views/vendor/activation/emails')
            ],
            'user_activation'
        );

        $this->loadViewsFrom($views_path, 'UserActivation');
        $this->loadTranslationsFrom($lang_path, 'userActivation');

        $this->setAuthModel();

        $this->model->observe($this->app->make(UserActivationObserver::class));
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $config_path = __DIR__ . '/config/user_activation.php';

        $this->mergeConfigFrom(
            $config_path,
            'user_activation'
        );

        $this->app->singleton(UserActivationObserver::class, function () {
            return new UserActivationObserver(
                new $this->model,
                new UserActivationService()
            );
        });
    }

    /**
     * Set the Authentication User Model
     */
    private function setAuthModel()
    {
        $auth_model = $this->app
                           ->make('config')
                           ->get('auth.providers.users.model');

        $this->model = new $auth_model;
    }
}
