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
use FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException;
use FireHub\Runtime;

/**
 * ### Represents an HTTP header parameter
 *
 * Immutable value object representing a generic HTTP header parameter consisting of a name and a value.
 *
 * Parameter names follow HTTP token syntax and are case-insensitive. Parameter content may be represented as a token
 * or a quoted string.
 *
 * The original parameter name and decoded content are preserved. Quoting and escaping are applied automatically
 * during serialization.
 *
 * This class validates parameter syntax without interpreting header-specific parameter semantics.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Header\ {
 *     Name, Value, Parameter
 * };
 *
 * $parameter = new Parameter(
 *     new Name('charset'),
 *     new Value('utf-8')
 * );
 *
 * $parameter->name()->value();    // charset
 * $parameter->content()->value(); // utf-8
 * $parameter->value();            // charset=utf-8
 *
 * $parameter = Parameter::parse('filename="Annual Report.pdf"');
 *
 * $parameter->content()->value(); // Annual Report.pdf
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Parameter extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate parameter content.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate HTTP quoted-string characters.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name<non-empty-string> $name <p>
     * HTTP parameter name.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Value<string> $content <p>
     * Decoded HTTP parameter content.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
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
                '/\A[\x09\x20-\x7E\x80-\xFF]*\z/',
                $content->value()
            ),
            fn() => new InvalidParameterException(
                'Invalid HTTP parameter content.'
            )
        );

    }

    /**
     * ### Parses an HTTP header parameter
     *
     * Parses a serialized HTTP parameter into its name and decoded content.
     *
     * <code>
     * $parameter = Parameter::parse('charset=utf-8');
     *
     * $parameter->name()->value();    // charset
     * $parameter->content()->value(); // utf-8
     * </code>
     *
     * @since 1.0.0
     *
     * @uses self::parseContent() To parse parameter content.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To retrieve the parameter name and content.
     * @uses \FireHub\Runtime\Str\SB\Search::firstPosition() To find the equals sign.
     *
     * @param non-empty-string $value <p>
     * Serialized HTTP parameter.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed HTTP parameter.
     */
    public static function parse (string $value):self {

        $separator = Runtime\Str\SB\Search::firstPosition('=', $value);

        if ($separator === false)
            throw new InvalidParameterException(
                'An HTTP parameter must contain an equals sign.'
            );

        $name = Runtime\Str\SB\Access::part($value, 0, $separator);
        $content = Runtime\Str\SB\Access::part($value, $separator + 1);

        if ($name === '' || $content === '')
            throw new InvalidParameterException(
                'An HTTP parameter must contain a name and value.'
            );

        return new self(
            new Name($name),
            new Value(self::parseContent($content))
        );

    }

    /**
     * ### Returns the parameter name
     *
     * Returns the parameter name as an immutable HTTP name value object.
     *
     * <code>
     * $parameter = new Parameter(
     *     new Name('charset'),
     *     new Value('utf-8')
     * );
     *
     * $parameter->name()->value(); // charset
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Name<non-empty-string> HTTP parameter name.
     */
    public function name ():Name {

        return $this->name;

    }

    /**
     * ### Returns the parameter content
     *
     * Returns the decoded parameter content without surrounding quotation marks or escape sequences.
     *
     * <code>
     * $parameter = Parameter::parse('filename="Annual Report.pdf"');
     *
     * $parameter->content()->value(); // Annual Report.pdf
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Value<string> HTTP parameter content.
     */
    public function content ():Value {

        return $this->content;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $parameter = new Parameter(
     *     new Name('filename'),
     *     new Value('Annual Report.pdf')
     * );
     *
     * $parameter->value(); // filename="Annual Report.pdf"
     * </code>
     *
     * @since 1.0.0
     *
     * @uses self::isToken() To determine if the parameter content is a token or quoted string.
     * @uses \FireHub\Adapter\HTTP\Header\Name::value() To retrieve the parameter name.
     * @uses \FireHub\Adapter\HTTP\Header\Value::value() To retrieve the parameter content.
     * @uses \FireHub\Runtime\Str\SB\Replace::replace() To escape special characters.
     *
     * @return non-empty-string Serialized HTTP parameter.
     */
    public function value ():string {

        $content = $this->content->value();

        if (self::isToken($content))
            return $this->name->value() . '=' . $content;

        return $this->name->value() . '="' . Runtime\Str\SB\Replace::replace(
                ['\\', '"'],
                ['\\\\', '\\"'],
                $content
            ) . '"';

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Name::value() To retrieve the parameter name.
     * @uses \FireHub\Adapter\HTTP\Header\Value::value() To retrieve the parameter content.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize the parameter name.
     *
     * @return array{non-empty-string, string} Normalized parameter components.
     */
    protected function comparisonValue ():array {

        /** @var array{non-empty-string, string} */
        return [
            Runtime\Str\SB\Casing::toLower($this->name->value()),
            $this->content->value()
        ];

    }

    /**
     * ### Validates an HTTP token
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate HTTP token characters.
     *
     * @param string $value <p>
     * HTTP token value to validate.
     * </p>
     *
     * @return bool True if the value is a valid HTTP token.
     */
    private static function isToken (string $value):bool {

        return Runtime\Str\SB\Regex::match(
            "/\A[!#$%&'*+.^_`|~0-9A-Za-z-]+\z/",
            $value
        );

    }

    /**
     * ### Parses HTTP parameter content
     *
     * Validates token content or decodes a quoted-string representation.
     * @since 1.0.0
     *
     * @uses self::parseQuotedContent() To parse quoted-string content.
     * @uses self::parseTokenContent() To parse token content.
     *
     * @param string $content <p>
     * Serialized parameter content.
     * </p>s
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     *
     * @return string Decoded parameter content.
     */
    private static function parseContent (string $content):string {

        if ($content[0] === '"')
            return self::parseQuotedContent($content);

        if (!self::isToken($content))
            throw new InvalidParameterException(
                'Invalid HTTP parameter token value.'
            );

        return $content;

    }

    /**
     * ### Parses quoted HTTP parameter content
     *
     * Validates quoted-string syntax and decodes quoted-pair escape sequences.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To determine the length of the quoted-string.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate quoted-string characters.
     *
     * @param string $content <p>
     * Serialized quoted-string content.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     *
     * @return string Decoded parameter content.
     */
    private static function parseQuotedContent (string $content):string {

        $length = Runtime\Str\SB\Inspection::length($content);

        if ($length < 2 || $content[$length - 1] !== '"')
            throw new InvalidParameterException(
                'Invalid quoted HTTP parameter value.'
            );

        $decoded = '';

        for ($index = 1; $index < $length - 1; $index++) {

            $character = $content[$index];

            if ($character === '\\') {

                if (++$index >= $length - 1)
                    throw new InvalidParameterException(
                        'Incomplete escape sequence in HTTP parameter.'
                    );

                $character = $content[$index];

                if (!Runtime\Str\SB\Regex::match(
                    '/\A[\x09\x20-\x7E\x80-\xFF]\z/',
                    $character
                )) throw new InvalidParameterException(
                        'Invalid escaped character in HTTP parameter.'
                    );

            } else if (!Runtime\Str\SB\Regex::match(
                '/\A[\x09\x20-\x21\x23-\x5B\x5D-\x7E\x80-\xFF]\z/',
                $character
            )) throw new InvalidParameterException(
                    'Invalid character in quoted HTTP parameter.'
                );

            $decoded .= $character;

        }

        return $decoded;

    }

}