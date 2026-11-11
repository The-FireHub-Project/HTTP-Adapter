<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=7.4
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Response;

/**
 * ### Represents an HTTP response status
 *
 * Defines the contract for standard and custom HTTP response status codes.
 *
 * Provides access to the numeric status code, reason phrase, and HTTP response status classification.
 * @since 1.0.0
 */
interface Status {

    /**
     * ### Gets the HTTP response status code
     * @since 1.0.0
     *
     * @return int<100, 599> HTTP response status code.
     */
    public function code ():int;

    /**
     * ### Checks whether the status is informational
     *
     * Informational responses use status codes from 100 to 199.
     * @since 1.0.0
     *
     * @return bool True if the status belongs to the 1xx category.
     */
    public function isInformational ():bool;

    /**
     * ### Checks whether the status indicates success
     *
     * Successful responses use status codes from 200 to 299.
     * @since 1.0.0
     *
     * @return bool True if the status belongs to the 2xx category.
     */
    public function isSuccessful ():bool;

    /**
     * ### Checks whether the status indicates redirection
     *
     * Redirection responses use status codes from 300 to 399.
     * @since 1.0.0
     *
     * @return bool True if the status belongs to the 3xx category.
     */
    public function isRedirection ():bool;

    /**
     * ### Checks whether the status indicates a client error
     *
     * Client error responses use status codes from 400 to 499.
     * @since 1.0.0
     *
     * @return bool True if the status belongs to the 4xx category.
     */
    public function isClientError ():bool;

    /**
     * ### Checks whether the status indicates a server error
     *
     * Server error responses use status codes from 500 to 599.
     * @since 1.0.0
     *
     * @return bool True if the status belongs to the 5xx category.
     */
    public function isServerError ():bool;

    /**
     * ### Gets the HTTP reason phrase
     * @since 1.0.0
     *
     * @return non-empty-string HTTP reason phrase.
     */
    public function reasonPhrase ():string;

}