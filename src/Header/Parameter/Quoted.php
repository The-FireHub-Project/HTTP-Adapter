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

namespace FireHub\Adapter\HTTP\Header\Parameter;

use FireHub\Adapter\HTTP\Header\{
    Name, Parameter, Value
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException;
use FireHub\Runtime;

/**
 * ### HTTP Token or Quoted-String Parameter
 *
 * Represents an HTTP parameter whose content follows token or quoted-string syntax.
 *
 * Parameter content is stored in decoded form.
 *
 * Token-compatible content is serialized without quotation marks. Other valid content is serialized as an escaped
 * quoted-string.
 *
 * <code>
 * $parameter = new Quoted(
 *     new Name('charset'),
 *     new Value('utf-8')
 * );
 *
 * $parameter->value(); // charset=utf-8
 *
 * $parameter = new Quoted(
 *     new Name('filename'),
 *     new Value('Annual Report.pdf')
 * );
 *
 * $parameter->value(); // filename="Annual Report.pdf"
 *
 * $parameter = Quoted::parse(
 *     'filename="Annual Report.pdf"'
 * );
 *
 * $parameter->content()->value(); // Annual Report.pdf
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Quoted extends Parameter {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the parameter content.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate HTTP quoted-string characters.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name $name <p>
     * HTTP parameter name.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Value $content <p>
     * Decoded HTTP parameter content.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (Name $name, Value $content) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                '/\A[\x09\x20-\x7E\x80-\xFF]*\z/',
                $content->value()
            ),
            fn() => new InvalidParameterException(
                'Invalid HTTP quoted-string parameter content.'
            )
        );

        parent::__construct($name, $content);

    }

    /**
     * ### Parses an HTTP Token or Quoted-String Parameter
     *
     * <code>
     * $parameter = Quoted::parse('level="hello world"');
     *
     * $parameter->content()->value(); // hello world
     * $parameter->value();            // level="hello world"
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Quoted::decode() To decode the quoted-string.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Quoted::isToken() To check if the content is an HTTP token.
     * @uses \FireHub\Runtime\Str\SB\Search::firstPosition() To locate the name-value separator.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To extract the parameter name and content.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate HTTP token or quoted-string characters.
     *
     * @param non-empty-string $value <p>
     * Serialized parameter.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed parameter.
     */
    public static function parse (string $value):self {

        $separator = Runtime\Str\SB\Search::firstPosition('=', $value);

        if ($separator === false)
            throw new InvalidParameterException(
                'An HTTP parameter must contain an equals sign.'
            );

        /** @var non-empty-string $name */
        $name = Runtime\Str\SB\Access::part($value, 0, $separator);
        $content = Runtime\Str\SB\Access::part($value, $separator + 1);

        if (self::isToken($content))
            return new self(new Name($name), new Value($content));

        return new self(
            new Name($name),
            new Value(self::decode($content))
        );

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $parameter = new Quoted(
     *     new Name('profile'),
     *     new Value('https://example.com/schema')
     * );
     *
     * $parameter->value();
     * // profile="https://example.com/schema"
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Quoted::isToken() To check if the content is an HTTP token.
     * @uses \FireHub\Runtime\Str\SB\Replace::replace() To escape special characters.
     */
    public function value ():string {

        $content = $this->content()->value();

        if (self::isToken($content))
            return parent::value();

        return $this->name()->value() . '="' .
            Runtime\Str\SB\Replace::replace(
                ['\\', '"'],
                ['\\\\', '\\"'],
                $content
            ) . '"';

    }

    /**
     * ### Checks HTTP Token Syntax
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate HTTP token characters.
     *
     * @param string $value <p>
     * Content to validate.
     * </p>
     *
     * @return bool True if the content is an HTTP token.
     */
    private static function isToken (string $value):bool {

        return Runtime\Str\SB\Regex::match(
            "/\A[!#$%&'*+.^_`|~0-9A-Za-z-]+\z/",
            $value
        );

    }

    /**
     * ### Decodes an HTTP Quoted-String
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To get the length of the string.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate HTTP quoted-string characters.
     *
     * @param string $value <p>
     * Serialized quoted-string.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the syntax is invalid.
     *
     * @return string Decoded content.
     */
    private static function decode (string $value):string {

        $length = Runtime\Str\SB\Inspection::length($value);

        if (
            $length < 2
            || $value[0] !== '"'
            || $value[$length - 1] !== '"'
        ) throw new InvalidParameterException(
            'Invalid HTTP quoted-string parameter.'
        );

        $decoded = '';

        for ($index = 1; $index < $length - 1; $index++) {

            $character = $value[$index];

            if ($character === '\\') {

                if (++$index >= $length - 1)
                    throw new InvalidParameterException(
                        'Incomplete HTTP quoted-pair escape sequence.'
                    );

                $character = $value[$index];

                if (!Runtime\Str\SB\Regex::match(
                    '/\A[\x09\x20-\x7E\x80-\xFF]\z/',
                    $character
                )) throw new InvalidParameterException(
                    'Invalid escaped character in HTTP quoted-string.'
                );

            } else if (!Runtime\Str\SB\Regex::match(
                '/\A[\x09\x20-\x21\x23-\x5B\x5D-\x7E\x80-\xFF]\z/',
                $character
            )) throw new InvalidParameterException(
                'Invalid character in HTTP quoted-string.'
            );

            $decoded .= $character;

        }

        return $decoded;

    }

}