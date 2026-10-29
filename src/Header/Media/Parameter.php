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

namespace FireHub\Adapter\HTTP\Header\Media;

use FireHub\Adapter\HTTP\Header\Name;
use FireHub\Adapter\HTTP\Header\Exception\InvalidParameterValueException;
use FireHub\Runtime;

/**
 * ### Represents an HTTP media type parameter
 *
 * Encapsulates a parameter name and value used in media types, such as charset or boundary.
 * @since 1.0.0
 *
 * @template TValue of non-empty-string
 *
 * @extends \FireHub\Adapter\HTTP\Header\Name<TValue>
 */
final readonly class Parameter extends Name {

    /**
     * ### Serialized HTTP parameter value
     * @since 1.0.0
     *
     * @var TValue
     */
    private string $serialized;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the parameter name.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the parameter syntax.
     * @uses \FireHub\Runtime\Str\SB\Escape::addCSlashes() To escape the parameter value.
     *
     * @param non-empty-string $name <p>
     * Parameter name.
     * </p>
     * @param string $value <p>
     * Parameter value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If the header name is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidMediaTypeException If the media type is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $name,
        private string $value
    ) {

        parent::__construct($name);

        $this->guard(
            fn() => !Runtime\Str\SB\Regex::match(
                '/[^\x20-\x7E]/',
                $value
            ),
            fn() => new InvalidParameterValueException(
                'Invalid media parameter value.'
            )
        );

        /** @var TValue $serialized */
        $serialized = Runtime\Str\SB\Regex::match( // @phpstan-ignore varTag.nativeType
            '/\A[!#$%&\'*+.^_`|~0-9A-Za-z-]+\z/',
            $value
        ) ? "$name=$value"
            : "$name=\"".Runtime\Str\SB\Escape::addCSlashes($value, "\\\"")."\"";

        $this->serialized = $serialized;

    }

    /**
     * ### Gets the parameter name
     * @since 1.0.0
     *
     * @return non-empty-string Parameter name.
     */
    public function parameterName ():string {

        return $this->name;

    }

    /**
     * ### Gets the parameter value
     * @since 1.0.0
     *
     * @return string Parameter value.
     */
    public function parameterValue ():string {

        return $this->value;

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
     * @uses \FireHub\Runtime\Data::serialize() To serialize the parameter name and value.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To convert the parameter name to lowercase.
     */
    protected function comparisonValue ():string {

        return Runtime\Data::serialize([
            Runtime\Str\SB\Casing::toLower($this->name),
            $this->value
        ]);

    }

}