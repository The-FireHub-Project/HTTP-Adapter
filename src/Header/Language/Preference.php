<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache-2.0
 *
 * @php-version >=8.2
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Header\Language;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Quality;
use FireHub\Adapter\HTTP\Header\Parameter\Parser;
use FireHub\Adapter\HTTP\Header\Exception\InvalidLanguageException;
use FireHub\Runtime;

/**
 * ### Represents an HTTP language preference
 *
 * Encapsulates a language range and its associated quality factor for HTTP content negotiation.
 * @since 1.0.0
 *
 * @template TValue of non-empty-string
 *
 * @extends \FireHub\Core\Type\ValueObject<TValue>
 */
final readonly class Preference extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\Language\Type|\FireHub\Adapter\HTTP\Header\Language\Range|\FireHub\Adapter\HTTP\Header\Language\Custom<non-empty-string> $language <p>
     * Accepted language or language range.
     * </p>
     *
     * @param \FireHub\Adapter\HTTP\Header\Quality $quality [optional] <p>
     * Quality factor.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Type|Range|Custom $language,
        private Quality $quality = new Quality(1.0)
    ) {}

    /**
     * ### Parses an HTTP Accept-Language preference
     *
     * Converts a raw language preference into a validated language range and its optional quality factor.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Parser::parse() To parse quality parameters.
     * @uses \FireHub\Adapter\HTTP\Header\Quality::parse() To parse the quality factor.
     * @uses \FireHub\Runtime\Str\SB\Search::firstPosition() To find the separator between language and parameters.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To extract the language.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To remove optional whitespace.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize language tags.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To validate the number of parameters.
     *
     * @param string $value <p>
     * Raw Accept-Language preference.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidLanguageException If the language preference is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterValueException If parameter syntax is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidMediaPreferenceExceptionQuality If the quality factor is
     * invalid.
     *
     * @return self<non-empty-string> Parsed language preference.
     */
    public static function parse (string $value):self {

        $separator = Runtime\Str\SB\Search::firstPosition(';', $value);

        $language = Runtime\Str\SB\Transform::trim(
            $separator === false
                ? $value
                : Runtime\Str\SB\Access::part($value, 0, $separator),
            characters: " \t"
        );

        if ($language === '')
            throw new InvalidLanguageException(
                'Accept-Language language range cannot be empty.'
            );

        $normalized = Runtime\Str\SB\Casing::toLower($language);

        /** @var non-empty-string&lowercase-string $normalized */
        $type = Range::tryFrom($normalized);

        if ($type === null) {

            foreach (Type::cases() as $case)
                if (Runtime\Str\SB\Casing::toLower($case->value) === $normalized) {

                    $type = $case;

                    break;

                }

        }

        /** @var non-empty-string $language */
        $type ??= new Custom($language);

        if ($separator === false)
            return new self($type);

        $raw = Runtime\Str\SB\Access::part($value, $separator + 1);

        if (Runtime\Str\SB\Transform::trim($raw, characters: " \t") === '')
            throw new InvalidLanguageException(
                'Accept-Language quality parameter cannot be empty.'
            );

        $parameters = Parser::parse($raw);

        if (
            Runtime\Arr\Inspection::count($parameters) !== 1
            || Runtime\Str\SB\Casing::toLower($parameters[0]['name']) !== 'q' // @phpstan-ignore offsetAccess.notFound
        ) throw new InvalidLanguageException(
            'Accept-Language supports only one q parameter.'
        );

        if ($parameters[0]['quoted']) // @phpstan-ignore offsetAccess.notFound
            throw new InvalidLanguageException(
                'Accept-Language quality factor must not be quoted.'
            );

        return new self(
            $type,
            Quality::parse($parameters[0]['value']) // @phpstan-ignore offsetAccess.notFound
        );

    }

    /**
     * ### Gets the accepted language
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Language\Type|\FireHub\Adapter\HTTP\Header\Language\Range|\FireHub\Adapter\HTTP\Header\Language\Custom<non-empty-string>
     * Accepted content language or range.
     */
    public function language ():Type|Range|Custom {

        return $this->language;

    }

    /**
     * ### Gets the quality factor
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Quality Quality factor.
     */
    public function quality ():Quality {

        return $this->quality;

    }

    /**
     * {@inheritDoc}
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Language\Custom::value() To get the language value.
     * @uses \FireHub\Adapter\HTTP\Header\Quality::value() To get the quality factor value.
     */
    public function value ():string {

        $language = $this->language instanceof Custom
            ? $this->language->value()
            : $this->language->value;

        $quality = $this->quality->value();

        /** @var TValue $language */
        if ($quality === 1.0) return $language;

        return "$language;q=$quality"; // @phpstan-ignore-line

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Language\Custom::value() To get the language value.
     * @uses \FireHub\Adapter\HTTP\Header\Quality::value() To get the quality factor value.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To convert the language to lowercase.
     *
     * @return array{string, float} Comparison value
     */
    protected function comparisonValue ():array {

        $language = $this->language instanceof Custom
            ? $this->language->value()
            : $this->language->value;

        return [
            Runtime\Str\SB\Casing::toLower($language),
            $this->quality->value()
        ];

    }

}