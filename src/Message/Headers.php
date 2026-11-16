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
    Content\Disposition as ContentDisposition, Content\Encoding as ContentEncoding,
    Content\Language as ContentLanguage, Content\Length as ContentLength, Content\Type as ContentType,
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
     * @return static Updated header collection.
     */
    final public function with (Name $name, Value $value, Value ...$additional):static {

        $replacements = [$value, ...$additional];
        $headers = []; $inserted = false;
        foreach ($this->headers as $header) {

            if ($this->sameName($header['name'], $name)) {

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
     * @uses \FireHub\Adapter\HTTP\Message\Headers::sameName() Compares header names case-insensitively.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name<non-empty-string> $name <p>
     * Header name.
     * </p>
     *
     * @return static Updated header collection.
     */
    final public function without (Name $name):static {

        $headers = [];
        foreach ($this->headers as $header)
            if (!$this->sameName($header['name'], $name))
                $headers[] = $header;

        return new static($headers);

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
    final public function has (Name $name):bool {

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
    final public function get (Name $name):array {

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
    final public function all ():array {

        return $this->headers;

    }

    /**
     * ### Sets the HTTP Content-Encoding Header
     *
     * Replaces the Content-Encoding header with the provided content codings.
     *
     * <code>
     * $headers = Headers::empty()->withContentEncoding(
     *     Encoding::of(Content::GZIP)
     * );
     *
     * $headers->getContentEncoding()?->value(); // gzip
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Content\Encoding::value() To serialize the content codings.
     * @uses \FireHub\Adapter\HTTP\Message\Headers::with() To replace the Content-Encoding header.
     *
     * @param \FireHub\Adapter\HTTP\Header\Content\Encoding $encoding <p>
     * Content codings applied to the representation.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If the header name is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the header value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return static New headers instance with the updated Content-Encoding header.
     */
    public function withContentEncoding (ContentEncoding $encoding):static {

        return $this->with(
            new Name('Content-Encoding'),
            new Value($encoding->value())
        );

    }

    /**
     * ### Sets the HTTP Content-Language Header
     *
     * Replaces all existing Content-Language header fields with the provided language tags.
     *
     * <code>
     * $headers = Headers::empty()->withContentLanguage(
     *     ContentLanguage::of(
     *         Tag::of('hr-HR'),
     *         Tag::of('en-US')
     *     )
     * );
     *
     * $headers->getContentLanguage()?->value(); // hr-HR, en-US
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Content\Language::value() To serialize language tags.
     * @uses \FireHub\Adapter\HTTP\Message\Headers::with() To replace the Content-Language header.
     *
     * @param \FireHub\Adapter\HTTP\Header\Content\Language $language <p>
     * Content language tags.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If the header name is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the header value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return static Updated headers collection.
     */
    public function withContentLanguage (ContentLanguage $language):static {

        return $this->with(
            new Name('Content-Language'),
            new Value($language->value())
        );

    }

    /**
     * ### Sets the HTTP Content-Disposition Header
     *
     * Replaces all existing Content-Disposition header fields with the provided content disposition.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Header\Content\Disposition;
     * use FireHub\Adapter\HTTP\Header\Disposition\KnownType;
     * use FireHub\Adapter\HTTP\Header\Disposition\Parameter\Filename;
     *
     * $headers = Headers::empty()->withContentDisposition(
     *     Disposition::known(
     *         KnownType::ATTACHMENT,
     *         Filename::of('report.pdf')
     *     )
     * );
     *
     * $headers->getContentDisposition()?->value();
     * // attachment; filename=report.pdf
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
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If the header name is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the header value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return static Updated headers collection.
     */
    public function withContentDisposition (ContentDisposition $disposition):static {

        return $this->with(
            new Name('Content-Disposition'),
            new Value($disposition->value())
        );

    }

    /**
     * ### Sets the HTTP Content-Type Header
     *
     * Replaces all existing Content-Type header fields with the provided media type and its parameters.
     *
     * <code>
     * $headers = Headers::empty()->withContentType(
     *     ContentType::known(KnownMediaType::APPLICATION_JSON)
     * );
     *
     * $headers->getContentType()?->value();
     * // application/json
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\Content\Type $type <p>
     * Content type and its parameters.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If the header name is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the header value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return static Updated headers collection.
     */
    public function withContentType (ContentType $type):static {

        return $this->with(
            new Name('Content-Type'),
            new Value($type->value())
        );

    }

    /**
     * ### Sets the HTTP Content-Length Header
     *
     * Replaces all existing Content-Length header fields with the provided content length.
     *
     * Content-Length specifies the number of octets in the HTTP message content.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Header\Content\Length;
     *
     * $headers = Headers::empty()->withContentLength(
     *     new Length(1024)
     * );
     *
     * $headers->getContentLength()?->bytes();
     * // 1024
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
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If the header name is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the header value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return static Updated headers collection.
     */
    public function withContentLength (ContentLength $length):static {

        return $this->with(
            new Name('Content-Length'),
            new Value($length->value())
        );

    }

    /**
     * ### Gets the HTTP Content-Encoding Header
     *
     * Retrieves and parses all Content-Encoding header fields into a single ordered collection of content codings.
     *
     * Multiple Content-Encoding fields are combined while preserving their original order.
     *
     * Returns null when the header is absent.
     *
     * <code>
     * $headers = Headers::empty()->withContentEncoding(
     *     Encoding::of(
     *         Content::GZIP,
     *         Content::BR
     *     )
     * );
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
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() To retrieve Content-Encoding header values.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Encoding::parse() To parse each header field.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Encoding::all() To retrieve parsed codings.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If a content coding is unsupported.
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Content\Encoding Parsed Content-Encoding header, or null if absent.
     */
    public function getContentEncoding ():?ContentEncoding {

        $values = $this->get(new Name('Content-Encoding'));

        if ($values === []) return null;

        $codings = [];

        foreach ($values as $value) {

            $encoding = ContentEncoding::parse($value->value());

            foreach ($encoding->all() as $coding)
                $codings[] = $coding;

        }

        return new ContentEncoding(...$codings);

    }

    /**
     * ### Gets the HTTP Content-Language Header
     *
     * Retrieves and parses all Content-Language header fields into a single collection of language tags.
     *
     * Multiple fields are combined while preserving their original order.
     *
     * Returns null when the header is absent.
     *
     * <code>
     * $headers = Headers::empty()->withContentLanguage(
     *     ContentLanguage::of(
     *         Tag::of('hr-HR'),
     *         Tag::of('en-US')
     *     )
     * );
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
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() To retrieve Content-Language fields.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Language::parse() To parse individual field values.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Language::all() To retrieve parsed language tags.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If the header name is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If a language tag is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Content\Language Parsed Content-Language header, or null if absent.
     */
    public function getContentLanguage ():?ContentLanguage {

        $values = $this->get(new Name('Content-Language'));

        if ($values === []) return null;

        $tags = [];

        foreach ($values as $value) {

            $language = ContentLanguage::parse($value->value());

            foreach ($language->all() as $tag)
                $tags[] = $tag;

        }

        return new ContentLanguage(...$tags);

    }

    /**
     * ### Gets the HTTP Content-Type Header
     *
     * Returns the parsed Content-Type header or null when absent.
     *
     * <code>
     * $headers = Headers::empty()->withContentType(
     *     ContentType::known(KnownMediaType::TEXT_HTML)
     * );
     *
     * $headers->getContentType()?->mediaType()->value();
     * // text/html
     *
     * $headers = Headers::empty();
     *
     * $headers->getContentType(); // null
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() To retrieve Content-Type header values.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Type::parse() To parse the header value.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Content\Type Parsed content type.
     */
    public function getContentType ():?ContentType {

        $values = $this->get(new Name('Content-Type'));

        if ($values === []) return null;

        /** @var non-empty-string $value */
        $value = $values[0]->value();

        return ContentType::parse($value);

    }

    /**
     * ### Gets the HTTP Content-Disposition Header
     *
     * Returns the parsed Content-Disposition header field value.
     *
     * Returns null when the header is absent.
     *
     * Multiple Content-Disposition header fields are rejected because Content-Disposition does not define a
     * comma-separated list of values.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Header\Content\Disposition;
     * use FireHub\Adapter\HTTP\Header\Disposition\KnownType;
     *
     * $headers = Headers::empty()->withContentDisposition(
     *     Disposition::known(KnownType::ATTACHMENT)
     * );
     *
     * $headers->getContentDisposition()?->type()->value();
     * // attachment
     *
     * $headers = Headers::empty();
     *
     * $headers->getContentDisposition();
     * // null
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() To retrieve the header field values.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Disposition::parse() To parse the header value.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the disposition type is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Content\Disposition Parsed disposition or null if absent.
     */
    public function getContentDisposition ():?ContentDisposition {

        $values = $this->get(new Name('Content-Disposition'));

        if ($values === []) return null;

        /** @var non-empty-string $value */
        $value = $values[0]->value();

        return ContentDisposition::parse($value);

    }

    /**
     * ### Gets the HTTP Content-Length Header
     *
     * Returns the parsed Content-Length header field value.
     *
     * Returns null when the header is absent.
     *
     * This strict behavior prevents ambiguous message framing.
     *
     * <code>
     * use FireHub\Adapter\HTTP\Header\Content\Length;
     *
     * $headers = Headers::empty()->withContentLength(
     *     new Length(1024)
     * );
     *
     * $headers->getContentLength()?->bytes();
     * // 1024
     *
     * $headers = Headers::empty();
     *
     * $headers->getContentLength();
     * // null
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Message\Headers::get() To retrieve the header field values.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Length::parse() To parse the content length.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the content length is negative or
     * contains invalid characters.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return null|\FireHub\Adapter\HTTP\Header\Content\Length Parsed content length or null if absent.
     */
    public function getContentLength ():?ContentLength {

        $values = $this->get(new Name('Content-Length'));

        if ($values === []) return null;

        /** @var non-empty-string $value */
        $value = $values[0]->value();

        return ContentLength::parse($value);

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