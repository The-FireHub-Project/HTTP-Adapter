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

use FireHub\Adapter\HTTP\Header\ {
    Name, Value
};
use FireHub\Runtime;

/**
 * ### Represents an immutable collection of HTTP headers
 *
 * Stores HTTP header fields using case-insensitive names while preserving their original casing and individual values.
 *
 * Every modification produces a new collection instance.
 * @since 1.0.0
 */
final readonly class Headers {

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
    public function __construct (
        private array $headers = []
    ) {}

    /**
     * ### Creates an empty header collection
     * @since 1.0.0
     *
     * @return self Empty header collection.
     */
    public static function empty ():self {

        return new self;

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
     * @return self Updated header collection.
     */
    public function add (Name $name, Value $value):self {

        $headers = $this->headers;

        $headers[] = [
            'name' => $name,
            'value' => $value
        ];

        return new self($headers);

    }

    /**
     * ### Replaces all occurrences of a header
     *
     * Removes existing occurrences of the specified header name and inserts the replacement at the position of its
     * first occurrence.
     *
     * If the header does not exist, the replacement is appended.
     *
     * <code>
     * $headers = Headers::empty()
     *     ->add(new Name('X-Custom'), new Value('first'))
     *     ->add(new Name('Content-Type'), new Value('text/plain'))
     *     ->add(new Name('X-Custom'), new Value('second'));
     *
     * // Replace all occurrences at the position of the first occurrence.
     * $updated = $headers->with(
     *     new Name('X-Custom'),
     *     new Value('updated'),
     *     new Value('additional')
     * );
     *
     * $updated->get(new Name('X-Custom'));
     *
     * // [Value('updated'), Value('additional')]
     *
     * // The original collection remains unchanged.
     * $headers->get(new Name('X-Custom'));
     *
     * // [Value('first'), Value('second')]
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::sameName() Compares header names case-insensitively.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name<non-empty-string> $name <p>
     * HTTP header name.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Value<string> $value <p>
     * Header value.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Value<string> ...$additional <p>
     * Additional header values.
     * </p>
     *
     * @return self Updated header collection.
     */
    public function with (Name $name, Value $value, Value ...$additional):self {

        $headers = []; $inserted = false;
        foreach ($this->headers as $header) {

            if ($this->sameName($header['name'], $name)) {

                if (!$inserted) {

                    foreach ([$value, ...$additional] as $replacement)
                        $headers[] = [
                            'name' => $name,
                            'value' => $replacement
                        ];

                    $inserted = true;

                }

                continue;

            }

            $headers[] = $header;

        }

        if (!$inserted) {

            foreach ([$value, ...$additional] as $replacement)
                $headers[] = [
                    'name' => $name,
                    'value' => $replacement
                ];

        }

        return new self($headers);

    }

    /**
     * ### Removes all occurrences of a header
     *
     * <code>
     * $headers = Headers::empty()
     *     ->add(new Name('Accept'), new Value('application/json'))
     *     ->add(new Name('X-Custom'), new Value('first'))
     *     ->add(new Name('X-Custom'), new Value('second'));
     *
     * // Remove all occurrences of the specified header.
     * $updated = $headers->without(new Name('x-custom'));
     *
     * $updated->has(new Name('X-Custom')); // false
     * $updated->has(new Name('Accept'));   // true
     *
     * // The original collection remains unchanged.
     * $headers->has(new Name('X-Custom')); // true
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::sameName() Compares header names case-insensitively.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name<non-empty-string> $name <p>
     * Header name.
     * </p>
     *
     * @return self Updated header collection.
     */
    public function without (Name $name):self {

        $headers = [];
        foreach ($this->headers as $header)
            if (!$this->sameName($header['name'], $name))
                $headers[] = $header;

        return new self($headers);

    }

    /**
     * ### Checks whether a header exists
     *
     * <code>
     * $headers = Headers::empty()
     *     ->add(new Name('Content-Type'), new Value('application/json'));
     *
     * // Header names are compared case-insensitively.
     * $headers->has(new Name('Content-Type')); // true
     * $headers->has(new Name('content-type')); // true
     * $headers->has(new Name('CONTENT-TYPE')); // true
     * $headers->has(new Name('Authorization')); // false
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::sameName() Compares header names case-insensitively.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name<non-empty-string> $name <p>
     * Header name.
     * </p>
     *
     * @return bool True if at least one occurrence exists.
     */
    public function has (Name $name):bool {

        foreach ($this->headers as $header)
            if ($this->sameName($header['name'], $name))
                return true;

        return false;

    }

    /**
     * ### Gets all values for a header
     *
     * Values are returned in their original order.
     *
     * <code>
     * $headers = Headers::empty()
     *     ->add(new Name('Accept'), new Value('application/json'))
     *     ->add(new Name('X-Custom'), new Value('first'))
     *     ->add(new Name('X-Custom'), new Value('second'));
     *
     * // Retrieve values in their original order.
     * $values = $headers->get(new Name('x-custom'));
     *
     * foreach ($values as $value) {
     *     echo $value->value();
     * }
     * // Outputs: firstsecond
     *
     * // Missing headers return an empty list.
     * $headers->get(new Name('Authorization')); // []
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::sameName() Compares header names case-insensitively.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name<non-empty-string> $name <p>
     * Header name.
     * </p>
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Value<string>> Matching header values.
     */
    public function get (Name $name):array {

        $values = [];
        foreach ($this->headers as $header)
            if ($this->sameName($header['name'], $name))
                $values[] = $header['value'];

        return $values;

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
    public function all ():array {

        return $this->headers;

    }

    /**
     * ### Compares HTTP header names case-insensitively
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() Converts a string to lower case.
     * @uses \FireHub\Adapter\HTTP\Header\Name::value() Gets the header name value.
     *
     * @param Name<non-empty-string> $first <p>
     * First header name.
     * </p>
     * @param Name<non-empty-string> $second <p>
     * Second header name.
     * </p>
     *
     * @return bool True if both names identify the same header.
     */
    private function sameName (Name $first, Name $second):bool {

        return Runtime\Str\SB\Casing::toLower($first->value())
            === Runtime\Str\SB\Casing::toLower($second->value());

    }

}