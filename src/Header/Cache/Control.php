<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.4
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Header\Cache;

use FireHub\Adapter\HTTP\Header\{
    Directives, Name, Parameter
};
use FireHub\Adapter\HTTP\Header\Parsing\Splitter;
use FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException;
use FireHub\Runtime;

/**
 * ### HTTP Cache-Control Header
 *
 * Represents an immutable collection of HTTP Cache-Control directives.
 *
 * Supports standard request and response cache directives, including directives with numeric values, quoted
 * field-name lists, and flags.
 *
 * Unknown extension directives are preserved as generic HTTP names or parameters.
 *
 * <code>
 * $control = new Control(
 *     new Directive\PublicCache(),
 *     new Directive\MaxAge(3600),
 *     new Directive\Immutable()
 * );
 *
 * $control->value();
 * // public, max-age=3600, immutable
 *
 * $control = Control::parse(
 *     'private, max-age=600, no-transform'
 * );
 *
 * $control->count(); // 3
 * </code>
 *
 * @since 1.0.0
 */
readonly class Control extends Directives {

    /**
     * ### Parses a Cache-Control Header
     *
     * Parses a comma-separated Cache-Control header value into a collection of typed cache directives.
     *
     * Recognized directives are converted into their corresponding specialized value objects.
     *
     * Unknown directives are preserved as generic HTTP names or quoted-string parameters.
     *
     * Directive names are case-insensitive.
     *
     * The collection preserves directive order and duplicate names.
     *
     * <code>
     * $control = Control::parse(
     *     'public, max-age=3600, immutable'
     * );
     *
     * $control->get(new Name('max-age'))
     *     instanceof Directive\MaxAge; // true
     *
     * $control->value();
     * // public, max-age=3600, immutable
     *
     * $control = Control::parse(
     *     'no-cache="Set-Cookie, Authorization", no-store'
     * );
     *
     * $control->count(); // 2
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parsing\Splitter::split() To separate comma-delimited directives while
     * respecting quoted strings.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To remove optional whitespace around directives.
     * @uses \FireHub\Runtime\Str\SB\Search::contains() To distinguish parameterized directives from flags.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize directive names.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Quoted::parse() To parse unknown parameterized directives.
     *
     * @param string $value <p>
     * Serialized Cache-Control header value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If a recognized directive contains an
     * invalid argument.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed Cache-Control directives.
     */
    public static function parse (string $value):self {

        $directives = [];

        foreach (Splitter::split($value, ',') as $segment) {

            $segment = Runtime\Str\SB\Transform::trim($segment, characters: " \t");

            if ($segment === '') continue;

            $separator = Runtime\Str\SB\Search::firstPosition('=', $segment);

            $name = $separator === false
                ? $segment
                : Runtime\Str\SB\Access::part(
                    $segment,
                    0,
                    $separator
                );

            $name = Runtime\Str\SB\Casing::toLower(
                Runtime\Str\SB\Transform::trim(
                    $name,
                    characters: " \t"
                )
            );

            $directives[] = match ($name) {
                'max-age' => Directive\MaxAge::parse($segment),
                's-maxage' => Directive\SMaxAge::parse($segment),
                'max-stale' => Directive\MaxStale::parse($segment),
                'min-fresh' => Directive\MinFresh::parse($segment),
                'stale-while-revalidate' => Directive\StaleWhileRevalidate::parse($segment),
                'stale-if-error' => Directive\StaleIfError::parse($segment),
                'no-cache' => Directive\NoCache::parse($segment),
                'private' => Directive\PrivateCache::parse($segment),
                'no-store' => self::parseFlag($segment, new Directive\NoStore()),
                'no-transform' => self::parseFlag($segment, new Directive\NoTransform()),
                'only-if-cached' => self::parseFlag($segment, new Directive\OnlyIfCached()),
                'must-revalidate' => self::parseFlag($segment, new Directive\MustRevalidate()),
                'proxy-revalidate' => self::parseFlag($segment, new Directive\ProxyRevalidate()),
                'must-understand' => self::parseFlag($segment, new Directive\MustUnderstand()),
                'public' => self::parseFlag($segment, new Directive\PublicCache()),
                'immutable' => self::parseFlag($segment, new Directive\Immutable()),
                default => Runtime\Str\SB\Search::contains('=', $segment)
                    ? Parameter\Quoted::parse($segment)
                    : new Name($segment)

            };

        }

        return new self(...$directives);

    }

    /**
     * ### Parses a Cache-Control Flag Directive
     *
     * Validates a directive that does not accept an argument.
     *
     * The serialized name is compared case-insensitively with the expected directive.
     *
     * <code>
     * $directive = self::parseFlag(
     *     'NO-STORE',
     *     new Directive\NoStore()
     * );
     *
     * $directive->value(); // no-store
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Name::equalsIgnoreCase() To validate the directive name.
     * @uses \FireHub\Runtime\Str\SB\Search::contains() To distinguish parameterized directives from flags.
     *
     * @param non-empty-string $value <p>
     * Serialized flag directive.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Name $directive <p>
     * Expected cache directive.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the directive name is invalid or
     * contains an argument.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return \FireHub\Adapter\HTTP\Header\Name Validated flag directive.
     */
    private static function parseFlag (string $value, Name $directive):Name {

        if (
            Runtime\Str\SB\Search::contains('=', $value)
            || !new Name($value)->equalsIgnoreCase($directive)
        ) throw new InvalidParameterException(
            'Invalid Cache-Control directive: ' . $value
        );

        return $directive;

    }

}