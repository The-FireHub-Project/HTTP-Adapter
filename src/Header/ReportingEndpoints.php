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

namespace FireHub\Adapter\HTTP\Header;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\ReportingGroup;
use FireHub\Adapter\HTTP\Header\ReportingEndpoints\Endpoint;
use FireHub\Runtime;

/**
 * ### Reporting Endpoints
 *
 * Represents an immutable Reporting-Endpoints HTTP response header.
 *
 * Defines named endpoints for browser-generated reports.
 *
 * Each reporting group may reference one endpoint URL.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<string>
 */
final readonly class ReportingEndpoints extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param array<string, \FireHub\Adapter\HTTP\Header\ReportingEndpoints\Endpoint> $endpoints <p>
     * Reporting endpoints.
     * </p>
     *
     * @return void
     */
    private function __construct (
        private array $endpoints = []
    ) {}

    /**
     * ### Creates an empty reporting endpoint collection
     * @since 1.0.0
     *
     * @return self Empty collection.
     */
    public static function create ():self {

        return new self;

    }

    /**
     * ### Adds or replaces a reporting endpoint
     *
     * If an endpoint with the same reporting group already exists, it is replaced.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ReportingEndpoints\Endpoint::group() To get the reporting group.
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\ReportingGroup::value() To get the reporting group name.
     *
     * @param \FireHub\Adapter\HTTP\Header\ReportingEndpoints\Endpoint $endpoint <p>
     * Reporting endpoint.
     * </p>
     *
     * @return self Updated collection.
     */
    public function withEndpoint (Endpoint $endpoint):self {

        $endpoints = $this->endpoints;

        $endpoints[$endpoint->group()->value()] = $endpoint;

        return new self($endpoints);

    }

    /**
     * ### Removes a reporting endpoint
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\ReportingGroup::value() To get the reporting group name.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\ReportingGroup $group <p>
     * Reporting group.
     * </p>
     *
     * @return self Updated collection.
     */
    public function withoutEndpoint (ReportingGroup $group):self {

        $endpoints = $this->endpoints;

        unset($endpoints[$group->value()]);

        return new self($endpoints);

    }

    /**
     * ### Checks whether a reporting group exists
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\ReportingGroup::value() To get the reporting group name.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\ReportingGroup $group <<p>
     * Reporting group.
     * </p>
     *
     * @return bool True if the group exists.
     */
    public function hasEndpoint (ReportingGroup $group):bool {

        return isset($this->endpoints[$group->value()]);

    }

    /**
     * ### Gets a reporting endpoint
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\ReportingGroup::value() To get the reporting group name.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\ReportingGroup $group <p>
     * Reporting group.
     * </p>
     *
     * @return null|\FireHub\Adapter\HTTP\Header\ReportingEndpoints\Endpoint Matching endpoint or null.
     */
    public function endpoint (ReportingGroup $group):?Endpoint {

        return $this->endpoints[$group->value()] ?? null;

    }

    /**
     * ### Gets all reporting endpoints
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::values() To get the endpoint values.
     *
     * @return list<\FireHub\Adapter\HTTP\Header\ReportingEndpoints\Endpoint> Reporting endpoints.
     */
    public function all ():array {

        return Runtime\Arr\Access::values($this->endpoints);

    }

    /**
     * ### Serializes the Reporting-Endpoints header
     *
     * Serializes endpoints as an HTTP Structured Fields dictionary.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ReportingEndpoints\Endpoint::value() To get the endpoint value.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To serialize the endpoints.
     *
     * @return string Serialized Reporting-Endpoints header.
     */
    public function value ():string {

        $values = [];

        foreach ($this->endpoints as $endpoint)
            $values[] = $endpoint->value();

        return Runtime\Str\SB\Delimiter::implode($values, ', ');

    }

}