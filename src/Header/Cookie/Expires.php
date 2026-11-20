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

use FireHub\Core\Type\Date\Zone;
use FireHub\Adapter\HTTP\Header\{
    Name, Parameter, Value
};
use FireHub\Foundation\Temporal\{
    DateTime, NamedTimezone
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException;

/**
 * ### HTTP Cookie Expires Attribute
 *
 * Represents the Expires attribute of an HTTP Set-Cookie header.
 *
 * The expiration date is serialized using the HTTP-date format in GMT.
 *
 * The original Foundation DateTime object is preserved, including its timezone.
 *
 * <code>
 * $expires = new Expires(
 *     DateTime::from('2030-01-01 12:00:00 UTC')
 * );
 *
 * $expires->value();
 * // Expires=Tue, 01 Jan 2030 12:00:00 GMT
 *
 * $expires->content()->value();
 * // Tue, 01 Jan 2030 12:00:00 GMT
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Expires extends Parameter {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::withTimezone() To set the timezone to UTC.
     * @uses \FireHub\Foundation\Temporal\DateTime::format() To serialize the expiration date.
     *
     * @param \FireHub\Foundation\Temporal\DateTime<non-empty-string> $datetime <<p>
     * Cookie expiration date and time.
     * </p>
     *
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimeZoneException If the timezone is invalid.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException If the datetime string is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private DateTime $datetime
    ) {

        parent::__construct(
            new Name('Expires'),
            new Value(
                $datetime
                    ->withTimezone(new NamedTimezone(Zone::UTC))
                    ->format('D, d M Y H:i:s \G\M\T')
            )
        );

    }

    /**
     * ### Parses an HTTP Cookie Expires Attribute
     *
     * Parses an Expires attribute containing an HTTP-date value.
     *
     * The date must use the IMF-fixdate format and GMT timezone.
     *
     * The parsed date is stored as a Foundation DateTime instance.
     *
     * <code>
     * $expires = Expires::parse(
     *     'Expires=Wed, 21 Oct 2026 07:28:00 GMT'
     * );
     *
     * $expires->value();
     * // Expires=Wed, 21 Oct 2026 07:28:00 GMT
     *
     * $expires->datetime()->format('Y-m-d H:i:s');
     * // 2026-10-21 07:28:00
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::parse() To parse the generic parameter.
     * @uses \FireHub\Adapter\HTTP\Header\Name::equalsIgnoreCase() To validate the attribute name.
     * @uses \FireHub\Foundation\Temporal\DateTime::fromFormat() To parse the HTTP-date.
     * @uses \FireHub\Foundation\Temporal\DateTime::format() To validate the parsed date.
     *
     * @param non-empty-string $value <p>
     * Serialized HTTP cookie Expires attribute.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the attribute name or date is invalid.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException If the date cannot be parsed.
     * @throws \FireHub\Core\Exception\FireHubException If component validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed HTTP cookie Expires attribute.
     */
    public static function parse (string $value):self {

        $parameter = parent::parse($value);

        if (!$parameter->name()->equalsIgnoreCase(new Name('Expires')))
            throw new InvalidParameterException(
                'Expected an HTTP cookie attribute named "Expires".'
            );

        /** @var non-empty-string $content */
        $content = $parameter->content()->value();

        $format = 'D, d M Y H:i:s \G\M\T';

        $datetime = DateTime::fromFormat(
            $content,
            '!'.$format,
            new NamedTimezone(Zone::UTC)
        );

        if ($datetime->format($format) !== $content)
            throw new InvalidParameterException(
                'Invalid HTTP cookie expiration date.'
            );

        return new self($datetime);

    }

    /**
     * ### Gets the Expiration Date
     *
     * Returns the original Foundation DateTime object, preserving its timezone.
     *
     * <code>
     * $expires = new Expires(
     *     DateTime::from('2030-01-01 12:00:00 UTC')
     * );
     *
     * $expires->datetime();
     * // Foundation DateTime instance
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Temporal\DateTime<non-empty-string> Original expiration date.
     */
    public function datetime ():DateTime {

        return $this->datetime;

    }

}