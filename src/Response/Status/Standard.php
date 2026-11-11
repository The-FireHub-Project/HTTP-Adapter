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

namespace FireHub\Adapter\HTTP\Response\Status;

use FireHub\Adapter\HTTP\Response\Status;

/**
 * ### Represents standard HTTP response status codes
 *
 * Defines standard HTTP response status codes and their corresponding reason phrases.
 * @since 1.0.0
 */
enum Standard:int implements Status {


    /* * ============================================================
     * Informational responses (1xx)
     * ============================================================ */

    /**
     * ### 100 Continue
     * @since 1.0.0
     */
    case CONTINUE = 100;

    /**
     * ### 101 Switching Protocols
     * @since 1.0.0
     */
    case SWITCHING_PROTOCOLS = 101;

    /**
     * ### 102 Processing
     * @since 1.0.0
     */
    case PROCESSING = 102;

    /**
     * ### 103 Early Hints
     * @since 1.0.0
     */
    case EARLY_HINTS = 103;


    /* * ============================================================
     * Successful responses (2xx)
     * ============================================================ */

    /**
     * ### 200 OK
     * @since 1.0.0
     */
    case OK = 200;

    /**
     * ### 201 Created
     * @since 1.0.0
     */
    case CREATED = 201;

    /**
     * ### 202 Accepted
     * @since 1.0.0
     */
    case ACCEPTED = 202;

    /**
     * ### 203 Non-Authoritative Information
     * @since 1.0.0
     */
    case NON_AUTHORITATIVE_INFORMATION = 203;

    /**
     * ### 204 No Content
     * @since 1.0.0
     */
    case NO_CONTENT = 204;

    /**
     * ### 205 Reset Content
     * @since 1.0.0
     */
    case RESET_CONTENT = 205;

    /**
     * ### 206 Partial Content
     * @since 1.0.0
     */
    case PARTIAL_CONTENT = 206;

    /**
     * ### 207 Multi-Status
     * @since 1.0.0
     */
    case MULTI_STATUS = 207;

    /**
     * ### 208 Already Reported
     * @since 1.0.0
     */
    case ALREADY_REPORTED = 208;

    /**
     * ### 226 IM Used
     * @since 1.0.0
     */
    case IM_USED = 226;


    /* * ============================================================
     * Redirection responses (3xx)
     * ============================================================ */

    /**
     * ### 300 Multiple Choices
     * @since 1.0.0
     */
    case MULTIPLE_CHOICES = 300;

    /**
     * ### 301 Moved Permanently
     * @since 1.0.0
     */
    case MOVED_PERMANENTLY = 301;

    /**
     * ### 302 Found
     * @since 1.0.0
     */
    case FOUND = 302;

    /**
     * ### 303 See Other
     * @since 1.0.0
     */
    case SEE_OTHER = 303;

    /**
     * ### 304 Not Modified
     * @since 1.0.0
     */
    case NOT_MODIFIED = 304;

    /**
     * ### 305 Use Proxy (Deprecated)
     *
     * Deprecated HTTP status code retained for compatibility.
     * @since 1.0.0
     */
    case USE_PROXY = 305;

    /**
     * ### 307 Temporary Redirect
     * @since 1.0.0
     */
    case TEMPORARY_REDIRECT = 307;

    /**
     * ### 308 Permanent Redirect
     * @since 1.0.0
     */
    case PERMANENT_REDIRECT = 308;


    /* * ============================================================
     * Client error responses (4xx)
     * ============================================================ */

    /**
     * ### 400 Bad Request
     * @since 1.0.0
     */
    case BAD_REQUEST = 400;

    /**
     * ### 401 Unauthorized
     * @since 1.0.0
     */
    case UNAUTHORIZED = 401;

    /**
     * ### 402 Payment Required
     * @since 1.0.0
     */
    case PAYMENT_REQUIRED = 402;

    /**
     * ### 403 Forbidden
     * @since 1.0.0
     */
    case FORBIDDEN = 403;

    /**
     * ### 404 Not Found
     * @since 1.0.0
     */
    case NOT_FOUND = 404;

    /**
     * ### 405 Method Not Allowed
     * @since 1.0.0
     */
    case METHOD_NOT_ALLOWED = 405;

    /**
     * ### 406 Not Acceptable
     * @since 1.0.0
     */
    case NOT_ACCEPTABLE = 406;

    /**
     * ### 407 Proxy Authentication Required
     * @since 1.0.0
     */
    case PROXY_AUTHENTICATION_REQUIRED = 407;

