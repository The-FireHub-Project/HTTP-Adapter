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

namespace FireHub\Adapter\HTTP\Header\Cookie\Response;

use FireHub\Core\Type\ValueObject;
use FireHub\Core\Type\Date\Zone;
use FireHub\Foundation\Temporal\ {
    DateTime, NamedTimezone
};

/**
 * ### HTTP Cookie Expires Attribute
 *
 * Represents the Expires attribute of an HTTP Set-Cookie header.
 *
 * The expiration date is serialized in GMT using the HTTP-date format required for cookie expiration.
 *
 * The original DateTime object is preserved, including its timezone.
 *
 * <code>
 * $expires = new Expires(
 *     DateTime::from('2026-10-21 07:28:00')
 * );
 *
 * $expires->headerValue();
 * // Expires=Wed, 21 Oct 2026 07:28:00 GMT
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<string>
 */
final readonly class Expires extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\Temporal\DateTime<non-empty-string> $datetime <p>
     * Cookie expiration date and time.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private DateTime $datetime
    ) {}

    /**
     * ### Gets the Expiration Date
     *
     * Returns the original Foundation DateTime object.
     *
     * <code>
     * $expires = new Expires(
     *     DateTime::from('2026-10-21 07:28:00')
     * );
     *
     * $expires->datetime();
     * // Foundation DateTime instance
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Temporal\DateTime<non-empty-string> Cookie expiration date.
     */
    public function datetime ():DateTime {

        return $this->datetime;

    }

    /**
     * ### Gets the HTTP Expiration Date
     *
     * Serializes the expiration instant in GMT.
     *
     * <code>
     * $expires = new Expires(
     *     DateTime::from('2026-10-21 07:28:00')
     * );
     *
     * $expires->value();
     * // Wed, 21 Oct 2026 07:28:00 GMT
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::timestamp() To retrieve the absolute expiration instant.
     *
     * @return string HTTP expiration date in GMT.
     */
    public function value ():string {

        return $this->datetime
            ->withTimezone(new NamedTimezone(Zone::UTC))
            ->format('D, d M Y H:i:s \G\M\T');

    }

    /**
     * ### Gets the Serialized Expires Attribute
     *
     * <code>
     * $expires = new Expires(
     *     DateTime::from('2026-10-21 07:28:00')
     * );
     *
     * $expires->headerValue();
     * // Expires=Wed, 21 Oct 2026 07:28:00 GMT
     * </code>
     *
     * @since 1.0.0
     *
     * @uses self::value() To serialize the expiration date.
     *
     * @return non-empty-string Serialized Expires attribute.
     */
    public function headerValue ():string {

        return 'Expires='.$this->value();

    }

}