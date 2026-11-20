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

namespace FireHub\Adapter\HTTP\Header\Content;

use FireHub\Adapter\HTTP\Header\{
    Attributes, Item, Name, Parameter
};
use FireHub\Adapter\HTTP\Header\Content\Disposition\{
    Filename, FilenameExtended, Type
};
use FireHub\Adapter\HTTP\Header\Parameter\Quoted;
use FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException;
use FireHub\Runtime;

/**
 * ### HTTP Content-Disposition Header
 *
 * Represents a Content-Disposition header field value.
 *
 * A Content-Disposition value consists of a disposition type and an optional collection of name-value parameters.
 *
 * Parameter names are case-insensitive and must be unique.
 *
 * Known filename parameters are converted to specialized value objects.
 *
 * <code>
 * $disposition = new Disposition(
 *     Type::attachment(),
 *     new Attributes(
 *         new Filename('report.pdf')
 *     )
 * );
 *
 * $disposition->value();
 * // attachment; filename=report.pdf
 *
 * $disposition = Disposition::parse(
 *     'attachment; filename="annual report.pdf"'
 * );
 *
 * $disposition->content()->value(); // attachment
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Disposition extends Item {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Attributes::all() To retrieve the parameters.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Disposition\Filename::__construct() To normalize filename parameters.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Disposition\FilenameExtended::parse() To normalize extended filenames.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize parameter names.
     *
     * @param \FireHub\Adapter\HTTP\Header\Content\Disposition\Type $type <p>
     * Disposition type.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Attributes $parameters <p>
     * Optional disposition parameters.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If a parameter is invalid or duplicated.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (Type $type, Attributes $parameters = new Attributes) {

        $normalized = [];
        $names = [];

        foreach ($parameters->all() as $attribute) {

            if (!$attribute instanceof Parameter)
                throw new InvalidParameterException(
                    'Content-Disposition attributes must be name-value parameters.'
                );

            $name = Runtime\Str\SB\Casing::toLower($attribute->name()->value());

            if (isset($names[$name]))
                throw new InvalidParameterException(
                    'Duplicate Content-Disposition parameter: ' . $name
                );

            $names[$name] = true;

            $normalized[] = match ($name) {
                'filename' => new Filename(
                    $attribute->content()->value() // @phpstan-ignore argument.type
                ),
                'filename*' => $attribute instanceof FilenameExtended
                    ? $attribute
                    : FilenameExtended::parse($attribute->value()),
                default => $attribute

            };

        }

        parent::__construct(
            $type,
            new Attributes(...$normalized)
        );

    }

    /**
     * ### Parses an HTTP Content-Disposition Header
     *
     * Parses a serialized Content-Disposition header field value.
     *
     * Supports standard disposition types, extension disposition types, quoted-string parameters, and extended
     * filename parameters.
     *
     * <code>
     * $disposition = Disposition::parse(
     *     'attachment; filename="annual report.pdf"'
     * );
     *
     * $disposition->content()->value(); // attachment
     *
     * $filename = $disposition->getParameter(
     *     new Name('filename')
     * );
     *
     * $filename instanceof Filename; // true
     *
     * $disposition = Disposition::parse(
     *     "attachment; filename*=UTF-8''izvje%C5%A1taj.pdf"
     * );
     *
     * $disposition->value();
     * // attachment; filename*=UTF-8''izvje%C5%A1taj.pdf
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Item::parse() To parse the generic header item.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Quoted::parse() To decode quoted-string parameters.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Disposition\Type::parse() To parse the disposition type.
     * @uses \FireHub\Adapter\HTTP\Header\Content\Disposition\FilenameExtended::parse() To parse extended filenames.
     *
     * @param non-empty-string $value <p>
     * Serialized Content-Disposition header value.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the header parameters are invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the quoted-string syntax is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed Content-Disposition header.
     */
    public static function parse (string $value):self {

        $item = parent::parse($value);

        $parameters = [];

        foreach ($item->attributes()->all() as $attribute) {

            if (!$attribute instanceof Parameter)
                throw new InvalidParameterException(
                    'Content-Disposition attributes must be name-value parameters.'
                );

            $name = Runtime\Str\SB\Casing::toLower(
                $attribute->name()->value()
            );

            $parameters[] = match ($name) {
                'filename*' => FilenameExtended::parse(
                    $attribute->value()
                ),
                default => Quoted::parse(
                    $attribute->value()
                )
            };

        }

        /** @var non-empty-string $type */
        $type = $item->content()->value();

        return new self(
            Type::parse($type),
            new Attributes(...$parameters)
        );

    }

    /**
     * ### Gets the Disposition Type
     *
     * <code>
     * $disposition = new Disposition(Type::attachment());
     *
     * $disposition->type()->value(); // attachment
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Item::content() To retrieve the primary value.
     *
     * @return \FireHub\Adapter\HTTP\Header\Content\Disposition\Type Disposition type.
     */
    public function type ():Type {

        /** @var \FireHub\Adapter\HTTP\Header\Content\Disposition\Type $type */
        $type = $this->content();

        return $type;

    }

    /**
     * ### Gets a Disposition Parameter
     *
     * Searches for a parameter by its case-insensitive name.
     *
     * <code>
     * $disposition = new Disposition(
     *     Type::attachment(),
     *     new Attributes(new Filename('report.pdf'))
     * );
     *
     * $filename = $disposition->getParameter(
     *     new Name('FILENAME')
     * );
     *
     * $filename instanceof Filename; // true
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Attributes::all() To retrieve the parameters.
     * @uses \FireHub\Adapter\HTTP\Header\Name::equalsIgnoreCase() To compare parameter names.
     *
     * @param \FireHub\Adapter\HTTP\Header\Name $name <p>
     * Parameter name to find.
     * </p>
     *
     * @return \FireHub\Adapter\HTTP\Header\Parameter|null Matching parameter or null.
     */
    public function getParameter (Name $name):?Parameter {

        foreach ($this->attributes()->all() as $attribute)
            if (
                $attribute instanceof Parameter
                && $attribute->name()->equalsIgnoreCase($name)
            ) return $attribute;

        return null;

    }

}