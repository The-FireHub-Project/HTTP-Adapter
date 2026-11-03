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

namespace FireHub\Adapter\HTTP\Header\Reporting;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Exception\InvalidReportingEndpointException;
use FireHub\Runtime;

/**
 * ### Reporting Group
 *
 * Represents a named reporting endpoint group used by the Reporting API.
 *
 * The group name is serialized as an HTTP Structured Fields dictionary key and may be referenced by the Content
 * Security Policy report-to directive.
 *
 * Group names must conform to the Structured Fields key syntax defined  by RFC 8941.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Group extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the reporting group name.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the reporting group name syntax.
     *
     * @param non-empty-string $name <p>
     * Reporting group name.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidReportingEndpointException If the name is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $name
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                '/^[a-z*][a-z0-9_.*-]*$/D',
                $name
            ),
            fn() => new InvalidReportingEndpointException(
                'Invalid CSP reporting group name.'
            )
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return non-empty-string Reporting group name.
     */
    public function value ():string {

        return $this->name;

    }

}