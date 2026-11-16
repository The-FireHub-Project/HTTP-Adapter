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

namespace FireHub\Adapter\HTTP\Header\Disposition\Parameter;

use FireHub\Core\Type\Str\Encoding;
use FireHub\Adapter\HTTP\Header\ {
    Name, Value, Parameter
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException;
use FireHub\Runtime;

/**
 * ### Extended Content-Disposition Filename Parameter
 *
 * Represents the extended filename* parameter defined by RFC 8187.
 *
 * The extended parameter supports UTF-8 filenames through percent-encoding  and an optional language tag.
 *
 * The serialized format is:
 *
 * charset'language'encoded-value
 *
 * This implementation uses UTF-8 for serialization and accepts UTF-8 encoded values when parsing.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Header\Disposition\Parameter\FilenameExtended;
 *
 * $filename = FilenameExtended::of('izvještaj.pdf');
 *
 * $filename->value();
 * // filename*=UTF-8''izvje%C5%A1taj.pdf
 *
 * $filename->filename();
 * // izvještaj.pdf
 * </code>
 *
 * @since 1.0.0
 */
final readonly class FilenameExtended extends Parameter {

    /**
     * ### Creates an Extended Filename Parameter
     *
     * Creates a filename* parameter from a UTF-8 filename.
     *
     * The filename is percent-encoded, according to RFC 8187.
     *
     * <code>
     * $filename = FilenameExtended::of('izvještaj.pdf');
     *
     * $filename->value();
     * // filename*=UTF-8''izvje%C5%A1taj.pdf
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::of() To create the parameter.
     * @uses \FireHub\Runtime\Str\MB\Inspection::checkEncoding() To check if the filename is valid UTF-8.
     * @uses \FireHub\Runtime\Encoding::rawUrlEncode() To percent-encode the filename.
     *
     * @param non-empty-string $filename <p>
     * UTF-8 filename.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Extended filename parameter.
     */
    public static function of (string $filename):self {

        if ($filename === '' || !Runtime\Str\MB\Inspection::checkEncoding($filename, Encoding::UTF_8))
            throw new InvalidParameterException(
                'Extended filename must be a non-empty UTF-8 string.'
            );

        $encoded = Runtime\Encoding::rawUrlEncode($filename);

        return new self(
            new Name('filename*'),
            new Value("UTF-8''".$encoded)
        );

    }

    /**
     * ### Parses an Extended Filename Parameter
     *
     * Parses a serialized filename* parameter containing an RFC 8187 extended value.
     *
     * <code>
     * $filename = FilenameExtended::parse(
     *     "filename*=UTF-8''izvje%C5%A1taj.pdf"
     * );
     *
     * $filename->filename();
     * // izvještaj.pdf
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::parse() To parse the parameter name and value.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::name() To compare parameter names.
     * @uses \FireHub\Adapter\HTTP\Header\Name::equals() To compare parameter names.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the extended filename format.
     * @uses \FireHub\Runtime\Str\MB\Inspection::checkEncoding() To check if the filename is valid UTF-8.
     * @uses \FireHub\Runtime\Encoding::rawUrlEncode() To percent-encode the filename.
     * @uses \FireHub\Runtime\Str\SB\Compare::lexical() To compare the charset.
     *
     * @param non-empty-string $value <p>
     * Serialized filename* parameter.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed extended filename parameter.
     */
    public static function parse (string $value):self {

        $parameter = Parameter::parse($value);

        if (!$parameter->name()->equals(new Name('filename*')))
            throw new InvalidParameterException(
                'Expected an extended filename parameter.'
            );

        $content = $parameter->content()->value();

        if (!Runtime\Str\SB\Regex::match(
            "/\A([^']*)'([^']*)'(.*)\z/",
            $content,
            result: $matches
        )) throw new InvalidParameterException(
            'Invalid extended filename format.'
        );

        /** @var array{1: non-empty-string, 2: non-empty-string, 3: non-empty-string} $matches */
        [, $charset, $language, $encoded] = $matches;

        if (Runtime\Str\SB\Compare::lexical($charset, 'UTF-8', false) !== 0)
            throw new InvalidParameterException(
                'Unsupported extended filename charset.'
            );

        if ($language !== '' && !Runtime\Str\SB\Regex::match(
                '/\A[A-Za-z]{1,8}(?:-[A-Za-z0-9]{1,8})*\z/',
                $language
        )) throw new InvalidParameterException(
            'Invalid extended filename language tag.'
        );

        if (!Runtime\Str\SB\Regex::match(
            "/\A(?:[A-Za-z0-9!#$&+.^_`|~-]|%[0-9A-Fa-f]{2})+\z/",
            $encoded
        )) throw new InvalidParameterException(
            'Invalid extended filename encoding.'
        );

        $filename = Runtime\Encoding::rawUrlEncode($encoded);

        if ($filename === '' || !Runtime\Str\MB\Inspection::checkEncoding($filename, Encoding::UTF_8))
            throw new InvalidParameterException(
                'Invalid UTF-8 extended filename.'
            );

        return self::of($filename);

    }

    /**
     * ### Gets the Decoded Filename
     *
     * Returns the decoded UTF-8 filename.
     *
     * <code>
     * $filename = FilenameExtended::of('izvještaj.pdf');
     *
     * $filename->filename();
     * // izvještaj.pdf
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Encoding::rawUrlDecode() To decode the percent-encoded filename.
     * @uses \FireHub\Runtime\Str\SB\Search::firstPosition() To find the encoded filename.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To extract the encoded filename.
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     *
     * @return non-empty-string Decoded UTF-8 filename.
     */
    public function filename ():string {

        $content = $this->content()->value();

        $position = Runtime\Str\SB\Search::firstPosition($content, "''");

        if ($position === false)
            throw new InvalidParameterException(
                'Invalid extended filename format.'
            );

        /** @var non-empty-string */
        return Runtime\Encoding::rawUrlDecode(Runtime\Str\SB\Access::part($content, $position + 2));

    }

}