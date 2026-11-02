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

namespace FireHub\Adapter\HTTP\Response\Status;

use FireHub\Core\Type\ValueObject;
use FireHub\Adapter\HTTP\Response\Status;
use FireHub\Adapter\HTTP\Response\Status\Exception\InvalidStatusCodeException;

/**
 * ### Represents a custom HTTP response status code
 *
 * Supports extension status codes within the HTTP status code range.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<int>
 */
final readonly class Custom extends ValueObject implements Status {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the status code.
     *
     * @param int<100, 599> $code <p>
     * HTTP response status code.
     * </p>
     * @param non-empty-string $reason_phrase <p>
     * Human-readable reason phrase.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Response\Status\Exception\InvalidStatusCodeException If the status code is
     * outside the valid HTTP range.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private int $code,
        private string $reason_phrase
    ) {

        $this->guard(
            fn() => $code >= 100 && $code <= 599,
            fn() => new InvalidStatusCodeException
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function code ():int {

        return $this->code;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function isInformational ():bool {

        return $this->code >= 100 && $this->code < 200;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function isSuccessful ():bool {

        return $this->code >= 200 && $this->code < 300;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function isRedirection ():bool {

        return $this->code >= 300 && $this->code < 400;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function isClientError ():bool {

        return $this->code >= 400 && $this->code < 500;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function isServerError ():bool {

        return $this->code >= 500 && $this->code < 600;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function reasonPhrase ():string {

        return $this->reason_phrase;

    }

    /**
     * @inheritdoc
     *
     * @since 1.0.0
     *
     * @return int<100, 599> HTTP status code.
     */
    public function value ():int {

        return $this->code;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return array{int, string} Comparison value.
     */
    protected function comparisonValue ():array {

        return [
            $this->code,
            $this->reason_phrase
        ];

    }

}