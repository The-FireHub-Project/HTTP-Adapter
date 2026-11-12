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

namespace FireHub\Adapter\HTTP\Response;

use FireHub\Adapter\HTTP\ {
    Request, Response
};
use FireHub\Adapter\HTTP\Request\Method\Standard as Method;

final readonly class Policy {

    /**
     * ### Checks whether an HTTP response may contain a message body
     *
     * Determines whether response content is permitted according to the associated request method and HTTP response
     * status.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Response\Status::code() To retrieve the HTTP response status code.
     * @uses \FireHub\Adapter\HTTP\Request\Method::value() To retrieve the HTTP request method.
     *
     * @return bool True if response content is permitted.
     */
    public static function allowsBody (Request $request, Response $response):bool {

        $status = $response->status()->code();
        $method = $request->method()->value();

        return $method !== Method::HEAD->value
            && $status >= 200
            && $status !== 204
            && $status !== 304
            && !($method === Method::CONNECT->value && $status < 300);

    }

}