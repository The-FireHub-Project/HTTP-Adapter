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

namespace FireHub\Adapter\HTTP\Header\UserAgent;

use FireHub\Adapter\HTTP\Header\Name;
use FireHub\Adapter\HTTP\Header\Exception\InvalidUserAgentProductException;
use FireHub\Runtime;

/**
 * ### Represents an HTTP User-Agent product
 *
 * Encapsulates a product name and optional version according to RFC 9110.
 * @since 1.0.0
 *
 * @extends \FireHub\Adapter\HTTP\Header\Name<non-empty-string>
 */
final readonly class Product extends Name {

    /**
     * ### Serialized product
     * @since 1.0.0
     */
    private string $serialized;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\Identifier::guard() To enforce the invariant that an identifier name cannot be empty.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the product name.
     *
     * @param non-empty-string $name <p>
     * Product name.
     * </p>
     * @param null|non-empty-string $version <p>
     * Optional product version.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If the header name is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidUserAgentProductException If the product name is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $name,
        private ?string $version = null
    ) {

        parent::__construct($name);

        if ($version !== null) {

            $this->guard(
                fn() => Runtime\Str\SB\Regex::match(
                    '/\A[!#$%&\'*+.^_`|~0-9A-Za-z-]+\z/',
                    $version
                ),
                fn() => new InvalidUserAgentProductException(
                    'Invalid User-Agent product version.'
                )
            );

        }

        $this->serialized = $version === null
            ? $name
            : "$name/$version";

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->serialized;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Data::serialize() To serialize the product name and version.
     */
    protected function comparisonValue ():string {

        return Runtime\Data::serialize([
            $this->name,
            $this->version
        ]);

    }

}