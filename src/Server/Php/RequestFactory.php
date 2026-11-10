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

namespace FireHub\Adapter\HTTP\Server\Php;

use FireHub\Adapter\HTTP\Request;
use FireHub\Adapter\HTTP\Request\ {
    Method\Standard, Method\Custom,
    Target\Origin,
    Headers, Method, Target
};
use FireHub\Adapter\HTTP\Message\Version;
use FireHub\Adapter\HTTP\Header\ {
    Name, Value
};
use FireHub\Adapter\HTTP\Body\Stream;
use FireHub\Foundation\IO\Stream\Input;
use FireHub\Adapter\HTTP\Exception\ {
    InvalidServerEnvironmentException, InvalidServerProtocolException
};
use FireHub\Runtime;

/**
 * ### PHP HTTP Server Request Factory
 *
 * Creates HTTP request instances from the standard PHP HTTP execution environment.
 *
 * Reads PHP server variables and HTTP headers when a request is created, ensuring that each request uses a fresh
 * snapshot of the current execution environment.
 *
 * Converts server-provided request metadata into FireHub HTTP value objects without parsing or consuming the request
 * body.
 *
 * The request body is exposed as a readable input stream.
 * @since 1.0.0
 */
final readonly class RequestFactory {

    /**
     * ### Creates an HTTP request
     *
     * Creates a FireHub HTTP request from the current PHP execution environment.
     *
     * Captures server variables and HTTP headers for the current request.
     *
     * The request body is backed by php://input and is not loaded into memory during request construction.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Server\Php\RequestFactory::method() To resolve the HTTP request method.
     * @uses \FireHub\Adapter\HTTP\Server\Php\RequestFactory::target() To resolve the HTTP request target.
     * @uses \FireHub\Adapter\HTTP\Server\Php\RequestFactory::version() To resolve the HTTP protocol version.
     * @uses \FireHub\Adapter\HTTP\Server\Php\RequestFactory::headers() To resolve HTTP request headers.
     * @uses \FireHub\Runtime\Http\Request::headers() To retrieve the HTTP request headers.
     *
     * @throws \FireHub\Adapter\HTTP\Exception\InvalidServerEnvironmentException If the request method is missing or
     * invalid.
     * @throws \FireHub\Adapter\HTTP\Request\Method\Exception\InvalidMethodNameException If the method token is invalid.
     * @throws \FireHub\Adapter\HTTP\Request\Target\Exception\InvalidTargetException If the URI contains unsupported
     * components.
     * @throws \FireHub\Adapter\HTTP\Exception\InvalidServerProtocolException If the HTTP protocol is invalid or
     * unsupported.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidUriException If the URI is invalid.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidSchemeException If scheme has invalid pattern.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidPortException If port is not between 0 and 65535.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidPathException If path has invalid pattern.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidQueryException If query has invalid pattern.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidFragmentException If fragment has invalid pattern.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If the header name is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the header value is invalid.
     * @throws \FireHub\Adapter\HTTP\Body\Exception\InvalidStreamBodyException If the stream is invalid.
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the input stream cannot be opened.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return \FireHub\Adapter\HTTP\Request HTTP request.
     */
    public function create ():Request {

        /** @var array<non-empty-string, mixed> $server */
        $server = $_SERVER;

        /** @var array<non-empty-string, string> $headers */
        $headers = Runtime\Http\Request::headers();

        return new Request(
            $this->method($server),
            $this->target($server),
            $this->version($server),
            $this->headers($headers),
            new Stream(new Input)
        );

    }

    /**
     * ### Resolves the HTTP request method
     *
     * Converts the server-provided HTTP method into a standard or custom HTTP method representation.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Server\Php\RequestFactory::required() To retrieve the required request method.
     *
     * @param array<string, mixed> $server <p>
     * PHP server variables.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Exception\InvalidServerEnvironmentException If the request method is missing or
     * invalid.
     * @throws \FireHub\Adapter\HTTP\Request\Method\Exception\InvalidMethodNameException If the method token is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return \FireHub\Adapter\HTTP\Request\Method HTTP method.
     */
    private function method (array $server):Method {

        $method = $this->required($server, 'REQUEST_METHOD');

        return Standard::tryFrom($method) ?? new Custom($method);

    }

    /**
     * ### Resolves the HTTP request target
     *
     * Parses the server-provided request URI into an HTTP origin-form request target.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Server\Php\RequestFactory::required() To retrieve the required request URI.
     * @uses \FireHub\Adapter\HTTP\Request\Target\Origin::parse() To parse the HTTP request target.
     *
     * @param array<string, mixed> $server <p>
     * PHP server variables.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Exception\InvalidServerEnvironmentException If the request URI is missing or
     * invalid.
     * @throws \FireHub\Adapter\HTTP\Request\Target\Exception\InvalidTargetException If the URI contains unsupported
     * components.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidUriException If the URI is invalid.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidSchemeException If scheme has invalid pattern.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidPortException If port is not between 0 and 65535.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidPathException If path has invalid pattern.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidQueryException If query has invalid pattern.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidFragmentException If fragment has invalid pattern.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return \FireHub\Adapter\HTTP\Request\Target HTTP target.
     */
    private function target (array $server):Target {

        return Origin::parse(
            $this->required($server, 'REQUEST_URI')
        );

    }

    /**
     * ### Resolves the HTTP protocol version
     *
     * Validates the HTTP protocol identifier and resolves the corresponding supported HTTP version.
     *
     * The protocol identifier must begin with HTTP/.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Server\Php\RequestFactory::required() To retrieve the required server protocol.
     * @uses \FireHub\Runtime\Str\SB\Search::startsWith() To validate the HTTP protocol prefix.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To extract the HTTP version.
     * @uses \FireHub\Adapter\HTTP\Message\Version::parse() To resolve the HTTP protocol version.
     *
     * @param array<string, mixed> $server <p>
     * PHP server variables.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Exception\InvalidServerEnvironmentException If the server protocol is missing or
     * invalid.
     * @throws \FireHub\Adapter\HTTP\Exception\InvalidServerProtocolException If the HTTP protocol is invalid or
     * unsupported.
     *
     * @return \FireHub\Adapter\HTTP\Message\Version HTTP version.
     */
    private function version (array $server):Version {

        $protocol = $this->required($server, 'SERVER_PROTOCOL');

        if (!Runtime\Str\SB\Search::startsWith('HTTP/', $protocol))
            throw new InvalidServerProtocolException(
                "Invalid HTTP server protocol: $protocol."
            );

        return Version::parse(
            Runtime\Str\SB\Access::part($protocol, 5)
        ) ?? throw new InvalidServerProtocolException(
            "Unsupported HTTP server protocol: $protocol."
        );

    }

    /**
     * ### Resolves HTTP request headers
     *
     * Converts PHP-provided HTTP header names and values into a FireHub HTTP headers collection.
     *
     * Empty Content-Type values are ignored because some PHP server environments expose them even when no
     * Content-Type header was provided by the client.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Request\Headers::empty() To create an empty headers collection.
     * @uses \FireHub\Adapter\HTTP\Request\Headers::add() To add an HTTP header.
     * @uses \FireHub\Runtime\Str\SB\Compare::lexical() To ignore empty Content-Type values.
     *
     * @param array<non-empty-string, string> $values <p>
     * HTTP header values.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If the header name is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the header value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return \FireHub\Adapter\HTTP\Request\Headers HTTP headers.
     */
    private function headers (array $values):Headers {

        $headers = Headers::empty();

        foreach ($values as $name => $value) {

            if (
                $value === ''
                && Runtime\Str\SB\Compare::lexical($name, 'Content-Type') === 0
            ) continue;

            $headers = $headers->add(
                new Name($name),
                new Value($value)
            );

        }

        return $headers;

    }

    /**
     * ### Gets a required PHP server variable
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\DataIs::string() To validate the variable value.
     *
     * @param array<string, mixed> $server <p>
     * Server variables.
     * </p>
     * @param non-empty-string $name <p>
     * Server variable name.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Exception\InvalidServerEnvironmentException If the variable is missing or invalid.
     *
     * @return non-empty-string Server variable value.
     */
    private function required (array $server, string $name):string {

        $value = $server[$name] ?? null;

        if ($value === '' || !Runtime\DataIs::string($value))
            throw new InvalidServerEnvironmentException(
                "Missing or invalid PHP server variable: $name."
            );

        return $value;

    }

}