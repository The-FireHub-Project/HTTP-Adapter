<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.0
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Header\CacheControl;

use FireHub\Core\Meta\Enum\Side;
use FireHub\Core\Foundation\Constant\Numeric\IntegerLimits;
use FireHub\Adapter\HTTP\Header\Listing\Parser as ListingParser;
use FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException;
use FireHub\Runtime;

/**
 * ### Parses HTTP request Cache-Control directives
 *
 * Converts serialized request cache directives into strongly typed RequestDirective or Custom objects.
 *
 * Supports standard request directives, extension directives, quoted values, and comma-separated directive lists.
 *
 * This parser validates directive syntax without evaluating caching policies or determining cache eligibility.
 * @since 1.0.0
 */
final class RequestParser {

    /**
     * ### Parses HTTP request Cache-Control directives
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Listing\Parser::parse() To split comma-separated directives.
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\RequestParser::parseDirective() To parse each directive.
     *
     * @param string $value <p>
     * Serialized Cache-Control request header value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If a directive is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterValueException If the directive list is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If a validation condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If a validation exception is not a FireHubException.
     *
     * @return list<\FireHub\Adapter\HTTP\Header\CacheControl\RequestDirective|\FireHub\Adapter\HTTP\Header\CacheControl\Custom> Parsed request cache directives.
     */
    public static function parse (string $value):array {

        $directives = [];

        foreach (ListingParser::parse($value) as $directive)
            $directives[] = self::parseDirective($directive);

        return $directives;

    }

    /**
     * ### Parses a single HTTP request cache directive
     *
     * Recognizes standard request directives and preserves supported extension directives as Custom objects.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate directive syntax.
     * @uses \FireHub\Runtime\Str\SB\Regex::replace() To unescape quoted values.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To extract directive name and value.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize directive names.
     * @uses \FireHub\Adapter\HTTP\Header\CacheControl\RequestParser::parseSeconds() To parse numeric directive values.
     *
     * @param non-empty-string $directive <p>
     * Serialized cache directive.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If the directive is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If a validation condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If a validation exception is not a FireHubException.
     *
     * @return \FireHub\Adapter\HTTP\Header\CacheControl\RequestDirective|\FireHub\Adapter\HTTP\Header\CacheControl\Custom Parsed cache directive.
     */
    private static function parseDirective (string $directive):RequestDirective|Custom {

        if (!Runtime\Str\SB\Regex::match(
            '/\A([!#$%&\'*+.^_`|~0-9A-Za-z-]+)(?:[ \t]*=[ \t]*([!#$%&\'*+.^_`|~0-9A-Za-z-]+|"(?:[\x20-\x21\x23-\x5B\x5D-\x7E]|\\\\[\x20-\x7E])*"))?\z/',
            $directive,
            result: $matches
        )) throw new InvalidCacheDirectiveException(
            'Invalid Cache-Control directive syntax.'
        );

        /** @var non-empty-string $name */
        $name = Runtime\Str\SB\Casing::toLower($matches[1]); // @phpstan-ignore-line

        /** @var null|string $value */
        $value = $matches[2] ?? null;

        if ($value !== null && $value[0] === '"') { // @phpstan-ignore offsetAccess.notFound

            $value = Runtime\Str\SB\Access::part($value, 1, -1); // @phpstan-ignore argument.type

            $value = Runtime\Str\SB\Regex::replace(
                '/\\\\([\x20-\x7E])/',
                '$1',
                $value
            );

        }

        return match ($name) {
            'max-age' => RequestDirective::maxAge(self::parseSeconds($value)),
            'max-stale' => RequestDirective::maxStale($value === null ? null : self::parseSeconds($value)),
            'min-fresh' => RequestDirective::minFresh(self::parseSeconds($value)),
            'no-cache' => self::parseFlag($value, RequestDirective::noCache()),
            'no-store' => self::parseFlag($value, RequestDirective::noStore()),
            'no-transform' => self::parseFlag($value, RequestDirective::noTransform()),
            'only-if-cached' => self::parseFlag($value, RequestDirective::onlyIfCached()),
            default => new Custom($name, $value)
        };

    }

    /**
     * ### Parses a cache directive delta-seconds value
     *
     * Accepts non-negative decimal integers that fit within the supported PHP integer range.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate decimal digits.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To trim leading zeros.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To validate the length.
     * @uses \FireHub\Runtime\Str\SB\Compare::lexical() To validate the length.
     * @uses \FireHub\Core\Foundation\Constant\Numeric\IntegerLimits::MAX To validate the length.
     *
     * @param null|string $value <p>
     * Serialized delta-seconds value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If the value is missing, negative,
     * non-numeric, or exceeds the supported integer range.
     *
     * @return non-negative-int Parsed delta-seconds value.
     */
    private static function parseSeconds (?string $value):int {

        if (
            $value === null
            || !Runtime\Str\SB\Regex::match('/\A[0-9]+\z/', $value)
        ) throw new InvalidCacheDirectiveException(
            'Cache-Control directive requires non-negative delta-seconds.'
        );

        $normalized = Runtime\Str\SB\Transform::trim($value, Side::LEFT, '0');

        if ($normalized === '')
            return 0;

        $maximum = (string)IntegerLimits::MAX;

        if (
            Runtime\Str\SB\Inspection::length($normalized) > Runtime\Str\SB\Inspection::length($maximum)
            || (
                Runtime\Str\SB\Inspection::length($normalized) === Runtime\Str\SB\Inspection::length($maximum)
                && Runtime\Str\SB\Compare::lexical($normalized, $maximum) > 0
            )
        ) throw new InvalidCacheDirectiveException(
            'Cache-Control delta-seconds exceeds the supported integer range.'
        );

        /** @var non-negative-int */
        return (int)$normalized;

    }

    /**
     * ### Validates a valueless cache directive
     *
     * Rejects parameters on standard request directives that do not accept directive values.
     * @since 1.0.0
     *
     * @param null|string $value <p>
     * Optional serialized directive value.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\CacheControl\RequestDirective $directive <p>
     * Standard request directive.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidCacheDirectiveException If a value is provided.
     *
     * @return \FireHub\Adapter\HTTP\Header\CacheControl\RequestDirective Validated request directive.
     */
    private static function parseFlag (?string $value, RequestDirective $directive):RequestDirective {

        if ($value !== null)
            throw new InvalidCacheDirectiveException(
                'Cache-Control directive does not accept a value.'
            );

        return $directive;

    }

}