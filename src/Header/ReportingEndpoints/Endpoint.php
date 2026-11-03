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

namespace FireHub\Adapter\HTTP\Header\ReportingEndpoints;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\ReportingGroup;
use FireHub\Adapter\HTTP\Header\Exception\InvalidReportingEndpointException;
use FireHub\Foundation\Uri;
use FireHub\Runtime;

/**
 * ### Represents a Reporting API endpoint
 *
 * Associates a reporting group with an absolute HTTPS URL.
 *
 * The endpoint is serialized as a Structured Fields dictionary member.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Endpoint extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the reporting endpoint.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the reporting endpoint syntax.
     * @uses \FireHub\Foundation\Uri::value() To get the URI value.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\ReportingGroup $group <p>
     * Reporting group name.
     * </p>
     * @param \FireHub\Foundation\Uri $uri <p>
     * Absolute HTTPS reporting endpoint.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidReportingEndpointException If the endpoint is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private ReportingGroup $group,
        private Uri $uri
    ) {

        $value = $uri->value();

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                    '~^https://[^/?#]+(?:[/?#]|$)~i',
                    $value
                ) && !Runtime\Str\SB\Regex::match('/[\x00-\x1F\x7F]/', $value),
            fn() => new InvalidReportingEndpointException(
                'Reporting endpoints must use an absolute HTTPS URL.'
            )
        );

    }

    /**
     * ### Gets the reporting group
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\ReportingGroup Reporting group.
     */
    public function group ():ReportingGroup {

        return $this->group;

    }

    /**
     * ### Gets the reporting endpoint URI
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Uri Reporting endpoint URI.
     */
    public function uri ():Uri {

        return $this->uri;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri::value() To get the URI value.
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\ReportingGroup::value() To get the reporting group name.
     * @uses \FireHub\Runtime\Str\SB\Replace::replace() To escape the URI.
     *
     * @return non-empty-string Serialized endpoint.
     */
    public function value ():string {

        $uri = $this->uri->value();

        $uri = Runtime\Str\SB\Replace::replace(
            ['\\', '"'],
            ['\\\\', '\\"'],
            $uri
        );

        return $this->group->value().'="'.$uri.'"';

    }

}