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
use FireHub\Adapter\HTTP\Header\Cookie\{
    Name, Value
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;
use FireHub\Runtime;

/**
 * ### HTTP Cookie
 *
 * Represents an immutable HTTP cookie name-value pair.
 *
 * A cookie consists of a case-sensitive name and a value separated by an equal sign.
 *
 * This class represents only the cookie-pair syntax. Cookie attributes such as Path, Domain, Secure, HttpOnly, and
 * SameSite are handled separately by the Set-Cookie implementation.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Header\Cookie;
 * use FireHub\Adapter\HTTP\Header\Cookie\Name;
 * use FireHub\Adapter\HTTP\Header\Cookie\Value;
 *
 * $cookie = new Cookie(
 *     new Name('session'),
 *     new Value('abc123')
 * );
 *
 * $cookie->value();
 * // session=abc123
 *
 * $cookie->name()->value();
 * // session
 *
 * $cookie->content()->value();
 * // abc123
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Cookie extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\Cookie\Name $name <p>
     * Cookie name.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Cookie\Value $content <p>
     * Cookie value.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Name $name,
        private Value $content
    ) {}

    /**
     * ### Parses an HTTP Cookie
     *
     * Parses a serialized cookie name-value pair.
     *
     * The first equals sign separates the cookie name from its value. Additional equals signs are treated as part of
     * the cookie value.
     *
     * Optional surrounding double quotation marks are supported through the cookie value parser.
     *
     * <code>
     * $cookie = Cookie::parse('session=abc123');
     *
     * $cookie->name()->value();
     * // session
     *
     * $cookie->content()->value();
     * // abc123
     *
     * $cookie = Cookie::parse('token=abc=123');
     *
     * $cookie->content()->value();
     * // abc=123
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Name::parse() To parse the cookie name.
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Value::parse() To parse the cookie value.
     * @uses \FireHub\Runtime\Str\SB\Search::firstPosition() To find the first equals sign.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To extract the cookie name and value.
     *
     * @param string $value <p>
     * Serialized HTTP cookie name-value pair.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the cookie-pair syntax is malformed.
     * @throws \FireHub\Core\Exception\FireHubException If a value-object validation condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If a validation exception is not a FireHubException.
     *
     * @return self Parsed HTTP cookie.
     */
    public static function parse (string $value):self {

        $position = Runtime\Str\SB\Search::firstPosition('=', $value);

        if ($position === false)
            throw new InvalidFieldValueException(
                'Invalid HTTP cookie pair. Expected name=value.'
            );

        $name = Runtime\Str\SB\Access::part($value, 0, $position);
        $content = Runtime\Str\SB\Access::part($value, $position + 1);

        if ($name === '')
            throw new InvalidFieldValueException(
                'HTTP cookie name cannot be empty.'
            );

        return new self(
            Name::parse($name),
            Value::parse($content)
        );

    }

    /**
     * ### Gets the Cookie Name
     *
     * Returns the case-sensitive name of this cookie.
     *
     * <code>
     * $cookie = Cookie::parse('session=abc123');
     *
     * $cookie->name()->value();
     * // session
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Cookie\Name Cookie name.
     */
    public function name ():Name {

        return $this->name;

    }

    /**
     * ### Gets the Cookie Content
     *
     * Returns the validated value of this cookie.
     *
     * <code>
     * $cookie = Cookie::parse('session=abc123');
     *
     * $cookie->content()->value();
     * // abc123
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Cookie\Value Cookie content.
     */
    public function content ():Value {

        return $this->content;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $cookie = new Cookie(
     *     new Name('session'),
     *     new Value('abc123')
     * );
     *
     * $cookie->value();
     * // session=abc123
     * </code>
     *
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Name::value() To serialize the cookie name.
     * @uses \FireHub\Adapter\HTTP\Header\Cookie\Value::value() To serialize the cookie value.
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->name->value().'='.$this->content->value();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return array{non-empty-string, string} Cookie comparison value.
     */
    protected function comparisonValue ():array {

        return [
            $this->name->value(),
            $this->content->value()
        ];

    }

}