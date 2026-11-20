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
use FireHub\Adapter\HTTP\Header\ {
    Content\Encoding as ContentEncoding, Content\Language as ContentLanguage
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
     *     name: \FireHub\Adapter\HTTP\Header\Name,
     *     value: \FireHub\Adapter\HTTP\Header\Value
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
     * @uses \FireHub\Adapter\HTTP\Header\Name::equalsIgnoreCase() To compare header names.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name $name <p>
     * Header name.
     * </p>
     *
     * @return bool True if at least one occurrence exists.
     */
    final public function has (Name $name):bool {

        foreach ($this->headers as $header)
            if ($header['name']->equalsIgnoreCase($name))
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
     * @uses \FireHub\Adapter\HTTP\Header\Name::equalsIgnoreCase() To compare header names.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name $name <p>
     * Header name.
     * </p>
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Value> Matching header values.
     */
    final public function get (Name $name):array {

        $values = [];
        foreach ($this->headers as $header)
            if ($header['name']->equalsIgnoreCase($name))
                $values[] = $header['value'];

        return $values;

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
     * @param \FireHub\Adapter\HTTP\Header\Name $name <p>
     * HTTP header name.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Value $value <p>
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
     * @uses \FireHub\Adapter\HTTP\Header\Name::equalsIgnoreCase() To compare header names.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name $name <p>
     * HTTP header name.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Value $value <p>
     * Header value.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Value ...$additional <p>
     * Additional header values.
     * </p>
     *
     * @return static Updated header collection.
     */
    final public function with (Name $name, Value $value, Value ...$additional):static {

        $replacements = [$value, ...$additional];
        $headers = []; $inserted = false;
        foreach ($this->headers as $header) {

            if ($header['name']->equalsIgnoreCase($name)) {

                if (!$inserted) {

                    foreach ($replacements as $replacement)
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

        if (!$inserted)
            foreach ($replacements as $replacement)
                $headers[] = [
                    'name' => $name,
                    'value' => $replacement
                ];

        return new static($headers);

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
     * @uses \FireHub\Adapter\HTTP\Header\Name::equalsIgnoreCase() To compare header names.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name $name <p>
     * Header name.
     * </p>
     *
     * @return static Updated header collection.
     */
    final public function without (Name $name):static {

        $headers = [];
        foreach ($this->headers as $header)
            if (!$header['name']->equalsIgnoreCase($name))
                $headers[] = $header;

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
     *     name: \FireHub\Adapter\HTTP\Header\Name,
     *     value: \FireHub\Adapter\HTTP\Header\Value
     * }> Header fields.
     */
    final public function all ():array {

        return $this->headers;

    }

    /**
     * ### Sets the HTTP Content-Encoding Header
     *
     * Replaces all existing Content-Encoding header fields with the supplied collection of content codings.
     *
     * Content codings are serialized in the order in which they were applied to the representation.
     *
     * The original header collection remains unchanged.
     *
     * <code>
     * $headers = Headers::empty()->withContentEncoding(
     *     ContentEncoding::parse('gzip, br')
     * );
     *
     * $headers->getContentEncoding()?->value();
     * // gzip, br
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Content\Encoding::value() To serialize the content codings.
     * @uses self::with() To replace existing Content-Encoding header fields.
     *
     * @param \FireHub\Adapter\HTTP\Header\Content\Encoding $encoding <p>
     * Content-Encoding header containing the applied content codings.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the serialized header value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return static Updated HTTP headers.
     */
    final public function withContentEncoding (ContentEncoding $encoding):static {

        return $this->with(
            new Name('Content-Encoding'),
            new Value($encoding->value())
        );

    }

    /**
     * ### Gets the HTTP Content-Encoding Header
     *
     * Retrieves all Content-Encoding header field values and parses each field independently.
     *
     * Parsed content codings are combined into a single collection while preserving their original order.
     *
     * Returns null when the Content-Encoding header is absent.
     *
     * <code>
     * $headers = Headers::empty()
     *     ->add(new Name('Content-Encoding'), new Value('gzip'))
     *     ->add(new Name('Content-Encoding'), new Value('br'));
     *
     * $encoding = $headers->getContentEncoding();
     *
     * $encoding?->count(); // 2
     * $encoding?->value(); // gzip, br
     *
     * Headers::empty()->getContentEncoding(); // null
     * </code>
     *
     * @since 1.0.0
     *
     * @uses self::get() To retrieve Content-Encoding header fields.
     * @uses \FireHub\Adapter\HTTP\Header\Value::value() To retrieve each field value.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Encoding::parse() To parse individual header fields.
     * @uses \FireHub\Adapter\HTTP\Header\Items::all() To retrieve parsed content codings.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If a content coding is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the header contains invalid items.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Content\Encoding Parsed Content-Encoding header, or null if absent.
     */
    final public function getContentEncoding ():?ContentEncoding {

        $values = $this->get(new Name('Content-Encoding'));

        if ($values === []) return null;

        $items = [];

        foreach ($values as $value) {

            $parsed = ContentEncoding::parse($value->value());

            foreach ($parsed->all() as $item)
                $items[] = $item;

        }

        return new ContentEncoding(...$items);

    }

    /**
     * ### Sets the HTTP Content-Language Header
     *
     * Replaces all existing Content-Language header fields with the supplied collection of language tags.
     *
     * Language tags identify the intended audience of the representation rather than necessarily every language
     * appearing in its content.
     *
     * The original header collection remains unchanged.
     *
     * <code>
     * $headers = Headers::empty()->withContentLanguage(
     *     ContentLanguage::parse('hr-HR, en-US')
     * );
     *
     * $headers->getContentLanguage()?->value();
     * // hr-HR, en-US
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Content\Language::value() To serialize the language tags.
     * @uses self::with() To replace existing Content-Language header fields.
     *
     * @param \FireHub\Adapter\HTTP\Header\Content\Language $language <p>
     * Content-Language header containing the representation language tags.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the serialized header value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return static Updated HTTP headers.
     */
    final public function withContentLanguage (ContentLanguage $language):static {

        return $this->with(
            new Name('Content-Language'),
            new Value($language->value())
        );

    }

    /**
     * ### Gets the HTTP Content-Language Header
     *
     * Retrieves all Content-Language header field values and parses each field independently.
     *
     * Parsed language tags are combined into a single collection while preserving their original order.
     *
     * Returns null when the Content-Language header is absent.
     *
     * <code>
     * $headers = Headers::empty()
     *     ->add(new Name('Content-Language'), new Value('hr-HR'))
     *     ->add(new Name('Content-Language'), new Value('en-US'));
     *
     * $language = $headers->getContentLanguage();
     *
     * $language?->count(); // 2
     * $language?->value(); // hr-HR, en-US
     *
     * Headers::empty()->getContentLanguage(); // null
     * </code>
     *
     * @since 1.0.0
     *
     * @uses self::get() To retrieve Content-Language header fields.
     * @uses \FireHub\Adapter\HTTP\Header\Value::value() To retrieve each field value.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Language::parse() To parse individual header fields.
     * @uses \FireHub\Adapter\HTTP\Header\Items::all() To retrieve parsed language tags.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If a language tag is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the header contains invalid items.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Content\Language Parsed Content-Language header, or null if absent.
     */
    final public function getContentLanguage ():?ContentLanguage {

        $values = $this->get(new Name('Content-Language'));

        if ($values === []) return null;

        $items = [];

        foreach ($values as $value) {

            $parsed = ContentLanguage::parse($value->value());

            foreach ($parsed->all() as $item)
                $items[] = $item;

        }

        return new ContentLanguage(...$items);

    }

}