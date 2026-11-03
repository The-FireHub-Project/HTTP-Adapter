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
use FireHub\Adapter\HTTP\Header\ContentDisposition\ {
    Custom, Type
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidContentDispositionException;
use FireHub\Runtime;

/**
 * ### Represents an HTTP Content-Disposition header
 *
 * Defines whether response content should be displayed inline or handled as an attachment.
 *
 * Supports UTF-8 filenames using RFC 8187 extended parameters.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class ContentDisposition extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the content disposition filename.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the content disposition filename.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentDisposition\Type|\FireHub\Adapter\HTTP\Header\ContentDisposition\Custom $type <p>
     * Content disposition type.
     * </p>
     * @param null|non-empty-string $filename [optional] <p>
     * Suggested filename.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentDispositionException If the filename is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private Type|Custom $type,
        private ?string $filename = null
    ) {

        if ($filename !== null) {

            $this->guard(
                fn() => $filename !== ''
                    && !Runtime\Str\SB\Regex::match('/[\x00-\x1F\x7F]/', $filename),
                fn() => new InvalidContentDispositionException(
                    'Invalid content disposition filename.'
                )
            );

        }

    }

    /**
     * ### Returns a disposition with a different filename
     * @since 1.0.0
     *
     * @param non-empty-string $filename <p>
     * Suggested filename.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentDispositionException If the filename is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Updated content disposition.
     */
    public function withFilename (string $filename):self {

        return new self(
            $this->type,
            $filename
        );

    }

    /**
     * ### Returns a disposition without a filename
     * @since 1.0.0
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentDispositionException If the filename is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Updated content disposition.
     */
    public function withoutFilename ():self {

        return new self(
            $this->type
        );

    }

    /**
     * ### Returns a disposition with a different type
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentDisposition\Type|\FireHub\Adapter\HTTP\Header\ContentDisposition\Custom $type <p>
     * New disposition type.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentDispositionException If the filename is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Updated content disposition.
     */
    public function withType (Type|Custom $type):self {

        return new self(
            $type,
            $this->filename
        );

    }

    /**
     * ### Gets the content disposition type
     * @since 1.0.0
     *
     * @return \FireHub\Adapter\HTTP\Header\ContentDisposition\Type|\FireHub\Adapter\HTTP\Header\ContentDisposition\Custom Disposition type.
     */
    public function type ():Type|Custom {

        return $this->type;

    }

    /**
     * ### Gets the suggested filename
     * @since 1.0.0
     *
     * @return null|non-empty-string Suggested filename.
     */
    public function filename ():?string {

        return $this->filename;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentDisposition\Type::value() To get the type value.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the filename.
     * @uses \FireHub\Runtime\Str\SB\Regex::replace() To generate an ASCII fallback for compatibility.
     * @uses \FireHub\Runtime\Str\SB\Replace::replace() To escape the filename.
     * @uses \FireHub\Runtime\Encoding::rawUrlEncode() To encode the filename.
     */
    public function value ():string {

        $type = $this->type instanceof Type
            ? $this->type->value
            : $this->type->value();

        if ($this->filename === null)
            return $type;

        $filename = $this->filename;

        $fallback = Runtime\Str\SB\Regex::replace(
            '/[^\x20-\x7E]/',
            '_',
            $filename
        );

        $fallback = Runtime\Str\SB\Replace::replace(
            ['\\', '"'],
            ['\\\\', '\\"'],
            $fallback
        );

        $result = $type . '; filename="' . $fallback . '"';

        if (!Runtime\Str\SB\Regex::match('/[^\x20-\x7E]/', $filename))
            return $result;

        return $result
            . "; filename*=UTF-8''"
            . Runtime\Encoding::rawUrlEncode($filename);

    }

}