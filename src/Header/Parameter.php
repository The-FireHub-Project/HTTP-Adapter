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
 * ### Represents an HTTP Header Parameter
 *
 * Immutable value object representing a generic HTTP header parameter consisting of a name and content.
 *
 * Parameter names follow HTTP token syntax.
 *
 * Parameter content is preserved without interpreting or decoding header-specific syntax.
 *
 * The first equals sign separates the parameter name from its content. Additional equals signs are preserved.
 *
 * Serialization returns the name and content without applying quotation marks or escaping.
 *
 * Header-specific parameter validation belongs to specialized implementations.
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
 * $parameter->content()->value(); // "Annual Report.pdf"
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
readonly class Parameter extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate parameter content.
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
     * ### Parses an HTTP Parameter
     *
     * Parses a generic HTTP name-value pair.
     *
     * The first equals sign separates the name from the content.
     * Additional equals signs are preserved as part of the content.
     *
     * <code>
     * $parameter = Parameter::parse('Path=/users');
     *
     * $parameter->name()->value();    // Path
     * $parameter->content()->value(); // /users
     *
     * $parameter = Parameter::parse('token=abc==');
     *
     * $parameter->content()->value(); // abc==
     *
     * $parameter = Parameter::parse('session=');
     *
     * $parameter->content()->value(); // ''
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Search::firstPosition() To locate the separator.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To extract the components.
     *
     * @param non-empty-string $value Serialized HTTP parameter.
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

        /** @var non-empty-string $name */
        $name = Runtime\Str\SB\Access::part($value, 0, $separator);
        $content = Runtime\Str\SB\Access::part($value, $separator + 1);

        return new self(
            new Name($name),
            new Value($content)
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
     * @return \FireHub\Adapter\HTTP\Header\Name HTTP parameter name.
     */
    final public function name ():Name {

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
     * @return \FireHub\Adapter\HTTP\Header\Value HTTP parameter content.
     */
    final public function content ():Value {

        return $this->content;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $parameter = new Parameter(
     *     new Name('Path'),
     *     new Value('/users')
     * );
     *
     * $parameter->value(); // Path=/users
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Name::value() To retrieve the parameter name.
     * @uses \FireHub\Adapter\HTTP\Header\Value::value() To retrieve the parameter content.
     */
    public function value ():string {

        return $this->name->value() . '=' . $this->content->value();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return array{\FireHub\Adapter\HTTP\Header\Name, \FireHub\Adapter\HTTP\Header\Value} Normalized parameter
     * components.
     */
    final protected function comparisonValue ():array {

        return [
            $this->name,
            $this->content
        ];

    }

}