    /**
     * ### 408 Request Timeout
     * @since 1.0.0
     */
    case REQUEST_TIMEOUT = 408;

    /**
     * ### 409 Conflict
     * @since 1.0.0
     */
    case CONFLICT = 409;

    /**
     * ### 410 Gone
     * @since 1.0.0
     */
    case GONE = 410;

    /**
     * ### 411 Length Required
     * @since 1.0.0
     */
    case LENGTH_REQUIRED = 411;

    /**
     * ### 412 Precondition Failed
     * @since 1.0.0
     */
    case PRECONDITION_FAILED = 412;

    /**
     * ### 413 Content Too Large
     * @since 1.0.0
     */
    case CONTENT_TOO_LARGE = 413;

    /**
     * ### 414 URI Too Long
     * @since 1.0.0
     */
    case URI_TOO_LONG = 414;

    /**
     * ### 415 Unsupported Media Type
     * @since 1.0.0
     */
    case UNSUPPORTED_MEDIA_TYPE = 415;

    /**
     * ### 416 Range Not Satisfiable
     * @since 1.0.0
     */
    case RANGE_NOT_SATISFIABLE = 416;

    /**
     * ### 417 Expectation Failed
     * @since 1.0.0
     */
    case EXPECTATION_FAILED = 417;

    /**
     * ### 418 I'm a Teapot
     * @since 1.0.0
     */
    case IM_A_TEAPOT = 418;

    /**
     * ### 421 Misdirected Request
     * @since 1.0.0
     */
    case MISDIRECTED_REQUEST = 421;

    /**
     * ### 422 Unprocessable Content
     * @since 1.0.0
     */
    case UNPROCESSABLE_CONTENT = 422;

    /**
     * ### 423 Locked
     * @since 1.0.0
     */
    case LOCKED = 423;

    /**
     * ### 424 Failed Dependency
     * @since 1.0.0
     */
    case FAILED_DEPENDENCY = 424;

    /**
     * ### 425 Too Early
     * @since 1.0.0
     */
    case TOO_EARLY = 425;

    /**
     * ### 426 Upgrade Required
     * @since 1.0.0
     */
    case UPGRADE_REQUIRED = 426;

    /**
     * ### 428 Precondition Required
     * @since 1.0.0
     */
    case PRECONDITION_REQUIRED = 428;

    /**
     * ### 429 Too Many Requests
     * @since 1.0.0
     */
    case TOO_MANY_REQUESTS = 429;

    /**
     * ### 431 Request Header Fields Too Large
     * @since 1.0.0
     */
    case REQUEST_HEADER_FIELDS_TOO_LARGE = 431;

    /**
     * ### 451 Unavailable For Legal Reasons
     * @since 1.0.0
     */
    case UNAVAILABLE_FOR_LEGAL_REASONS = 451;


    /* * ============================================================
     * Server error responses (5xx)
     * ============================================================ */

    /**
     * ### 500 Internal Server Error
     * @since 1.0.0
     */
    case INTERNAL_SERVER_ERROR = 500;

    /**
     * ### 501 Not Implemented
     * @since 1.0.0
     */
    case NOT_IMPLEMENTED = 501;

    /**
     * ### 502 Bad Gateway
     * @since 1.0.0
     */
    case BAD_GATEWAY = 502;

    /**
     * ### 503 Service Unavailable
     * @since 1.0.0
     */
    case SERVICE_UNAVAILABLE = 503;

    /**
     * ### 504 Gateway Timeout
     * @since 1.0.0
     */
    case GATEWAY_TIMEOUT = 504;

    /**
     * ### 505 HTTP Version Not Supported
     * @since 1.0.0
     */
    case HTTP_VERSION_NOT_SUPPORTED = 505;

    /**
     * ### 506 Variant Also Negotiates
     * @since 1.0.0
     */
    case VARIANT_ALSO_NEGOTIATES = 506;

    /**
     * ### 507 Insufficient Storage
     * @since 1.0.0
     */
    case INSUFFICIENT_STORAGE = 507;

    /**
     * ### 508 Loop Detected
     * @since 1.0.0
     */
    case LOOP_DETECTED = 508;

    /**
     * ### 510 Not Extended
     *
     * Obsolete extension status retained for compatibility.
     * @since 1.0.0
     */
    case NOT_EXTENDED = 510;

