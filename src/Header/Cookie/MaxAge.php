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

namespace FireHub\Adapter\HTTP\Header\Cookie;

use FireHub\Adapter\HTTP\Header\ {
    Name, Value, Parameter
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException;
use FireHub\Runtime;

/**
 * ### HTTP Cookie Max-Age Attribute
 *
 * Represents the Max-Age attribute of an HTTP Set-Cookie header.
 *
 * The Max-Age attribute specifies the cookie lifetime in seconds.
 *
 * Positive values define the cookie lifetime, while zero and negative values indicate that the cookie should expire
 * immediately.
 *
 * <code>
 * $maxAge = new MaxAge(3600);
 *
 * $maxAge->name()->value();    // Max-Age
 * $maxAge->content()->value(); // 3600
 * $maxAge->value();            // Max-Age=3600
 * $maxAge->seconds();          // 3600
 * $maxAge->isExpired();        // false
 *
 * $maxAge = MaxAge::parse('Max-Age=0');
 *
 * $maxAge->isExpired(); // true
 * </code>
 *
 * @since 1.0.0
 */
final readonly class MaxAge extends Parameter {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param int $seconds <p>
     * Cookie lifetime in seconds
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private int $seconds
    ) {

        parent::__construct(
            new Name('Max-Age'),
            new Value((string)$seconds)
        );

    }

    /**
     * ### Parses an HTTP Cookie Max-Age Attribute
     *
     * Parses a serialized Max-Age attribute and validates its parameter name and integer value.
     *
     * <code>
     * $maxAge = MaxAge::parse('Max-Age=3600');
     *
     * $maxAge->seconds(); // 3600
     * $maxAge->value();   // Max-Age=3600
     *
     * $maxAge = MaxAge::parse('max-age=-1');
     *
     * $maxAge->seconds();   // -1
     * $maxAge->isExpired(); // true
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::parse() To parse the generic HTTP parameter.
     * @uses \FireHub\Adapter\HTTP\Header\Name::equalsIgnoreCase() To compare the parameter name.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the integer representation.
     * @uses \FireHub\Runtime\Filter::value() To convert the integer representation.
     *
     * @param non-empty-string $value <p>
     * Serialized HTTP cookie Max-Age attribute.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter name or value is
     * invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed HTTP cookie Max-Age attribute.
     */
    public static function parse (string $value):self {

        $parameter = parent::parse($value);

        if (!$parameter->name()->equalsIgnoreCase(new Name('Max-Age')))
            throw new InvalidParameterException(
                'Expected an HTTP cookie attribute named "Max-Age".'
            );

        $seconds = $parameter->content()->value();

        if (!Runtime\Str\SB\Regex::match('/\A-?[0-9]+\z/', $seconds))
            throw new InvalidParameterException('HTTP cookie Max-Age must be a valid integer.');

        /** @var int|false $integer */
        $integer = Runtime\Filter::value($seconds, FILTER_VALIDATE_INT);

        if ($integer === false)
            throw new InvalidParameterException(
                'HTTP cookie Max-Age exceeds the supported integer range.'
            );

        return new self($integer);

    }

    /**
     * ### Checks Whether the Cookie Should Expire Immediately
     *
     * Returns true when Max-Age is zero or negative.
     *
     * <code>
     * (new MaxAge(3600))->isExpired(); // false
     * (new MaxAge(0))->isExpired();    // true
     * (new MaxAge(-1))->isExpired();   // true
     * </code>
     *
     * @since 1.0.0
     *
     * @return bool True if the cookie should expire immediately.
     */
    public function isExpired ():bool {

        return $this->seconds <= 0;

    }

    /**
     * ### Gets the Cookie Lifetime
     *
     * Returns the cookie lifetime in seconds.
     *
     * <code>
     * $maxAge = new MaxAge(3600);
     *
     * $maxAge->seconds(); // 3600
     * </code>
     *
     * @since 1.0.0
     *
     * @return int Cookie lifetime in seconds.
     */
    public function seconds ():int {

        return $this->seconds;

    }

}