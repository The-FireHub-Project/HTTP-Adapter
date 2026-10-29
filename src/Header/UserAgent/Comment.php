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

namespace FireHub\Adapter\HTTP\Header\UserAgent;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Header\Exception\InvalidUserAgentCommentException;
use FireHub\Runtime;

/**
 * ### Represents an HTTP User-Agent comment
 *
 * Encapsulates an HTTP comment and serializes it using quoted-pair escaping.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Comment extends ValueObject {

    /**
     * ### Serialized comment
     * @since 1.0.0
     */
    private string $serialized;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\Identifier::guard() To enforce the invariant that an identifier name cannot be empty.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the comment content.
     *
     * @param string $comment <p>
     * Comment content.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidUserAgentCommentException If the comment is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $comment
    ) {

        $this->guard(
            fn() => !Runtime\Str\SB\Regex::match(
                '/[^\x20-\x7E]/',
                $comment
            ),
            fn() => new InvalidUserAgentCommentException
        );

        $escaped = Runtime\Str\SB\Escape::addCSlashes(
            $comment,
            '()\\'
        );

        $this->serialized = "($escaped)";

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->serialized;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    protected function comparisonValue ():string {

        return $this->comment;

    }

}