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

namespace FireHub\Adapter\HTTP\Header\Media;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Quality;
use FireHub\Runtime;

/**
 * ### Represents an HTTP media preference
 *
 * Encapsulates a concrete media type or wildcard media range together with its associated quality factor.
 *
 * Media preferences are used in HTTP content negotiation to express which representations a client accepts
 * and their relative priorities.
 *
 * The quality factor ranges from <code>0</code> to <code>1</code>, with a maximum precision of three decimal places.
 * A quality factor of <code>1</code> represents the highest preference, while <code>0</code> indicates that the
 * representation is not acceptable.
 * @since 1.0.0
 *
 * @template TValue of non-empty-string
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Preference extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\Media\Type|\FireHub\Adapter\HTTP\Header\Media\Range|\FireHub\Adapter\HTTP\Header\Media\Custom<non-empty-string> $media <p>
     * Accepted media type or media range.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Quality $quality [optional] <p>
     * Optional quality factor. Defaults to the maximum preference.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Type|Range|Custom $media,
        private Quality $quality = new Quality(1.0)
    ) {}

    /**
     * ### Gets the accepted media type or range
     *
     * <code>
     * $preference = new MediaPreference(MediaType::APPLICATION_JSON);
     *
     * $media = $preference->media();
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Media\Type|\FireHub\Adapter\HTTP\Header\Media\Range|\FireHub\Adapter\HTTP\Header\Media\Custom<non-empty-string>
     * Accepted media type or media range.
     */
    public function media ():Type|Range|Custom {

        return $this->media;

    }

    /**
     * ### Gets the HTTP quality factor
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\Quality Quality factor associated with this media preference.
     */
    public function quality ():Quality {

        return $this->quality;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * echo (new MediaPreference(MediaType::APPLICATION_JSON))->value();
     * // application/json
     *
     * echo (new MediaPreference(MediaType::TEXT_HTML, new Quality(0.9)))->value();
     * // text/html;q=0.9
     *
     * echo (new MediaPreference(MediaRange::IMAGE, new Quality(0.8)))->value();
     * // image/*;q=0.8
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Quality::value() To get the quality factor.
     * @uses \FireHub\Adapter\HTTP\Header\Media\Custom::value() To get the custom media type.
     */
    public function value ():string {

        $media = $this->media instanceof Custom
            ? $this->media->value()
            : $this->media->value;

        $quality = $this->quality->value();

        if ($quality === 1.0) return $media;

        return "$media;q=$quality";

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Quality::value() To get the quality factor.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To convert the media type to lowercase.
     * @uses \FireHub\Adapter\HTTP\Header\Media\Custom::value() To get the custom media type.
     *
     * @return array{string, float} Comparison value.
     */
    protected function comparisonValue ():array {

        $media = $this->media instanceof Custom
            ? $this->media->value()
            : $this->media->value;

        return [
            Runtime\Str\SB\Casing::toLower($media),
            $this->quality->value()
        ];

    }

}