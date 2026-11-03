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
use FireHub\Adapter\HTTP\Header\PermissionsPolicy\ {
    Allowlist, Feature
};
use FireHub\Runtime;

/**
 * ### Permissions Policy
 *
 * Represents the Permissions-Policy HTTP response header.
 *
 * Controls access to browser capabilities for the current document and its embedded browsing contexts.
 *
 * The policy is immutable. Modifications return new instances.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<string>
 */
final readonly class PermissionsPolicy extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param array<string, \FireHub\Adapter\HTTP\Header\PermissionsPolicy\Allowlist> $directives <p>
     * Configured directives.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private array $directives = []
    ) {}

    /**
     * ### Adds or replaces a feature directive
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\PermissionsPolicy\Feature $feature <p>
     * Browser feature.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\PermissionsPolicy\Allowlist $allowlist <p>
     * Allowed origins.
     * </p>
     *
     * @return self Updated permissions policy.
     */
    public function withFeature (Feature $feature, Allowlist $allowlist):self {

        $directives = $this->directives;

        $directives[$feature->value] = $allowlist;

        return new self($directives);

    }

    /**
     * ### Removes a feature directive
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\PermissionsPolicy\Feature $feature <p>
     * Browser feature.
     * </p>
     *
     * @return self Updated permissions policy.
     */
    public function withoutFeature (Feature $feature):self {

        $directives = $this->directives;

        unset($directives[$feature->value]);

        return new self($directives);

    }

    /**
     * ### Checks whether a feature directive exists
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\PermissionsPolicy\Feature $feature <p>
     * Browser feature.
     * </p>
     *
     * @return bool True if the feature is configured.
     */
    public function hasFeature (Feature $feature):bool {

        return isset($this->directives[$feature->value]);

    }

    /**
     * ### Gets a feature allowlist
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\PermissionsPolicy\Feature $feature <p>
     * Browser feature.
     * </p>
     *
     * @return null|\FireHub\Adapter\HTTP\Header\PermissionsPolicy\Allowlist Configured allowlist or null.
     */
    public function feature (Feature $feature):?Allowlist {

        return $this->directives[$feature->value] ?? null;

    }

    /**
     * ### Returns the serialized permissions policy
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\PermissionsPolicy\Allowlist::value() To get the serialized allowlist.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join the directives.
     *
     * @return string Serialized Permissions-Policy header value.
     */
    public function value ():string {

        $directives = [];

        foreach ($this->directives as $feature => $allowlist)
            $directives[] = $feature.'='.$allowlist->value();

        return Runtime\Str\SB\Delimiter::implode($directives, ', ');

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\PermissionsPolicy\Allowlist::value() To get the serialized allowlist.
     * @uses \FireHub\Runtime\Arr\Ordering::sortByKeys() To sort the directives by key.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join the directives.
     */
    protected function comparisonValue ():string {

        $directives = $this->directives;

        Runtime\Arr\Ordering::sortByKeys($directives, type: Runtime\Type\Arr\SortType::STRING);

        $values = [];

        foreach ($directives as $feature => $allowlist)
            $values[] = $feature.'='.$allowlist->value();

        return Runtime\Str\SB\Delimiter::implode($values, ', ');

    }

}