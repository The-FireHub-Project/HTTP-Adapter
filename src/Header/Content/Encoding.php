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

namespace FireHub\Adapter\HTTP\Header\Content;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Coding\Content;
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;
use FireHub\Runtime;

/**
 * ### HTTP Content-Encoding Header
 *
 * Represents an immutable, ordered collection of content codings applied to an HTTP message representation.
 *
 * Codings are stored in the order in which they were applied.
 *
 * <code>
 * $encoding = Encoding::of(
 *     Content::GZIP,
 *     Content::BR
 * );
 *
 * $encoding->count(); // 2
 * $encoding->value(); // gzip, br
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<string>
 */
final readonly class Encoding extends ValueObject {

    /**
     * ### Content Codings
     * @since 1.0.0
     *
     * @var list<\FireHub\Adapter\HTTP\Header\Coding\Content>
     */
    private array $codings;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::values() To reindex the coding list.
     *
     * @param \FireHub\Adapter\HTTP\Header\Coding\Content ...$codings <p>
     * Content codings in application order.
     * </p>
     *
     * @return void
     */
    public function __construct (Content ...$codings) {

        $this->codings = Runtime\Arr\Access::values($codings);

    }

    /**
     * ### Creates an HTTP Content-Encoding Header
     *
     * Creates an ordered collection of content codings.
     *
     * <code>
     * $encoding = Encoding::of(
     *     Content::GZIP,
     *     Content::BR
     * );
     *
     * $encoding->value(); // gzip, br
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\Coding\Content ...$codings <p>
     * Content codings in application order.
     * </p>
     *
     * @return self New HTTP Content-Encoding header.
     */
    public static function of (Content ...$codings):self {

        return new self(...$codings);

    }

    /**
     * ### Parses an HTTP Content-Encoding Header
     *
     * Parses a comma-separated list of known content codings.
     *
     * Content coding names are case-insensitive.
     *
     * Empty list members are ignored.
     *
     * <code>
     * $encoding = Encoding::parse('gzip, br');
     *
     * $encoding->count(); // 2
     * $encoding->value(); // gzip, br
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To split the field value.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To remove optional whitespace.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize coding names.
     *
     * @param string $value <p>
     * Serialized Content-Encoding header value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If a content coding is unsupported.
     *
     * @return self Parsed HTTP Content-Encoding header.
     */
    public static function parse (string $value):self {

        $codings = [];

        foreach (Runtime\Str\SB\Delimiter::explode($value, ',') as $part) {

            $coding = Runtime\Str\SB\Casing::toLower(
                Runtime\Str\SB\Transform::trim(
                    $part,
                    characters: " \t"
                )
            );

            if ($coding === '') continue;

            $codings[] = Content::tryFrom($coding)
                ?? throw new InvalidFieldValueException(
                    'Unsupported HTTP content coding.'
                );

        }

        return new self(...$codings);

    }

    /**
     * ### Counts Content Codings
     *
     * <code>
     * Encoding::of(
     *     Content::GZIP,
     *     Content::BR
     * )->count(); // 2
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Inspection::count() To count the codings.
     *
     * @return non-negative-int Number of content codings.
     */
    public function count ():int {

        return Runtime\Arr\Inspection::count($this->codings);

    }

    /**
     * ### Gets All Content Codings
     *
     * Returns content codings in their original application order.
     *
     * <code>
     * $codings = Encoding::of(
     *     Content::GZIP,
     *     Content::BR
     * )->all();
     *
     * $codings[0]; // Content::GZIP
     * $codings[1]; // Content::BR
     * </code>
     *
     * @since 1.0.0
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Coding\Content> Content codings.
     */
    public function all ():array {

        return $this->codings;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * Encoding::of(
     *     Content::GZIP,
     *     Content::BR
     * )->value(); // gzip, br
     *
     * Encoding::of()->value(); // ''
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To serialize the content codings.
     */
    public function value ():string {

        $values = [];

        foreach ($this->codings as $coding)
            $values[] = $coding->value;

        return Runtime\Str\SB\Delimiter::implode($values, ', ');

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Coding\Content> Codings used for equality comparison.
     */
    protected function comparisonValue ():array {

        return $this->codings;

    }

}