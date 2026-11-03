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

use FireHub\Adapter\HTTP\Header\Exception\InvalidVaryException;
use FireHub\Runtime;

/**
 * ### Represents an HTTP Vary header
 *
 * Specifies which request header fields influence the selection of a response representation.
 *
 * Supports an explicit list of field names or the wildcard value "*".
 * @since 1.0.0
 */
final readonly class Vary {

    /**
     * ### Request header field names
     * @since 1.0.0
     *
     * @var list<\FireHub\Adapter\HTTP\Header\Name<non-empty-string>>
     */
    private array $fields;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To convert the header field names to lowercase.
     * @uses \FireHub\Adapter\HTTP\Header\Name::value() To get the header field name.
     * @uses \FireHub\Runtime\Arr\Access::values() To remove duplicate header field names.
     *
     * @param null|\FireHub\Adapter\HTTP\Header\Name<non-empty-string> $field <p>
     * First request header field, or null for wildcard.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\Name<non-empty-string> ...$additional <p>
     * Additional request header fields.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidVaryException If the wildcard Vary contains additional
     * field names.
     *
     * @return void
     */
    public function __construct (?Name $field = null, Name ...$additional) {

        if ($field === null) {

            if ($additional !== [])
                throw new InvalidVaryException(
                    'Wildcard Vary cannot contain additional field names.'
                );

            $this->fields = [];

            return;

        }

        $unique = [];

        foreach ([$field, ...$additional] as $item) {

            $key = Runtime\Str\SB\Casing::toLower(
                $item->value()
            );

            if (!isset($unique[$key]))
                $unique[$key] = $item;

        }

        $this->fields = Runtime\Arr\Access::values($unique);

    }

    /**
     * ### Creates a wildcard Vary header
     * @since 1.0.0
     *
     * @return self Wildcard Vary header.
     */
    public static function wildcard ():self {

        return new self();

    }

    /**
     * ### Gets the request header field names
     * @since 1.0.0
     *
     * @return list<\FireHub\Adapter\HTTP\Header\Name<non-empty-string>> Request header field names.
     */
    public function fields ():array {

        return $this->fields;

    }

    /**
     * ### Checks whether the Vary header is a wildcard
     * @since 1.0.0
     *
     * @return bool True if the Vary header is a wildcard.
     */
    public function isWildcard ():bool {

        return $this->fields === [];

    }

    /**
     * ### Serializes the Vary header
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\Vary::isWildcard() To check if the Vary header is a wildcard.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To serialize the header field names.
     * @uses \FireHub\Adapter\HTTP\Header\Name::value() To get the header field name.
     *
     * @return non-empty-string Serialized Vary header value.
     */
    public function value ():string {

        if ($this->isWildcard()) return '*';

        $values = [];

        foreach ($this->fields as $field)
            $values[] = $field->value();

        /** @var non-empty-string */
        return Runtime\Str\SB\Delimiter::implode(
            $values,
            ', '
        );

    }

}