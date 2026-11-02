<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.1
 * @package Adapter\HTTP
 */

namespace FireHub\Adapter\HTTP\Header\Authorization\Challenge\Bearer;

/**
 * ### Represents a Bearer authentication error
 *
 * Defines standardized OAuth 2.0 Bearer token error codes.
 * @since 1.0.0
 */
enum Error:string {

    /**
     * ### The request is missing a required parameter or is otherwise malformed
     * @since 1.0.0
     */
    case INVALID_REQUEST = 'invalid_request';

    /**
     * ### The access token is expired, revoked, malformed, or otherwise invalid
     * @since 1.0.0
     */
    case INVALID_TOKEN = 'invalid_token';

    /**
     * ### The access token does not provide the required privileges
     * @since 1.0.0
     */
    case INSUFFICIENT_SCOPE = 'insufficient_scope';

}