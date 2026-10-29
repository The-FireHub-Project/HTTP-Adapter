<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.1
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Header\CacheControl;

use FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException;
use FireHub\Runtime;

/**
 * ### Serializes HTTP Cache-Control directives
 *
 * Provides shared serialization for standard cache directives.
 * @since 1.0.0
 *
 * @internal
 */
final readonly class Serializer {

    /**
     * ### Serializes a directive without a value
     * @since 1.0.0
     *
     * @param non-empty-string $name <p>
     * $name Directive name.
     * </p>
     *
     * @return non-empty-string Serialized directive.
     */
    public static function flag (string $name):string {

        return $name;

    }

    /**
     * ### Serializes a numeric directive
     * @since 1.0.0
     *
     * @param non-empty-string $name <p>
     * Directive name.
     * </p>
     * @param int $seconds <p>
     * Number of seconds.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If the seconds are negative.
     *
     * @return non-empty-string Serialized directive.
     */
    public static function seconds (string $name, int $seconds):string {

        if ($seconds < 0)
            throw new InvalidCacheDirectiveException(
                'Cache-Control seconds must not be negative.'
            );

        return "$name=$seconds";

    }

    /**
     * ### Serializes a field-name directive
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the field name.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To serialize the field names.
     *
     * @param non-empty-string $name <p>
     * Directive name.
     * </p>
     * @param non-empty-list<non-empty-string> $fields <p>
     *  Response field names.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If the field name is invalid or
     * the list is empty.
     *
     * @return non-empty-string Serialized directive.
     */
    public static function fields (string $name, array $fields):string {

        if ($fields === [])
            throw new InvalidCacheDirectiveException(
                'Cache-Control field list must not be empty.'
            );

        foreach ($fields as $field) {

            if (!Runtime\Str\SB\Regex::match(
                '/\A[!#$%&\'*+.^_`|~0-9A-Za-z-]+\z/',
                $field
            )) {

                throw new InvalidCacheDirectiveException(
                    'Invalid Cache-Control field name.'
                );

            }

        }

        return $name.'="'.Runtime\Str\SB\Delimiter::implode($fields, ', ').'"';

    }

}