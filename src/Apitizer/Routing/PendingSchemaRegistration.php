<?php

namespace Apitizer\Routing;

use Apitizer\QueryBuilder;
use Apitizer\GenericApi\Service;

class PendingSchemaRegistration
{
    /**
     * @var bool Has the resource been registered yet?
     */
    protected $registered = false;

    /**
     * @var class-string<QueryBuilder>
     */
    protected $schema;

    /**
     * @var array{only?: string[], except?: string[], controller?: class-string,
     *            service?: class-string<Service>}
     */
    protected $options = [];

    /**
     * @param class-string<QueryBuilder> $schema
     */
    public function __construct(string $schema)
    {
        $this->schema = $schema;
    }

    /**
     * Set the methods the controller should apply to.
     *
     * @param string[] $methods
     *
     * @return \Apitizer\Routing\PendingSchemaRegistration
     */
    public function only(array $methods)
    {
        $this->options['only'] = $methods;

        return $this;
    }

    /**
     * Set the methods the controller should exclude.
     *
     * @param string[] $methods
     *
     * @return \Apitizer\Routing\PendingSchemaRegistration
     */
    public function except(array $methods)
    {
        $this->options['except'] = $methods;

        return $this;
    }

    /**
     * Set the controller that should be used.
     *
     * @param string $controller
     *
     * @return \Apitizer\Routing\PendingSchemaRegistration
     */
    public function controller(string $controller)
    {
        // TODO: allow users to give the controller as a string, and build it to
        // 'app/http/controllers'?
        $this->options['controller'] = $controller;

        return $this;
    }

    /**
     * Set the service class that should be used to handle the request.
     *
     * @param class-string<Service> $service
     *
     * @return \Apitizer\Routing\PendingSchemaRegistration
     */
    public function service(string $service)
    {
        $this->options['service'] = $service;

        return $this;
    }

    public function register(): void
    {
        $this->registered = true;

        (new Route($this->schema, $this->options))->register();
    }

    /**
     * Handle the object's destruction.
     *
     * @return void
     */
    public function __destruct()
    {
        if (! $this->registered) {
            $this->register();
        }
    }
}
