<?php

namespace Apitizer;

use Apitizer\ExceptionStrategy\Raise;
use Apitizer\ExceptionStrategy\Strategy;
use Apitizer\Parser\InputParser;
use Apitizer\Parser\Parser;
use Apitizer\Rendering\BasicRenderer;
use Apitizer\Rendering\Renderer;
use Apitizer\Routing\SchemaRoute;
use Illuminate\Routing\Router;

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

        $this->app->bind(Strategy::class, Raise::class);
        $this->app->bind(Parser::class, InputParser::class);
        $this->app->bind(Renderer::class, BasicRenderer::class);
        $this->app->singleton(SchemaLoader::class, function () {
            return new SchemaLoader();
        });

        Router::macro('schema', function (string $schema) {
            /** @var class-string<\Apitizer\Schema> $schema */
            return (new SchemaRoute($schema))->generateRoutes();
        });
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
        $this->loadTranslationsFrom($root . '/resources/lang', 'apitizer');

        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\ValidateSchemaCommand::class,
            ]);
        }
    }

    protected function registerRoutes(): void
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
