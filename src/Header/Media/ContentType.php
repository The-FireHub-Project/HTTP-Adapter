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

use FireHub\Core\Type\ValueObject;
use FireHub\Runtime;

/**
 * ### Represents an HTTP Content-Type value
 *
 * Encapsulates a concrete media type and its optional parameters.
 * @since 1.0.0
 *
 * @template TValue of non-empty-string
 *
 * @extends \FireHub\Core\Type\ValueObject<TValue>
 */
final readonly class ContentType extends ValueObject {

    /**
     * ### Media type parameters
     * @since 1.0.0
     *
     * @var list<\FireHub\Adapter\HTTP\Header\Media\Parameter<non-empty-string>>
     */
    private array $parameters;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::values() To remove the keys from the parameters array.
     *
     * @param \FireHub\Adapter\HTTP\Header\Media\Type|\FireHub\Adapter\HTTP\Header\Media\Custom<non-empty-string> $media <p>
     * Concrete media type.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Media\Parameter<non-empty-string> ...$parameters <p>
     * Optional media type parameters.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Type|Custom $media,
        Parameter ...$parameters
    ) {

        $this->parameters = Runtime\Arr\Access::values($parameters);

    }

    /**
     * ### Gets the media type
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Media\Type|\FireHub\Adapter\HTTP\Header\Media\Custom<non-empty-string>
     * Media type.
     */
    public function media ():Type|Custom {

        return $this->media;

    }

    /**
     * ### Gets the media type parameters
     * @since 1.0.0
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Media\Parameter<non-empty-string>> Media type parameters.
     */
    public function parameters ():array {

        return $this->parameters;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Media\Parameter::value() To get the parameter value.
     * @uses \FireHub\Adapter\HTTP\Header\Media\Custom::value() To get the custom media type.
     */
    public function value ():string {

        $media = $this->media instanceof Custom
            ? $this->media->value()
            : $this->media->value;

        foreach ($this->parameters as $parameter) {
            $media .= '; '.$parameter->value();
        }

        /** @var TValue */
        return $media;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To convert the media type to lower case.
     * @uses \FireHub\Runtime\Arr\Transform::map() To map the parameters to lower case.
     * @uses \FireHub\Adapter\HTTP\Header\Media\Parameter::parameterName() To get the parameter name.
     * @uses \FireHub\Adapter\HTTP\Header\Media\Parameter::parameterValue() To get the parameter value.
     * @uses \FireHub\Adapter\HTTP\Header\Media\Custom::value() To get the custom media type.
     *
     * @return array{string, list<array{lowercase-string, string}>} Comparison value.
     */
    protected function comparisonValue ():array {

        $media = $this->media instanceof Custom
            ? $this->media->value()
            : $this->media->value;

        return [
            Runtime\Str\SB\Casing::toLower($media),
            Runtime\Arr\Transform::map(
                $this->parameters,
                static fn(Parameter $parameter):array => [
                    Runtime\Str\SB\Casing::toLower($parameter->parameterName()),
                    $parameter->parameterValue()
                ]
            )
        ];

    }

}