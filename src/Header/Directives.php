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

use FireHub\Adapter\HTTP\Header\Parsing\Splitter;
use FireHub\Runtime;

/**
 * ### HTTP Header Directives
 *
 * Represents an immutable collection of comma-separated HTTP directives.
 *
 * Each directive consists of a name and an optional value.
 *
 * <code>
 * $directives = new Directives(
 *     new Name('public'),
 *     new Parameter(
 *         new Name('max-age'),
 *         new Value('3600')
 *     ),
 *     new Name('must-revalidate')
 * );
 *
 * $directives->value();
 * // public, max-age=3600, must-revalidate
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Directives extends Collection {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\Name|\FireHub\Adapter\HTTP\Header\Parameter ...$directives <p>
     * HTTP directives.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (Name|Parameter ...$directives) {

        $normalized = [];

        foreach ($directives as $directive) {

            if ($directive instanceof Parameter && !$directive instanceof Parameter\Quoted)
                $directive = new Parameter\Quoted(
                    $directive->name(),
                    $directive->content()
                );

            $normalized[] = $directive;

        }

        parent::__construct(...$normalized);

    }

    /**
     * ### Parses Comma-Separated Directives
     *
     * Handles quoted strings containing commas.
     *
     * <code>
     * $directives = Directives::parse(
     *     'public, max-age=3600, must-revalidate'
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Parsing\Splitter::split() To split attributes.
     * @uses \FireHub\Adapter\HTTP\Header\Parameter\Quoted::parse() To parse name-value parameters.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To trim attribute segments.
     * @uses \FireHub\Runtime\Str\SB\Search::contains() To check for parameter syntax.
     *
     * @param string $value <p>
     * Serialized directives.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidParameterException If the parameter content is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed directives collection.
     */
    public static function parse (string $value):self {

        $directives = [];

        foreach (Splitter::split($value, ',') as $segment) {

            $segment = Runtime\Str\SB\Transform::trim($segment, characters: " \t");

            if ($segment === '') continue;

            $directives[] = Runtime\Str\SB\Search::contains('=', $segment)
                ? Parameter\Quoted::parse($segment)
                : new Name($segment);

        }

        return new self(...$directives);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $directives->value();
     * // public, max-age=3600, must-revalidate
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To serialize the directives.
     */
    public function value ():string {

        $values = [];

        foreach ($this->parameters as $directive)
            $values[] = $directive->value();

        return Runtime\Str\SB\Delimiter::implode($values, ', ');

    }

}