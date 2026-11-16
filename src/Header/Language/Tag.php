<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.5
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Header\Language;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;
use FireHub\Runtime;

/**
 * ### HTTP Language Tag
 *
 * Represents an immutable language tag using RFC 5646 syntax.
 *
 * Language tags are case-insensitive, but their original casing is preserved during serialization.
 *
 * Supports regular language tags, private-use tags, and grandfathered tags.
 *
 * <code>
 * Tag::of('hr-HR')->value();       // hr-HR
 * Tag::of('zh-Hant-TW')->value();  // zh-Hant-TW
 * Tag::of('x-private')->value();  // x-private
 * </code>
 *
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Tag extends ValueObject {

    /**
     * ### RFC 5646 Language Tag Pattern
     *
     * Validates the structural syntax of language tags.
     *
     * This pattern does not verify IANA registry membership or semantic subtag constraints.
     * @since 1.0.0
     *
     * @var non-empty-string
     */
    private const string PATTERN =
        '/\A(?:' .
        '(?:' .
        '(?:[A-Za-z]{2,3}(?:-[A-Za-z]{3}){0,3}|[A-Za-z]{4}|[A-Za-z]{5,8})' .
        '(?:-[A-Za-z]{4})?' .
        '(?:-(?:[A-Za-z]{2}|[0-9]{3}))?' .
        '(?:-(?:[A-Za-z0-9]{5,8}|[0-9][A-Za-z0-9]{3}))*' .
        '(?:-[0-9A-WY-Za-wy-z](?:-[A-Za-z0-9]{2,8})+)*' .
        '(?:-x(?:-[A-Za-z0-9]{1,8})+)?' .
        ')' .
        '|x(?:-[A-Za-z0-9]{1,8})+' .
        '|(?:en-GB-oed|i-ami|i-bnn|i-default|i-enochian|i-hak|' .
        'i-klingon|i-lux|i-mingo|i-navajo|i-pwn|i-tao|i-tay|i-tsu|' .
        'sgn-BE-FR|sgn-BE-NL|sgn-CH-DE|' .
        'art-lojban|cel-gaulish|no-bok|no-nyn|' .
        'zh-guoyu|zh-hakka|zh-min|zh-min-nan|zh-xiang)' .
        ')\z/iD';

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\Identifier::guard() To validate the language tag.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate RFC 5646 syntax.
     *
     * @param non-empty-string $tag <p>
     * Language tag.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the language tag is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $tag
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                self::PATTERN,
                $tag
            ),
            fn() => new InvalidFieldValueException(
                'Invalid HTTP language tag.'
            )
        );

    }

    /**
     * ### Creates a Language Tag
     *
     * <code>
     * Tag::of('hr-HR');
     * Tag::of('en-US');
     * Tag::of('zh-Hant-TW');
     * </code>
     *
     * @since 1.0.0
     *
     * @param non-empty-string $tag <p>
     * Language tag.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the language tag is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self New language tag.
     */
    public static function of (string $tag):self {

        return new self($tag);

    }

    /**
     * ### Parses a Language Tag
     *
     * Removes surrounding HTTP optional whitespace.
     *
     * <code>
     * $tag = Tag::parse(' hr-HR ');
     *
     * $tag->value(); // hr-HR
     *
     * Tag::parse('zh-Hant-TW')->value(); // zh-Hant-TW
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To remove optional whitespace.
     *
     * @param string $value <p>
     * Serialized language tag.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the language tag is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If validation fails.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed language tag.
     */
    public static function parse (string $value):self {

        $tag = Runtime\Str\SB\Transform::trim(
            $value,
            characters: " \t"
        );

        if ($tag === '')
            throw new InvalidFieldValueException(
                'HTTP language tag cannot be empty.'
            );

        return new self($tag);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $tag = Tag::of('zh-Hant-TW');
     *
     * $tag->value(); // zh-Hant-TW
     * </code>
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->tag;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize the language tag.
     *
     * @return lowercase-string Normalized language tag.
     */
    protected function comparisonValue ():string {

        return Runtime\Str\SB\Casing::toLower($this->tag);

    }

}