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

namespace FireHub\Adapter\HTTP\Header\CacheControl;

use FireHub\Adapter\HTTP\Header\Name;
use FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException;
use FireHub\Runtime;

/**
 * ### Represents a custom HTTP Cache-Control directive
 *
 * Supports extension cache directives that are not defined by the standard request and response directive enums.
 * @since 1.0.0
 *
 * @extends \FireHub\Adapter\HTTP\Header\Name<non-empty-string>
 */
final readonly class Custom extends Name implements Directive {

    /**
     * ### Serialized directive
     * @since 1.0.0
     */
    private string $serialized;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\Identifier::guard() To enforce the invariant that an identifier name cannot be empty.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the custom directive name.
     * @uses \FireHub\Runtime\Str\SB\Escape::addCSlashes() To escape the directive value.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To convert the directive name to lowercase.
     *
     * @param non-empty-string $name <p>
     * Custom directive name.
     * </p>
     * @param null|string $value <p>
     * Optional directive value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If the header name is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If the directive value is invalid or
     * the name is reserved for standard directives.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $name,
        private ?string $value = null
    ) {

        parent::__construct($name);

        $this->guard(
            fn() => Request::tryFrom(
                    Runtime\Str\SB\Casing::toLower($name)
                ) === null && Response::tryFrom(
                    Runtime\Str\SB\Casing::toLower($name)
                ) === null,
            fn() => new InvalidCacheDirectiveException(
                "Cache-Control directive '$name' is reserved for standard directives."
            )
        );

        if ($value !== null) {

            $this->guard(
                fn() => Runtime\Str\SB\Regex::match(
                    '/\A[\x20-\x7E]*\z/',
                    $value
                ),
                fn() => new InvalidCacheDirectiveException(
                    'Invalid Cache-Control directive value.'
                )
            );

        }

        if ($value === null) {

            $this->serialized = $name;

        } else if (Runtime\Str\SB\Regex::match(
            '/\A[!#$%&\'*+.^_`|~0-9A-Za-z-]+\z/',
            $value
        )) {

            $this->serialized = "$name=$value";

        } else {

            $this->serialized = $name.'="'.
                Runtime\Str\SB\Escape::addCSlashes(
                    $value,
                    "\\\""
                ).'"';

        }

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
     * @uses \FireHub\Runtime\Data::serialize() To serialize the directive name and value.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To convert the directive name to lowercase.
     */
    protected function comparisonValue ():string {

        return Runtime\Data::serialize([
            Runtime\Str\SB\Casing::toLower($this->name),
            $this->value
        ]);

    }

}