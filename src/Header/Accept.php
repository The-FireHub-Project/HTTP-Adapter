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

namespace FireHub\Adapter\HTTP\Header;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Accept\Preference;
use FireHub\Adapter\HTTP\Header\Parsing\Splitter;
use FireHub\Adapter\HTTP\Header\Exception\InvalidMediaTypeException;
use FireHub\Runtime;

/**
 * ### HTTP Accept Header
 *
 * Represents an immutable collection of HTTP media range preferences.
 *
 * Preferences preserve their original order and may contain duplicate media ranges.
 *
 * <code>
 * $accept = Accept::parse(
 *     'text/html, application/json;q=0.9, image/webp;q=0.8'
 * );
 *
 * $accept->count(); // 3
 *
 * $accept->value();
 * // text/html, application/json; q=0.9, image/webp; q=0.8
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<string>
 */
final readonly class Accept extends ValueObject {

    /**
     * ### HTTP Accept preferences
     * @since 1.0.0
     *
     * @var list<\FireHub\Adapter\HTTP\Header\Accept\Preference>
     */
    private array $preferences;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::values() To reindex the preference list.
     *
     * @param \FireHub\Adapter\HTTP\Header\Accept\Preference ...$preferences <p>
     * HTTP media range preferences.
     * </p>
     *
     * @return void
     */
    public function __construct (Preference ...$preferences) {

        $this->preferences = Runtime\Arr\Access::values($preferences);

    }

    /**
     * ### Parses an HTTP Accept Header
     *
     * Parses a comma-separated collection of HTTP Accept preferences.
     *
     * Commas inside quoted strings are preserved.
     *
     * <code>
     * $accept = Accept::parse(
     *     'text/html, application/json;q=0.9'
     * );
     *
     * $accept->count(); // 2
     *
     * $accept = Accept::parse(
     *     'application/json;profile="a,b";q=0.8'
     * );
     *
     * $accept->count(); // 1
     * </code>
     *
     * @since 1.0.0
     *
     * @uses self::split() To split the Accept header.
     * @uses \FireHub\Adapter\HTTP\Header\Parsing\Splitter::split() To split the Accept header.
     * @uses \FireHub\Adapter\HTTP\Header\Accept\Preference::parse() To parse individual preferences.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To trim whitespace from the preference string.
     *
     * @param string $value <p>
     * Serialized HTTP Accept header value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If a quoted string is unterminated.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidMediaTypeException If the media range is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the media parameters contain a
     * quality parameter or duplicate parameter names.s
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed Accept header.
     */
    public static function parse (string $value):self {

        if ($value === '') return new self();

        $preferences = [];

        foreach (Splitter::split($value, ',') as $part) {

            $part = Runtime\Str\SB\Transform::trim($part, characters: " \t");

            if ($part === '') continue;

            $preferences[] = Preference::parse($part);

        }

        return new self(...$preferences);

    }

    /**
     * ### Counts Accept Preferences
     *
     * <code>
     * $accept = Accept::parse('text/html, application/json');
     *
     * $accept->count(); // 2
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Inspection::count() To count the number of preferences.
     *
     * @return non-negative-int Number of preferences.
     */
    public function count ():int {

        return Runtime\Arr\Inspection::count($this->preferences);

    }

    /**
     * ### Gets All Accept Preferences
     *
     * Returns preferences in their original order.
     *
     * <code>
     * $accept = Accept::parse(
     *     'text/html, application/json;q=0.9'
     * );
     *
     * foreach ($accept->all() as $preference)
     *     echo $preference->value();
     * </code>
     *
     * @since 1.0.0
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Accept\Preference> Accept preferences.
     */
    public function all ():array {

        return $this->preferences;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $accept = Accept::parse(
     *     'text/html, application/json;q=0.9'
     * );
     *
     * $accept->value();
     * // text/html, application/json; q=0.9
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Accept\Preference::value() To serialize individual preferences.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To serialize the preferences.
     *
     * @return string Serialized Accept header value.
     */
    public function value ():string {

        $values = [];

        foreach ($this->preferences as $preference)
            $values[] = $preference->value();

        return Runtime\Str\SB\Delimiter::implode($values, ', ');

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Accept\Preference> Preferences used for equality comparison.
     */
    protected function comparisonValue ():array {

        return $this->preferences;

    }

}