    /**
     * ### 511 Network Authentication Required
     * @since 1.0.0
     */
    case NETWORK_AUTHENTICATION_REQUIRED = 511;

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function isInformational ():bool {

        return $this->value >= 100 && $this->value < 200;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function isSuccessful ():bool {

        return $this->value >= 200 && $this->value < 300;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function isRedirection ():bool {

        return $this->value >= 300 && $this->value < 400;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function isClientError ():bool {

        return $this->value >= 400 && $this->value < 500;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function isServerError ():bool {

        return $this->value >= 500 && $this->value < 600;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function code ():int {

        return $this->value;

    }

    /**
     * @inheritdoc
     *
     * @since 1.0.0
     */
    public function reasonPhrase ():string {

        return match ($this) {

            self::CONTINUE => 'Continue',
            self::SWITCHING_PROTOCOLS => 'Switching Protocols',
            self::PROCESSING => 'Processing',
            self::EARLY_HINTS => 'Early Hints',

            self::OK => 'OK',
            self::CREATED => 'Created',
            self::ACCEPTED => 'Accepted',
            self::NON_AUTHORITATIVE_INFORMATION => 'Non-Authoritative Information',
            self::NO_CONTENT => 'No Content',
            self::RESET_CONTENT => 'Reset Content',
            self::PARTIAL_CONTENT => 'Partial Content',
            self::MULTI_STATUS => 'Multi-Status',
            self::ALREADY_REPORTED => 'Already Reported',
            self::IM_USED => 'IM Used',

            self::MULTIPLE_CHOICES => 'Multiple Choices',
            self::MOVED_PERMANENTLY => 'Moved Permanently',
            self::FOUND => 'Found',
            self::SEE_OTHER => 'See Other',
            self::NOT_MODIFIED => 'Not Modified',
            self::USE_PROXY => 'Use Proxy',
            self::TEMPORARY_REDIRECT => 'Temporary Redirect',
            self::PERMANENT_REDIRECT => 'Permanent Redirect',

            self::BAD_REQUEST => 'Bad Request',
            self::UNAUTHORIZED => 'Unauthorized',
            self::PAYMENT_REQUIRED => 'Payment Required',
            self::FORBIDDEN => 'Forbidden',
            self::NOT_FOUND => 'Not Found',
            self::METHOD_NOT_ALLOWED => 'Method Not Allowed',
            self::NOT_ACCEPTABLE => 'Not Acceptable',
            self::PROXY_AUTHENTICATION_REQUIRED => 'Proxy Authentication Required',
            self::REQUEST_TIMEOUT => 'Request Timeout',
            self::CONFLICT => 'Conflict',
            self::GONE => 'Gone',
            self::LENGTH_REQUIRED => 'Length Required',
            self::PRECONDITION_FAILED => 'Precondition Failed',
            self::CONTENT_TOO_LARGE => 'Content Too Large',
            self::URI_TOO_LONG => 'URI Too Long',
            self::UNSUPPORTED_MEDIA_TYPE => 'Unsupported Media Type',
            self::RANGE_NOT_SATISFIABLE => 'Range Not Satisfiable',
            self::EXPECTATION_FAILED => 'Expectation Failed',
            self::IM_A_TEAPOT => "I'm a teapot",
            self::MISDIRECTED_REQUEST => 'Misdirected Request',
            self::UNPROCESSABLE_CONTENT => 'Unprocessable Content',
            self::LOCKED => 'Locked',
            self::FAILED_DEPENDENCY => 'Failed Dependency',
            self::TOO_EARLY => 'Too Early',
            self::UPGRADE_REQUIRED => 'Upgrade Required',
            self::PRECONDITION_REQUIRED => 'Precondition Required',
            self::TOO_MANY_REQUESTS => 'Too Many Requests',
            self::REQUEST_HEADER_FIELDS_TOO_LARGE => 'Request Header Fields Too Large',
            self::UNAVAILABLE_FOR_LEGAL_REASONS => 'Unavailable For Legal Reasons',

            self::INTERNAL_SERVER_ERROR => 'Internal Server Error',
            self::NOT_IMPLEMENTED => 'Not Implemented',
            self::BAD_GATEWAY => 'Bad Gateway',
            self::SERVICE_UNAVAILABLE => 'Service Unavailable',
            self::GATEWAY_TIMEOUT => 'Gateway Timeout',
            self::HTTP_VERSION_NOT_SUPPORTED => 'HTTP Version Not Supported',
            self::VARIANT_ALSO_NEGOTIATES => 'Variant Also Negotiates',
            self::INSUFFICIENT_STORAGE => 'Insufficient Storage',
            self::LOOP_DETECTED => 'Loop Detected',
            self::NOT_EXTENDED => 'Not Extended',
            self::NETWORK_AUTHENTICATION_REQUIRED => 'Network Authentication Required'

        };

    }

}