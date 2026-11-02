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

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Cookie\Response as ResponseCookie;
use FireHub\Adapter\HTTP\Header\Date;
use FireHub\Runtime;

/**
 * ### Represents an HTTP Set-Cookie header value
 *
 * Serializes a response cookie and its attributes into an HTTP Set-Cookie header field value.
 *
 * Each instance represents exactly one cookie.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Set extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Cookie\Response<string> $cookie <p>
     * Response cookie to serialize.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private ResponseCookie $cookie
    ) {}

    /**
     * ### Gets the response cookie
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Cookie\Response<string> Response cookie.
     */
    public function cookie ():ResponseCookie {

        return $this->cookie;

    }

    /**
     * ### Gets the serialized Set-Cookie header value
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Date::value() To serialize the expiration date.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join cookie attributes.
     *
     * @return non-empty-string Serialized Set-Cookie header value.
     */
    public function value ():string {

        $attributes = [
            $this->cookie->name() . '=' . $this->cookie->value()
        ];

        if (($domain = $this->cookie->domain()) !== null)
            $attributes[] = 'Domain=' . $domain->value();

        if (($path = $this->cookie->path()) !== null)
            $attributes[] = 'Path=' . $path->value();

        if (($expires = $this->cookie->expires()) !== null)
            $attributes[] = 'Expires=' . new Date($expires)->value();

        if (($max_age = $this->cookie->maxAge()) !== null)
            $attributes[] = 'Max-Age=' . $max_age;

        if ($this->cookie->secure())
            $attributes[] = 'Secure';

        if ($this->cookie->httpOnly())
            $attributes[] = 'HttpOnly';

        if (($same_site = $this->cookie->sameSite()) !== null)
            $attributes[] = 'SameSite=' . $same_site->value;

        if ($this->cookie->partitioned())
            $attributes[] = 'Partitioned';

        /** @var non-empty-string */
        return Runtime\Str\SB\Delimiter::implode(
            $attributes,
            '; '
        );

    }

}