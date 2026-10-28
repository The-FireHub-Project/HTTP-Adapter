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

namespace FireHub\Adapter\HTTP\Header\Encoding;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Quality;
use FireHub\Runtime;

/**
 * ### Represents an HTTP encoding preference
 *
 * Encapsulates a content coding or wildcard range together with its associated quality factor.
 *
 * Encoding preferences are used in HTTP content negotiation to indicate which content codings a client supports and
 * their relative priorities.
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
     * @param \FireHub\Adapter\HTTP\Header\Encoding\Type|\FireHub\Adapter\HTTP\Header\Encoding\Range|\FireHub\Adapter\HTTP\Header\Encoding\Custom<non-empty-string> $encoding <p>
     * Accepted content coding or wildcard range.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Quality $quality [optional] <p>
     * Quality factor associated with the content coding.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Type|Range|Custom $encoding,
        private Quality $quality = new Quality(1.0)
    ) {}

    /**
     * ### Gets the accepted content coding or range
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Encoding\Type|\FireHub\Adapter\HTTP\Header\Encoding\Range|\FireHub\Adapter\HTTP\Header\Encoding\Custom<non-empty-string>
     * Accepted content coding or range.
     */
    public function encoding ():Type|Range|Custom {

        return $this->encoding;

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
     * <code>
     * echo (new Preference(Type::GZIP))->value();
     * // gzip
     *
     * echo (new Preference(Type::BR, new Quality(0.9)))->value();
     * // br;q=0.9
     *
     * echo (new Preference(Range::ANY, new Quality(0.5)))->value();
     * // *;q=0.5
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Quality::value() To get the quality factor.
     * @uses \FireHub\Adapter\HTTP\Header\Encoding\Custom::value() To get the custom encoding.
     */
    public function value ():string {

        $encoding = $this->encoding instanceof Custom
            ? $this->encoding->value()
            : $this->encoding->value;

        $quality = $this->quality->value();

        /** @var TValue $encoding */
        if ($quality === 1.0) return $encoding;

        return "$encoding;q=$quality"; // @phpstan-ignore-line

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Quality::value() To get the quality factor.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To convert the encoding to lowercase.
     * @uses \FireHub\Adapter\HTTP\Header\Encoding\Custom::value() To get the custom encoding.
     *
     * @return array{string, float} Comparison value.
     */
    protected function comparisonValue ():array {

        $encoding = $this->encoding instanceof Custom
            ? $this->encoding->value()
            : $this->encoding->value;

        return [
            Runtime\Str\SB\Casing::toLower($encoding),
            $this->quality->value()
        ];

    }

}