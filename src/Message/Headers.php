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
    Cache\Control as CacheControl,
    Content\Disposition as ContentDisposition, Content\Encoding as ContentEncoding, Content\Language as ContentLanguage,
    Content\Length as ContentLength, Content\Type as ContentType,
    Name, Value
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;
use FireHub\Runtime;

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

    /**
     * ### Sets the HTTP Content-Type Header
     *
     * Replaces all existing Content-Type header fields with the supplied media type and its parameters.
     *
     * The original header collection remains unchanged.
     *
     * <code>
     * $headers = Headers::empty()->withContentType(
     *     ContentType::parse('text/html; charset=UTF-8')
     * );
     *
     * $headers->getContentType()?->value();
     * // text/html; charset=utf-8
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Content\Type::value() To serialize the Content-Type header.
     * @uses self::with() To replace existing Content-Type header fields.
     *
     * @param \FireHub\Adapter\HTTP\Header\Content\Type $type <p>
     * Content-Type header containing the media type and its parameters.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the serialized header value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return static Updated HTTP headers.
     */
    final public function withContentType (ContentType $type):static {

        return $this->with(
            new Name('Content-Type'),
            new Value($type->value())
        );

    }

    /**
     * ### Gets the HTTP Content-Type Header
     *
     * Retrieves and parses the Content-Type header.
     *
     * Unlike list-based HTTP fields, Content-Type represents a single media type and cannot be combined with
     * multiple field values.
     *
     * Returns null when the header is absent.
     *
     * Throws an exception when multiple Content-Type fields are present.
     *
     * <code>
     * $headers = Headers::empty()
     *     ->add(
     *         new Name('Content-Type'),
     *         new Value('application/json; charset=UTF-8')
     *     );
     *
     * $type = $headers->getContentType();
     *
     * $type?->content()->value(); // application/json
     * $type?->value();            // application/json; charset=utf-8
     *
     * Headers::empty()->getContentType(); // null
     * </code>
     *
     * @since 1.0.0
     *
     * @uses self::get() To retrieve Content-Type header fields.
     * @uses \FireHub\Adapter\HTTP\Header\Value::value() To retrieve the serialized field value.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Type::parse() To parse the Content-Type header.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To check for multiple Content-Type fields.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If multiple Content-Type fields are present or the media type is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If a Content-Type parameter is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Content\Type Parsed Content-Type header, or null if absent.
     */
    final public function getContentType ():?ContentType {

        $values = $this->get(new Name('Content-Type'));

        if ($values === []) return null;

        if (Runtime\Arr\Inspection::count($values) !== 1)
            throw new InvalidFieldValueException(
                'Content-Type must contain exactly one header field.'
            );

        /** @var non-empty-string $value */
        $value = $values[0]->value();

        return ContentType::parse($value);

    }

    /**
     * ### Sets the Content-Disposition Header
     *
     * Returns a new headers collection with the Content-Disposition field set to the specified value.
     *
     * Any existing Content-Disposition values are replaced.
     *
     * <code>
     * $headers = $headers->withContentDisposition(
     *     new ContentDisposition(
     *         Type::attachment(),
     *         new Attributes(
     *             new Filename('report.pdf')
     *         )
     *     )
     * );
     *
     * $headers->getContentDisposition()?->type()->value();
     * // attachment
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::with() To replace the header field.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Disposition::value() To serialize the disposition.
     *
     * @param \FireHub\Adapter\HTTP\Header\Content\Disposition $disposition <p>
     * Content-Disposition header value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the serialized header value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return static Updated headers collection.
     */
    final public function withContentDisposition (ContentDisposition $disposition):static {

        return $this->with(
            new Name('Content-Disposition'),
            new Value($disposition->value())
        );

    }

    /**
     * ### Gets the Content-Disposition Header
     *
     * Returns the parsed Content-Disposition header value.
     *
     * Returns null when the header is absent.
     *
     * Content-Disposition must contain exactly one header field value. Multiple field values are rejected instead of
     * being combined.
     *
     * <code>
     * $disposition = $headers->getContentDisposition();
     *
     * if ($disposition !== null) {
     *
     *     $disposition->type()->value(); // attachment
     *
     *     $filename = $disposition->getParameter(
     *         new Name('filename')
     *     );
     *
     * }
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() To retrieve the header values.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Disposition::parse() To parse the header.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To check for multiple Content-Disposition fields.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If multiple Content-Disposition
     * fields exist or the value is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the disposition parameters are invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Content\Disposition Parsed Content-Disposition header or null.
     */
    final public function getContentDisposition ():?ContentDisposition {

        $values = $this->get(new Name('Content-Disposition'));

        if ($values === [])
            return null;

        if (Runtime\Arr\Inspection::count($values) !== 1)
            throw new InvalidFieldValueException(
                'Content-Disposition must contain exactly one header field.'
            );

        /** @var non-empty-string $value */
        $value = $values[0]->value();

        return ContentDisposition::parse($value);

    }

    /**
     * ### Sets the Content-Length Header
     *
     * Returns a new headers collection with the Content-Length field set to the specified byte count.
     *
     * Existing Content-Length values are replaced.
     *
     * <code>
     * $headers = $headers->withContentLength(
     *     new ContentLength(1024)
     * );
     *
     * $headers->getContentLength()?->bytes(); // 1024
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::with() To replace the header field.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Length::value() To serialize the content length.
     *
     * @param \FireHub\Adapter\HTTP\Header\Content\Length $length <p>
     * Content length in bytes.
     * </p>
     *
     * @return static Updated headers collection.
     */
    final public function withContentLength (ContentLength $length):static {

        return $this->with(
            new Name('Content-Length'),
            new Value($length->value())
        );

    }

    /**
     * ### Gets the Content-Length Header
     *
     * Returns the parsed Content-Length header value, or null if absent.
     *
     * Multiple Content-Length field values are rejected to avoid ambiguous message framing.
     *
     * <code>
     * $length = $headers->getContentLength();
     *
     * $length?->bytes(); // 1024
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() To retrieve the header values.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Length::parse() To parse the content length.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To check for multiple Content-Length fields.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the header is invalid or ambiguous.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Content\Length Parsed content length or null.
     */
    final public function getContentLength ():?ContentLength {

        $values = $this->get(new Name('Content-Length'));

        if ($values === [])
            return null;

        if (Runtime\Arr\Inspection::count($values) !== 1)
            throw new InvalidFieldValueException(
                'Content-Length must contain exactly one header field value.'
            );

        return ContentLength::parse($values[0]->value());

    }

    /**
     * ### Sets the HTTP Cache-Control Header
     *
     * Replaces all existing Cache-Control header fields with the supplied collection of cache directives.
     *
     * The supplied Control instance is serialized before storage.
     *
     * <code>
     * $headers = Headers::empty()->withCacheControl(
     *     new Control(
     *         new Directive\PublicCache(),
     *         new Directive\MaxAge(3600)
     *     )
     * );
     *
     * $headers->getCacheControl()?->value();
     * // public, max-age=3600
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Cache\Control::value() To serialize the Cache-Control directives.
     * @uses \FireHub\Adapter\HTTP\Message\Headers::with() To replace existing header fields.
     *
     * @param \FireHub\Adapter\HTTP\Header\Cache\Control $control <p>
     * Cache-Control directives to store.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the serialized header value is
     * invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return static Updated HTTP headers.
     */
    public function withCacheControl (CacheControl $control):static {

        return $this->with(
            new Name('Cache-Control'),
            new Value($control->value())
        );

    }

    /**
     * ### Gets the HTTP Cache-Control Header
     *
     * Retrieves all Cache-Control header field values and parses each field independently.
     *
     * Parsed directives are combined into a single Control collection while preserving their original order and
     * duplicate directives.
     *
     * Returns null when the Cache-Control header is absent.
     *
     * <code>
     * $headers = Headers::empty()
     *     ->add(new Name('Cache-Control'), new Value('public'))
     *     ->add(new Name('Cache-Control'), new Value('max-age=3600'));
     *
     * $control = $headers->getCacheControl();
     *
     * $control?->count(); // 2
     * $control?->value(); // public, max-age=3600
     *
     * Headers::empty()->getCacheControl(); // null
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() To retrieve Cache-Control header fields.
     * @uses \FireHub\Adapter\HTTP\Header\Value::value() To retrieve serialized field values.
     * @uses \FireHub\Adapter\HTTP\Header\Cache\Control::parse() To parse each Cache-Control header field.
     * @uses \FireHub\Adapter\HTTP\Header\Collection::all() To retrieve parsed cache directives.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If a recognized cache directive is
     * invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the header value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Cache\Control Parsed Cache-Control directives, or null if absent.
     */
    public function getCacheControl ():?CacheControl {

        $values = $this->get(new Name('Cache-Control'));

        if ($values === []) return null;

        $directives = [];

        foreach ($values as $value) {

            $parsed = CacheControl::parse($value->value());

            foreach ($parsed->all() as $directive)
                $directives[] = $directive;

        }

        return new CacheControl(...$directives);

    }

}