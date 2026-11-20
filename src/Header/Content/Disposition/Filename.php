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

namespace FireHub\Adapter\HTTP\Header\Content\Disposition;

use FireHub\Adapter\HTTP\Header\{
    Name,
    Value
};
use FireHub\Adapter\HTTP\Header\Parameter\Quoted;
use FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException;

/**
 * ### HTTP Content-Disposition Filename Parameter
 *
 * Represents the filename parameter of a Content-Disposition header.
 *
 * The filename parameter uses HTTP quoted-string syntax.
 *
 * Token-compatible filenames are serialized without quotes, while filenames containing spaces or other permitted
 * characters are quoted.
 *
 * This class does not encode Unicode filenames using RFC 8187. Use FilenameExtended for the filename* parameter.
 *
 * <code>
 * $filename = new Filename('report.pdf');
 *
 * $filename->value(); // filename=report.pdf
 *
 * $filename = new Filename('annual report.pdf');
 *
 * $filename->value(); // filename="annual report.pdf"
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Filename extends Quoted {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param non-empty-string $filename <p>
     * Suggested filename.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the filename is empty or invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (string $filename) {

        parent::__construct(
            new Name('filename'),
            new Value($filename)
        );

    }

    /**
     * ### Parses a Content-Disposition Filename Parameter
     *
     * Accepts token and quoted-string representations.
     *
     * Quoted-string escaping is handled by the parent Quoted class.
     *
     * <code>
     * $filename = Filename::parse('filename="annual report.pdf"');
     *
     * $filename->content()->value(); // annual report.pdf
     *
     * $filename = Filename::parse('filename=report.pdf');
     *
     * $filename->content()->value(); // report.pdf
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Quoted::parse() To decode the filename parameter.
     * @uses \FireHub\Adapter\HTTP\Header\Name::equalsIgnoreCase() To validate the parameter name.
     *
     * @param non-empty-string $value <p>
     * Serialized filename parameter.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter name or filename is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the quoted-string syntax is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed filename parameter.
     */
    public static function parse (string $value):self {

        $parameter = parent::parse($value);

        if (!$parameter->name()->equalsIgnoreCase(new Name('filename')))
            throw new InvalidParameterException(
                'Expected a Content-Disposition parameter named "filename".'
            );

        /** @var non-empty-string $filename */
        $filename = $parameter->content()->value();

        return new self($filename);

    }

}