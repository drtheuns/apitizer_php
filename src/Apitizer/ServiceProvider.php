<?php

namespace Apitizer;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Register the service provider
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__ . '/../../config/apitizer.php';
        $this->mergeConfigFrom($configPath, 'apitizer');
    }

    /**
     * Bootstrap the application events
     *
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__ . '/../../config/apitizer.php';
        $this->publishes([$configPath => $this->getConfigPath()], 'config');

        if ($this->app['config']->get('apitizer.generate_documentation', false)) {
            $this->registerRoutes();
        }
    }

    protected function registerRoutes()
    {
        $routeConfig = [
            'namespace' => 'Apitizer\Controllers',
            'prefix' => $this->app['config']->get('apitizer.route_prefix'),
        ];

        $this->app['router']->group($routeConfig, function ($router) {
            $router->get('/', [
                'uses' => 'DocumentationController@list',
                'as' => 'apitizer.apidoc',
            ]);
        });
    }

    /**
     * Publish the config file
     *
     * @param  string $configPath
     */
    protected function publishConfig($configPath)
    {
        $this->publishes([$configPath => config_path('apitizer.php')], 'config');
    }

    /**
     * Get the config path
     *
     * @return string
     */
    protected function getConfigPath()
    {
        return config_path('debugbar.php');
    }
}
