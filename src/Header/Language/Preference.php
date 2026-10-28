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