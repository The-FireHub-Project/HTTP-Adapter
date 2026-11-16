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

use FireHub\Adapter\HTTP\Header\ {
    Name, Value, Parameter
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException;

/**
 * ### Content-Disposition Filename Parameter
 *
 * Represents the filename parameter of a Content-Disposition header.
 *
 * The filename parameter provides a suggested filename when content is downloaded or included in multipart form data.
 *
 * This class represents the regular filename parameter, not the extended filename* parameter.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Header\Disposition\Parameter\Filename;
 *
 * $filename = Filename::of('report.pdf');
 *
 * $filename->value();
 * // filename=report.pdf
 *
 * $filename->filename();
 * // report.pdf
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Filename extends Parameter {

    /**
     * ### Creates a Filename Parameter
     *
     * Creates a Content-Disposition filename parameter from the supplied filename.
     *
     * <code>
     * $filename = Filename::of('report.pdf');
     *
     * $filename->filename();
     * // report.pdf
     * </code>
     *
     * @since 1.0.0
     *
     * @param non-empty-string $filename <p>
     * Suggested filename.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Filename parameter.
     */
    public static function of (string $filename):self {

        if ($filename === '')
            throw new InvalidParameterException(
                'Content-Disposition filename cannot be empty.'
            );

        return new self(
            new Name('filename'),
            new Value($filename)
        );

    }

    /**
     * ### Parses a Filename Parameter
     *
     * Parses a serialized Content-Disposition filename parameter.
     *
     * <code>
     * $filename = Filename::parse('filename="report.pdf"');
     *
     * $filename->filename();
     * // report.pdf
     * </code>
     *
     * @since 1.0.0
     *
     * @uses self::of() To create the parameter.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::name() To compare the parameter name.
     * @uses \FireHub\Adapter\HTTP\Header\Name::equals() To compare the parameter name.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter::parse() To parse the serialized parameter.
     *
     * @param non-empty-string $value <p>
     * Serialized filename parameter.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed filename parameter.
     */
    public static function parse (string $value):self {

        $parameter = Parameter::parse($value);

        if (!$parameter->name()->equals(new Name('filename')))
            throw new InvalidParameterException(
                'Expected a Content-Disposition filename parameter.'
            );

        /** @var non-empty-string $filename */
        $filename = $parameter->content()->value();

        return self::of($filename);

    }

    /**
     * ### Gets the Filename
     *
     * Returns the suggested filename without the parameter name or surrounding quotation marks.
     *
     * <code>
     * $filename = Filename::of('report.pdf');
     *
     * $filename->filename();
     * // report.pdf
     * </code>
     *
     * @since 1.0.0
     *
     * @return non-empty-string Suggested filename.
     */
    public function filename ():string {

        /** @var non-empty-string */
        return $this->content()->value();

    }

}