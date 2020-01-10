<?php

namespace Apitizer;

use Apitizer\ExceptionStrategy\Raise;
use Apitizer\ExceptionStrategy\Strategy;
use Apitizer\Parser\InputParser;
use Apitizer\Parser\Parser;
use Apitizer\Rendering\BasicRenderer;
use Apitizer\Rendering\Renderer;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public $bindings = [
        Strategy::class => Raise::class,
        Parser::class   => InputParser::class,
        Renderer::class => BasicRenderer::class,
    ];

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
        $root = __DIR__ . '/../../';
        $configPath = $root . 'config/apitizer.php';
        $this->publishes([$configPath => $this->getConfigPath()], 'config');

        if ($this->app['config']->get('apitizer.generate_documentation', false)) {
            $this->registerRoutes();
        }

        $this->loadViewsFrom($root . '/resources/views', 'apitizer');

        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\ValidateSchemaCommand::class,
            ]);
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
     * Get the config path
     *
     * @return string
     */
    protected function getConfigPath()
    {
        return config_path('apitizer.php');
    }
}
