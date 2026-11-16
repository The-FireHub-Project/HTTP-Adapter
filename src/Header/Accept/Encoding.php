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

namespace FireHub\Adapter\HTTP\Header\Accept;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\ {
    Accept\Encoding\Preference, Coding\Content
};
use FireHub\Runtime;

/**
 * ### HTTP Accept-Encoding Header
 *
 * Represents an immutable collection of content coding preferences in an HTTP request or response.
 *
 * Preferences preserve their original order and may contain duplicate codings. An empty collection represents an
 * explicitly empty Accept-Encoding field value.
 *
 * <code>
 * $encoding = Encoding::of(Content::GZIP, Content::BR);
 *
 * $encoding->count(); // 2
 * $encoding->value(); // gzip, br
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<string>
 */
final readonly class Encoding extends ValueObject {

    /**
     * ### HTTP Accept-Encoding Preferences
     * @since 1.0.0
     *
     * @var list<\FireHub\Adapter\HTTP\Header\Accept\Encoding\Preference>
     */
    private array $preferences;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::values() To reindex the preference list.
     *
     * @param \FireHub\Adapter\HTTP\Header\Accept\Encoding\Preference ...$preferences <p>
     * HTTP content coding preferences.
     * </p>
     *
     * @return void
     */
    public function __construct (Preference ...$preferences) {

        $this->preferences = Runtime\Arr\Access::values($preferences);

    }

    /**
     * ### Creates an HTTP Accept-Encoding Header
     *
     * Converts known content codings into preferences with an implicit quality of 1.
     *
     * <code>
     * $encoding = Encoding::of(
     *     Content::GZIP,
     *     Preference::of(Content::BR, Quality::of('0.8')),
     *     Preference::any(Quality::of('0.1'))
     * );
     *
     * $encoding->value(); // gzip, br; q=0.8, *; q=0.1
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Accept\Encoding\Preference::of() To create content coding preferences.
     *
     * @param \FireHub\Adapter\HTTP\Header\Accept\Encoding\Preference|\FireHub\Adapter\HTTP\Header\Coding\Content ...$preferences <p>
     * Content coding preferences or known content codings.
     * </p>
     *
     * @return self New HTTP Accept-Encoding header.
     */
    public static function of (Preference|Content ...$preferences):self {

        $items = [];

        foreach ($preferences as $preference)
            $items[] = $preference instanceof Preference
                ? $preference
                : Preference::of($preference);

        return new self(...$items);

    }

    /**
     * ### Parses an HTTP Accept-Encoding Header
     *
     * Parses comma-separated content coding preferences and ignores empty list members.
     *
     * <code>
     * $encoding = Encoding::parse('gzip, br;q=0.9, *;q=0');
     *
     * $encoding->value(); // gzip, br; q=0.9, *; q=0
     *
     * Encoding::parse('')->count(); // 0
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To split the field value into list members.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To remove optional whitespace.
     * @uses \FireHub\Adapter\HTTP\Header\Accept\Encoding\Preference::parse() To parse individual preferences.
     *
     * @param string $value <p>
     * Serialized HTTP Accept-Encoding field value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If a content coding is unsupported.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the quality parameter is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If a weight is invalid.
     *
     * @return self Parsed HTTP Accept-Encoding header.
     */
    public static function parse (string $value):self {

        $preferences = [];

        foreach (Runtime\Str\SB\Delimiter::explode($value, ',') as $part) {

            $part = Runtime\Str\SB\Transform::trim($part, characters: " \t");

            if ($part === '') continue;

            $preferences[] = Preference::parse($part);

        }

        return new self(...$preferences);

    }

    /**
     * ### Counts Content Coding Preferences
     *
     * <code>
     * Encoding::of(Content::GZIP, Content::BR)->count(); // 2
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Inspection::count() To count the preferences.
     *
     * @return non-negative-int Number of preferences.
     */
    public function count ():int {

        return Runtime\Arr\Inspection::count($this->preferences);

    }

    /**
     * ### Gets All Content Coding Preferences
     *
     * Returns the preferences in their original order.
     *
     * <code>
     * $preferences = Encoding::parse('gzip, br;q=0.8')->all();
     *
     * $preferences[0]->coding(); // Content::GZIP
     * $preferences[1]->weight(); // 800
     * </code>
     *
     * @since 1.0.0
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Accept\Encoding\Preference> Content coding preferences.
     */
    public function all ():array {

        return $this->preferences;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * Encoding::parse('gzip,br;q=0.8')->value(); // gzip, br; q=0.8
     * Encoding::of()->value();                 // ''
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Accept\Encoding\Preference::value() To serialize individual preferences.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join the serialized preferences.
     *
     * @return string Serialized HTTP Accept-Encoding field value.
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
     * @return list<\FireHub\Adapter\HTTP\Header\Accept\Encoding\Preference> Preferences used for equality comparison.
     */
    protected function comparisonValue ():array {

        return $this->preferences;

    }

}