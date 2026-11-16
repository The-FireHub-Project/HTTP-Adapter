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
use FireHub\Adapter\HTTP\Header\Accept\Language\ {
    Preference, Range
};
use FireHub\Adapter\HTTP\Header\Language\Tag;
use FireHub\Runtime;

/**
 * ### HTTP Accept-Language Header
 *
 * Represents an immutable collection of HTTP language preferences.
 *
 * Preferences preserve their original order and may contain duplicate language ranges.
 *
 * <code>
 * $language = Language::of(
 *     Range::of('hr-HR'),
 *     Range::of('en-US')
 * );
 *
 * $language->count(); // 2
 * $language->value(); // hr-HR, en-US
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<string>
 */
final readonly class Language extends ValueObject {

    /**
     * ### HTTP Language Preferences
     * @since 1.0.0
     *
     * @var list<\FireHub\Adapter\HTTP\Header\Accept\Language\Preference>
     */
    private array $preferences;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::values() To reindex the preference list.
     *
     * @param \FireHub\Adapter\HTTP\Header\Accept\Language\Preference ...$preferences <p>
     * HTTP language preferences.
     * </p>
     *
     * @return void
     */
    public function __construct (Preference ...$preferences) {

        $this->preferences = Runtime\Arr\Access::values($preferences);

    }

    /**
     * ### Creates an HTTP Accept-Language Header
     *
     * Creates an immutable collection of language preferences from language tags, basic language ranges, or explicit
     * preferences.
     *
     * Language tags and ranges are automatically converted into preferences with an implicit quality of 1.
     *
     * <code>
     * $language = Language::of(
     *     Tag::of('hr-HR'),
     *     Preference::of(
     *         Tag::of('en-US'),
     *         Quality::of('0.8')
     *     ),
     *     Preference::any(
     *         Quality::of('0.1')
     *     )
     * );
     *
     * $language->count(); // 3
     *
     * $language->value();
     * // hr-HR, en-US; q=0.8, *; q=0.1
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Accept\Language\Preference::of() To create language preferences.
     *
     * @param \FireHub\Adapter\HTTP\Header\Accept\Language\Preference|\FireHub\Adapter\HTTP\Header\Accept\Language\Range|\FireHub\Adapter\HTTP\Header\Language\Tag ...$preferences <p>
     * Language preferences, ranges, or tags.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the language range is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self New HTTP Accept-Language header.
     */
    public static function of (Preference|Range|Tag ...$preferences):self {

        $items = [];

        foreach ($preferences as $preference)
            $items[] = $preference instanceof Preference
                ? $preference
                : Preference::of($preference);

        return new self(...$items);

    }

    /**
     * ### Parses an HTTP Accept-Language Header
     *
     * Parses comma-separated language preferences.
     *
     * Empty list members are ignored.
     *
     * <code>
     * $language = Language::parse(
     *     'hr-HR, hr;q=0.9, en-US;q=0.8'
     * );
     *
     * $language->count(); // 3
     *
     * $language->value();
     * // hr-HR, hr; q=0.9, en-US; q=0.8
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To split the header value.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To remove optional whitespace.
     * @uses \FireHub\Adapter\HTTP\Header\Accept\Language\Preference::parse() To parse language preferences.
     *
     * @param string $value <p>
     * Serialized HTTP Accept-Language header value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If a language range is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If a quality parameter is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed HTTP Accept-Language header.
     */
    public static function parse (string $value):self {

        $preferences = [];

        foreach (Runtime\Str\SB\Delimiter::explode($value, ',') as $part) {

            $part = Runtime\Str\SB\Transform::trim(
                $part,
                characters: " \t"
            );

            if ($part === '') continue;

            $preferences[] = Preference::parse($part);

        }

        return new self(...$preferences);

    }

    /**
     * ### Counts Language Preferences
     *
     * <code>
     * Language::of(
     *     Range::of('hr'),
     *     Range::of('en')
     * )->count(); // 2
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Inspection::count() To count the preferences.
     *
     * @return non-negative-int Number of language preferences.
     */
    public function count ():int {

        return Runtime\Arr\Inspection::count($this->preferences);

    }

    /**
     * ### Gets All Language Preferences
     *
     * Returns preferences in their original order.
     *
     * <code>
     * $preferences = Language::of(
     *     Range::of('hr-HR'),
     *     Range::of('en-US')
     * )->all();
     *
     * $preferences[0]->range()->value(); // hr-HR
     * $preferences[1]->range()->value(); // en-US
     * </code>
     *
     * @since 1.0.0
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Accept\Language\Preference> Language preferences.
     */
    public function all ():array {

        return $this->preferences;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * Language::of(
     *     Range::of('hr-HR'),
     *     Range::of('en-US')
     * )->value(); // hr-HR, en-US
     *
     * Language::of()->value(); // ''
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Accept\Language\Preference::value() To serialize language preferences.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join serialized preferences.
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
     * @return list<\FireHub\Adapter\HTTP\Header\Accept\Language\Preference> Preferences used for equality comparison.
     */
    protected function comparisonValue ():array {

        return $this->preferences;

    }

}