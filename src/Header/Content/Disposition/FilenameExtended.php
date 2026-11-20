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

namespace FireHub\Adapter\HTTP\Header\Disposition;

use FireHub\Core\Type\Str\Encoding;
use FireHub\Adapter\HTTP\Header\{
    Name, Parameter, Value
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException;
use FireHub\Runtime;

/**
 * ### Extended Content-Disposition Filename Parameter
 *
 * Represents the filename* parameter defined by RFC 8187.
 *
 * Supports UTF-8 filenames encoded using RFC 8187 percent-encoding.
 *
 * The constructor accepts a decoded UTF-8 filename, while parse() accepts a serialized extended parameter.
 *
 * <code>
 * $filename = new FilenameExtended('izvještaj.pdf');
 *
 * $filename->value();
 * // filename*=UTF-8''izvje%C5%A1taj.pdf
 *
 * $filename->filename(); // izvještaj.pdf
 * </code>
 *
 * @since 1.0.0
 */
final readonly class FilenameExtended extends Parameter {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the filename content.
     * @uses \FireHub\Adapter\HTTP\Header\Disposition\FilenameExtended::encode() To encode the filename.
     * @uses \FireHub\Runtime\Str\MB\Inspection::checkEncoding() To validate the filename content.
     *
     * @param string $filename <p>
     * Decoded UTF-8 filename.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $filename
    ) {

        $this->guard(
            static fn():bool => Runtime\Str\MB\Inspection::checkEncoding(
                $filename,
                Encoding::UTF_8
            ),
            static fn() => new InvalidParameterException(
                'Extended filename must contain valid UTF-8.'
            )
        );

        parent::__construct(
            new Name('filename*'),
            new Value("UTF-8''" . self::encode($filename))
        );

    }

    /**
     * ### Parses an Extended Filename Parameter
     *
     * Parses RFC 8187 extended parameter syntax.
     *
     * <code>
     * $filename = FilenameExtended::parse(
     *     "filename*=UTF-8''izvje%C5%A1taj.pdf"
     * );
     *
     * $filename->filename(); // izvještaj.pdf
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::parse() To parse the generic HTTP parameter.
     * @uses \FireHub\Adapter\HTTP\Header\Name::equalsIgnoreCase() To compare the parameter name.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the extended filename syntax.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To split the encoded filename.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To validate the number of parts.
     * @uses \FireHub\Runtime\Encoding::rawUrlDecode() To decode the encoded filename.
     *
     * @param non-empty-string $value <p>
     * Serialized extended filename parameter.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed extended filename parameter.
     */
    public static function parse (string $value):self {

        $parameter = parent::parse($value);

        if (!$parameter->name()->equalsIgnoreCase(new Name('filename*')))
            throw new InvalidParameterException(
                'Expected an extended filename parameter.'
            );

        $content = $parameter->content()->value();

        if (!Runtime\Str\SB\Regex::match(
            "/\AUTF-8'[^']*'(?<encoded>[!#$&+.^_`|~0-9A-Za-z%-]*)\z/i",
            $content
        )) throw new InvalidParameterException(
            'Invalid RFC 8187 extended filename.'
        );

        $parts = Runtime\Str\SB\Delimiter::explode($content, "'", 3);

        if (Runtime\Arr\Inspection::count($parts) !== 3)
            throw new InvalidParameterException(
                'Invalid extended filename syntax.'
            );

        /** @var array{0: non-empty-string, 1: non-empty-string, 2: non-empty-string} $parts */
        $filename = Runtime\Encoding::rawUrlDecode($parts[2]);

        return new self($filename);

    }

    /**
     * ### Returns the Decoded Filename
     *
     * <code>
     * $filename = new FilenameExtended('izvještaj.pdf');
     *
     * $filename->filename(); // izvještaj.pdf
     * </code>
     *
     * @since 1.0.0
     *
     * @return string Decoded UTF-8 filename.
     */
    public function filename ():string {

        return $this->filename;

    }

    /**
     * ### Encodes a Filename
     *
     * Applies RFC 8187 percent-encoding.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Encoding::rawUrlEncode() To encode the filename.
     *
     * @param string $filename <p>
     * Decoded UTF-8 filename.
     * </p>
     *
     * @return string Encoded filename.
     */
    private static function encode (string $filename):string {

        return Runtime\Encoding::rawUrlEncode($filename);

    }

}