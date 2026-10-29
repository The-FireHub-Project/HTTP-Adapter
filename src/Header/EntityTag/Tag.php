<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.5
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Header\EntityTag;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Exception\InvalidEntityTagException;
use FireHub\Runtime;

/**
 * ### Represents an HTTP entity tag
 *
 * Encapsulates a strong or weak entity tag used for conditional HTTP requests.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<string>
 */
final readonly class Tag extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\Identifier::guard() To enforce the invariant that an identifier name cannot be empty.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the entity tag syntax.
     *
     * @param string $tag <p>
     * Opaque entity tag value without surrounding quotation marks.
     * </p>
     * @param bool $weak [optional] <p>
     * Whether the entity tag uses weak validation.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidEntityTagException If the entity tag is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $tag,
        private bool $weak = false
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                '/\A[\x21\x23-\x7E\x80-\xFF]*\z/',
                $tag
            ),
            fn() => new InvalidEntityTagException
        );

    }

    /**
     * ### Checks whether the entity tag is weak
     * @since 1.0.0
     *
     * @return bool True if the entity tag is weak.
     */
    public function isWeak ():bool {

        return $this->weak;

    }

    /**
     * ### Gets the opaque entity tag
     * @since 1.0.0
     *
     * @return string Opaque entity tag.
     */
    public function tag ():string {

        return $this->tag;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function value ():string {

        return ($this->weak ? 'W/' : '').'"'.$this->tag.'"';

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return array{string, bool} Comparison value.
     */
    protected function comparisonValue ():array {

        return [$this->tag, $this->weak];

    }

}