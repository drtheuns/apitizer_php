<?php

namespace Apitizer\Routing;

use Apitizer\QueryBuilder;
use Apitizer\Types\Association;
use Illuminate\Support\Str;

class RouteSegmentBuilder
{
    /**
     * @var QueryBuilder
     */
    protected $schema;

    /**
     * @var Scope
     */
    protected $scope;

    /**
     * @var array<int, array{segment: string, type: string,
     *                       schema: class-string, visible: bool}>|null
     * where type is either 'param' or 'segment'.
     */
    protected $segments;

    /**
     * @var bool whether or not to add an additional parameter to the segments.
     */
    protected $withParameter = false;

    public function __construct(QueryBuilder $schema, Scope $scope, bool $withParameter = false)
    {
        $this->schema = $schema;
        $this->scope = $scope;
        $this->withParameter = $withParameter;

        $this->generate();
    }

    public function path(): string
    {
        return collect($this->segments)
            ->filter(function ($segment) {
                return (bool) $segment['visible'];
            })
            ->map(function ($segment) {
                return $segment['type'] === 'param'
                                        ? '{' . $segment['segment'] . '}'
                                        : $segment['segment'];
            })
            ->implode('/');
    }

    public function name(string $actionMethod): string
    {
        return collect($this->segments)
            ->filter(function ($segment) {
                return $segment['type'] === 'segment';
            })
            ->pluck('segment')
            ->implode('.') . ".$actionMethod";
    }

    /**
     * @return array<string, array<string, string|bool>>
     */
    public function routeParameters(): array
    {
        return collect($this->segments)
            ->mapWithKeys(function ($segment) {
                return [$segment['segment'] => [
                    'schema'      => $segment['schema'],
                    'has_param'   => $segment['type'] === 'param' && $segment['visible'],
                    'association' => $segment['association'],
                ]];
            })
            ->all();
    }

    protected function generate(): void
    {
        $segments = [];
        $schema = $this->schema;
        $scope = $this->scope;
        $firstIteration = ! $this->withParameter;

        // We're going to build all the segments in reverse order (child first)
        // and work upwards through the parents.
        do {
            $segment = $this->getSegmentName($schema, $scope);
            $association = $this->getAssociationToParent($schema, $scope);

            // After the first iteration we always need to add the ID param.
            // Since we're building the segments in reverse order, we need to
            // use the current segment and add it _before_ we add that segment.
            if (! $firstIteration) {
                $segments[] = [
                    'segment' => Str::singular($segment),
                    'type'    => 'param',
                    'schema'  => get_class($schema),
                    // If the association is, for example, a belongsTo, then we
                    // don't have a route param, but we still need to add the
                    // segment so the controller knows about the relationship.
                    'visible' => $association ? $association->returnsCollection() : true,
                    'association' => $association ? $association->getName() : null,
                ];
            }

            $segments[] = [
                'segment' => $segment,
                'type'    => 'segment',
                'schema'  => get_class($schema),
                'visible' => true,
                'association' => $association ? $association->getName() : null,
            ];

            // Prepare for next iteration.
            $schema = $schema->getParent();
            /** @var Scope $scope */
            $scope = $scope->getParent();
            $firstIteration = false;
        } while ($schema !== null);

        $this->segments = array_reverse($segments);
    }

    protected function getSegmentName(QueryBuilder $schema, Scope $scope): string
    {
        // The programmer may override the segment name by setting the path
        // variable.
        if ($name = $scope->getPath()) {
            return $name;
        }

        // For scopes that are the child of an association, we can simply use
        // the association name.
        if ($name = $scope->getName()) {
            return $name;
        }

        return $this->guessSegmentNameFromSchema($schema);
    }

    protected function guessSegmentNameFromSchema(QueryBuilder $schema): string
    {
        $classname = class_basename($schema);
        $segment = Str::endsWith($classname, 'Builder') ? substr($classname, 0, -7) : $classname;

        return Str::plural(Str::slug($segment));
    }

    protected function getAssociationToParent(QueryBuilder $schema, Scope $scope): ?Association
    {
        if (! $parent = $schema->getParent()) {
            return null;
        }

        return $parent->getAssociations()[$scope->getName()];
    }
}
