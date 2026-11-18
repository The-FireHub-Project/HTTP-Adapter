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

namespace FireHub\Adapter\HTTP\Message;

use FireHub\Adapter\HTTP\Header\{
    Name, Value
};

/**
 * ### Represents an immutable collection of HTTP headers
 *
 * Stores HTTP header fields using case-insensitive names while preserving their original casing and individual values.
 *
 * Every modification produces a new collection instance.
 * @since 1.0.0
 */
readonly class Headers {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param list<array{
     *     name: \FireHub\Adapter\HTTP\Header\Name<non-empty-string>,
     *     value: \FireHub\Adapter\HTTP\Header\Value<string>
     * }> $headers <p>
     * Internal header storage.
     * </p>
     *
     * @return void
     */
    final private function __construct (
        private array $headers = []
    ) {}

    /**
     * ### Creates an empty header collection
     * @since 1.0.0
     *
     * @return static Empty header collection.
     */
    final public static function empty ():static {

        return new static;

    }

    /**
     * ### Appends a header field
     *
     * Preserves all existing fields, including fields with the same name or value.
     *
     * <code>
     * $headers = Headers::empty()
     *     ->add(new Name('Accept'), new Value('application/json'))
     *     ->add(new Name('X-Custom'), new Value('first'))
     *     ->add(new Name('X-Custom'), new Value('second'));
     *
     * // All occurrences are preserved in their insertion order.
     * $headers->get(new Name('X-Custom'));
     *
     * // [Value('first'), Value('second')]
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\Name<non-empty-string> $name <p>
     * HTTP header name.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Value<string> $value <p>
     * Header value.
     * </p>
     *
     * @return static Updated header collection.
     */
    final public function add (Name $name, Value $value):static {

        $headers = $this->headers;

        $headers[] = [
            'name' => $name,
            'value' => $value
        ];

        return new static($headers);

    }

    /**
     * ### Gets all header fields in their original order
     *
     * <code>
     * $headers = Headers::empty()
     *     ->add(new Name('X-First'), new Value('1'))
     *     ->add(new Name('X-Second'), new Value('2'))
     *     ->add(new Name('x-first'), new Value('3'));
     *
     * // Iterate over all fields in their original order.
     * foreach ($headers->all() as $header) {
     *     echo $header['name']->value() . ': '
     *         . $header['value']->value() . PHP_EOL;
     * }
     *
     * // Output:
     * // X-First: 1
     * // X-Second: 2
     * // x-first: 3
     * </code>
     *
     * @since 1.0.0
     *
     * @return list<array{
     *     name: \FireHub\Adapter\HTTP\Header\Name<non-empty-string>,
     *     value: \FireHub\Adapter\HTTP\Header\Value<string>
     * }> Header fields.
     */
    final public function all ():array {

        return $this->headers;

    }

}