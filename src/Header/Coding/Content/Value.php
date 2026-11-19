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

namespace FireHub\Adapter\HTTP\Header\Coding\Content;

use FireHub\Adapter\HTTP\Header\ {
    Coding\Content, Value as HeaderValue
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;
use FireHub\Runtime;

/**
 * ### HTTP Content Coding Value
 *
 * Represents a supported HTTP content coding.
 *
 * The content coding is normalized to its canonical lowercase representation and stored as a strongly typed Content
 * enum.
 *
 * <code>
 * $coding = Value::parse('GZIP');
 *
 * $coding->content(); // Content::GZIP
 * $coding->value();   // gzip
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Value extends HeaderValue {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\Coding\Content $content <p>
     * HTTP content coding.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the header value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct(
        private Content $content
    ) {

        parent::__construct($content->value);

    }

    /**
     * ### Parses an HTTP Content Coding
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize the content coding to its canonical lowercase
     * representation.
     *
     * @param non-empty-string $value <p>
     * Content coding token.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the header value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed content coding.
     */
    public static function parse (string $value):self {

        $content = Content::tryFrom(
            Runtime\Str\SB\Casing::toLower($value)
        );

        if ($content === null)
            throw new InvalidFieldValueException(
                'Unsupported HTTP content coding.'
            );

        return new self($content);

    }

    /**
     * ### Gets the Content Coding
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Coding\Content Content coding.
     */
    public function content ():Content {

        return $this->content;

    }

}