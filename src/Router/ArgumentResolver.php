<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.2
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Router;

use FireHub\Adapter\HTTP\ {
    ExecutionContext, Request
};
use FireHub\Adapter\HTTP\Router\Exception\UnresolvableArgumentException;
use ReflectionNamedType, ReflectionMethod, ReflectionParameter;

/**
 * ### Resolves HTTP controller action arguments
 *
 * Resolves controller action parameters using route parameters, framework context objects, and the current
 * dependency injection scope.
 * @since 1.0.0
 */
final readonly class ArgumentResolver {

    /**
     * ### Resolves controller action arguments
     *
     * <code>
     * $arguments = $resolver->resolve(
     *     $controller,
     *     'show',
     *     $context,
     *     $match
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Router\ArgumentResolver::resolveParameter() To resolve a single action parameter.
     *
     * @param object $controller <p>
     * Controller instance.
     * </p>
     * @param non-empty-string $method <p>
     * Controller action name.
     * </p>
     * @param \FireHub\Adapter\HTTP\ExecutionContext $context <p>
     * Current HTTP execution context.
     * </p>
     * @param \FireHub\Adapter\HTTP\Router\RouteMatch $match <p>
     * Matched route.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\UnresolvableArgumentException If an argument cannot be resolved.
     *
     * @return list<mixed> Resolved arguments.
     */
    public function resolve (object $controller, string $method, ExecutionContext $context, RouteMatch $match):array {

        $reflection = new ReflectionMethod($controller, $method);

        $arguments = [];

        foreach ($reflection->getParameters() as $parameter)
            $arguments[] = $this->resolveParameter(
                $parameter,
                $context,
                $match
            );

        return $arguments;

    }

    /**
     * ### Resolves a single action parameter
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\ExecutionContext::scope() To get the current dependency injection scope.
     * @uses \FireHub\Adapter\HTTP\Router\RouteMatch::parameters() To retrieve route parameters.
     * @uses \FireHub\Adapter\HTTP\Request::request() To retrieve the HTTP request.
     * @uses \FireHub\Adapter\HTTP\Router\RouteMatch::route() To retrieve the matched route.
     *
     * @param ReflectionParameter $parameter <p>
     * Action parameter.
     * </p>
     * @param \FireHub\Adapter\HTTP\ExecutionContext $context <p>
     * HTTP execution context.
     * </p>
     * @param \FireHub\Adapter\HTTP\Router\RouteMatch $match <p>
     * Matched route.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Router\Exception\UnresolvableArgumentException If the parameter cannot be resolved.
     *
     * @return mixed Resolved parameter value.
     */
    private function resolveParameter (ReflectionParameter $parameter, ExecutionContext $context, RouteMatch $match):mixed {

        $name = $parameter->getName();
        $type = $parameter->getType();

        if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {

            $class = $type->getName();

            if ($class === ExecutionContext::class)
                return $context;

            if ($class === Request::class)
                return $context->request();

            if ($class === RouteMatch::class)
                return $match;

        }

        $parameters = $match->parameters();

        if (isset($parameters[$name])) {

            $value = $parameters[$name];

            if (
                !$type instanceof ReflectionNamedType
                || $type->getName() === 'string'
                || $type->getName() === 'mixed'
            ) return $value;

            throw new UnresolvableArgumentException(
                "Route parameter '$name' cannot be assigned to the declared action parameter type."
            );

        }

        if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {

            /** @var class-string $class */
            $class = $type->getName();

            if ($context->scope()->has($class))
                return $context->scope()->get($class);

        }

        if ($parameter->isDefaultValueAvailable())
            return $parameter->getDefaultValue();

        throw new UnresolvableArgumentException(
            "Unable to resolve controller action parameter '$name'."
        );

    }

}