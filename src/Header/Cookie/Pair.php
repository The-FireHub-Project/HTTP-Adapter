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
use FireHub\Adapter\HTTP\Header\ {
    Name, Value
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;
use FireHub\Runtime;

/**
 * ### Represents an HTTP Cookie Pair
 *
 * Immutable value object representing a single HTTP cookie name-value pair.
 *
 * Cookie names are case-sensitive, and empty cookie values are permitted.
 *
 * Cookie content is validated according to the cookie-octet syntax.  Optional surrounding double quotes are accepted
 * during parsing but are not preserved during serialization.
 *
 * Unlike generic HTTP parameters, cookie values do not support HTTP quoted-string escape sequences.
 *
 * <code>
 * $pair = new Pair(
 *     new Name('session'),
 *     new Value('abc123')
 * );
 *
 * $pair->name()->value();    // session
 * $pair->content()->value(); // abc123
 * $pair->value();            // session=abc123
 *
 * $pair = Pair::parse('token=YWJjZA==');
 *
 * $pair->content()->value(); // YWJjZA==
 *
 * $empty = Pair::parse('session=');
 *
 * $empty->content()->value(); // ''
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Pair extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate cookie content.
     * @uses \FireHub\Adapter\HTTP\Header\Value::value() To retrieve the cookie content.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate cookie-octet syntax.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name $name <p>
     * Cookie name.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Value $content <p>
     * Cookie content.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the cookie content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private Name $name,
        private Value $content
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                '/\A[\x21\x23-\x2B\x2D-\x3A\x3C-\x5B\x5D-\x7E]*\z/',
                $content->value()
            ),
            fn() => new InvalidFieldValueException(
                'Invalid HTTP cookie content.'
            )
        );

    }

    /**
     * ### Parses an HTTP Cookie Pair
     *
     * Parses a serialized HTTP cookie pair into its name and content.
     *
     * The first equals sign separates the cookie name from its content. Additional equals signs are preserved as
     * part of the content.
     *
     * Optional surrounding double quotes are removed before validation.
     *
     * <code>
     * $pair = Pair::parse('session=abc123');
     *
     * $pair->name()->value();    // session
     * $pair->content()->value(); // abc123
     *
     * $pair = Pair::parse('token=YWJjZA==');
     *
     * $pair->content()->value(); // YWJjZA==
     *
     * $pair = Pair::parse('session="abc123"');
     *
     * $pair->content()->value(); // abc123
     *
     * $pair = Pair::parse('session=');
     *
     * $pair->content()->value(); // ''
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Search::firstPosition() To locate the name-value separator.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To extract the cookie name and content.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To determine the content length.
     *
     * @param non-empty-string $value <p>
     * Serialized HTTP cookie pair.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the cookie content is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If the cookie name is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed HTTP cookie pair.
     */
    public static function parse (string $value):self {

        $separator = Runtime\Str\SB\Search::firstPosition('=', $value);

        if ($separator === false)
            throw new InvalidFieldValueException(
                'An HTTP cookie pair must contain an equals sign.'
            );

        /** @var non-empty-string $name */
        $name = Runtime\Str\SB\Access::part($value, 0, $separator);
        $content = Runtime\Str\SB\Access::part($value, $separator + 1);

        $length = Runtime\Str\SB\Inspection::length($content);

        if (
            $length >= 2
            && $content[0] === '"'
            && $content[$length - 1] === '"'
        ) $content = Runtime\Str\SB\Access::part($content, 1, $length - 2);

        return new self(
            new Name($name),
            new Value($content)
        );

    }

    /**
     * ### Returns the Cookie Name
     *
     * Returns the name of this cookie pair.
     *
     * Cookie names are case-sensitive.
     *
     * <code>
     * $pair = Pair::parse('session=abc123');
     *
     * $pair->name()->value(); // session
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Name Cookie name.
     */
    public function name ():Name {

        return $this->name;

    }

    /**
     * ### Returns the Cookie Content
     *
     * Returns the cookie content without surrounding double quotes.
     *
     * <code>
     * $pair = Pair::parse('session="abc123"');
     *
     * $pair->content()->value(); // abc123
     *
     * $empty = Pair::parse('session=');
     *
     * $empty->content()->value(); // ''
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Value Cookie content.
     */
    public function content ():Value {

        return $this->content;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $pair = new Pair(
     *     new Name('session'),
     *     new Value('abc123')
     * );
     *
     * $pair->value(); // session=abc123
     *
     * $pair = Pair::parse('token=YWJjZA==');
     *
     * $pair->value(); // token=YWJjZA==
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Name::value() To retrieve the cookie name.
     * @uses \FireHub\Adapter\HTTP\Header\Value::value() To retrieve the cookie content.
     */
    public function value ():string {

        return $this->name->value() . '=' . $this->content->value();

    }

    /**
     * {@inheritDoc}
     *
     * @since 1.0.0
     *
     * @return array{\FireHub\Adapter\HTTP\Header\Name, \FireHub\Adapter\HTTP\Header\Value} Normalized cookie pair
     * components.
     */
    protected function comparisonValue ():array {

        return [
            $this->name,
            $this->content
        ];

    }

}