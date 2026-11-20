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

namespace FireHub\Adapter\HTTP\Header\Content;

use FireHub\Adapter\HTTP\Header\Media\MediaType;
use FireHub\Adapter\HTTP\Header\{
    Attributes, Item, Parameter
};
use FireHub\Adapter\HTTP\Header\Parameter\{
    Boundary, Charset, Quoted
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException;
use FireHub\Runtime;

/**
 * ### HTTP Content-Type Header
 *
 * Represents the media type of HTTP representation and its optional parameters.
 *
 * The primary content must be a valid MIME media type.
 *
 * Parameters are unique by case-insensitive name. The charset and boundary parameters are represented by
 * specialized value objects.
 *
 * <code>
 * $type = Type::parse('text/html; charset=UTF-8');
 *
 * $type->content()->value(); // text/html
 * $type->value();            // text/html; charset=utf-8
 *
 * $type = Type::parse(
 *     'multipart/form-data; boundary="FireHub:123"'
 * );
 *
 * $type->getParameter(new Name('boundary')) instanceof Boundary; // true
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Type extends Item {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Charset::parse() To parse charset parameters.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Boundary::parse() To parse boundary parameters.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize parameter names.
     *
     * @param \FireHub\Adapter\HTTP\Header\Media\MediaType $media_type <p>
     * Representation media type.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Attributes $parameters <p>
     * Optional media type parameters.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If a parameter is invalid or duplicated.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (MediaType $media_type, Attributes $parameters = new Attributes) {

        $normalized = []; $names = [];
        foreach ($parameters->all() as $attribute) {

            if (!$attribute instanceof Parameter)
                throw new InvalidParameterException(
                    'Content-Type attributes must be name-value parameters.'
                );

            $name = Runtime\Str\SB\Casing::toLower(
                $attribute->name()->value()
            );

            if (isset($names[$name]))
                throw new InvalidParameterException(
                    'Duplicate Content-Type parameter: ' . $name
                );

            $names[$name] = true;

            $normalized[] = match ($name) {
                'charset' => Charset::parse($attribute->value()),
                'boundary' => Boundary::parse($attribute->value()),
                default => $attribute
            };

        }

        parent::__construct(
            $media_type,
            new Attributes(...$normalized)
        );

    }

    /**
     * ### Parses an HTTP Content-Type Header
     *
     * Parses a media type and its optional parameters.
     *
     * Known parameters are converted to specialized value objects.
     *
     * <code>
     * $type = Type::parse('application/json');
     *
     * $type->content()->value(); // application/json
     *
     * $type = Type::parse('text/html; charset="UTF-8"');
     *
     * $type->value(); // text/html; charset=utf-8
     *
     * $type = Type::parse(
     *     'multipart/form-data; boundary="FireHub:123"'
     * );
     *
     * $type->getParameter(new Name('boundary')) instanceof Boundary; // true
     * </code>
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Quoted::parse() To parse quoted parameters.
     *
     * @since 1.0.0
     *
     * @param non-empty-string $value <p>
     * Serialized Content-Type header value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the header parameters are invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the media type or quoted syntax is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed Content-Type header.
     */
    public static function parse (string $value):self {

        $item = parent::parse($value);

        $parameters = [];

        foreach ($item->attributes()->all() as $attribute) {

            if (!$attribute instanceof Parameter)
                throw new InvalidParameterException(
                    'Content-Type attributes must be name-value parameters.'
                );

            $parameters[] = Quoted::parse($attribute->value());

        }

        /** @var non-empty-string $value */
        $value = $item->content()->value();

        return new self(
            MediaType::parse($value),
            new Attributes(...$parameters)
        );

    }

}