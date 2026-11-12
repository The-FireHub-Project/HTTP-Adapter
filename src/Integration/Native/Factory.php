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

namespace FireHub\Adapter\HTTP\Integration\Native;

use FireHub\Adapter\HTTP\Request;
use FireHub\Adapter\HTTP\Message\Version;
use FireHub\Adapter\HTTP\Request\ {
    Headers, Method, Target
};
use FireHub\Adapter\HTTP\Request\Method\ {
    Custom, Standard
};
use FireHub\Adapter\HTTP\Request\Target\ {
    Asterisk, Authority, Absolute, Origin, Validator
};
use FireHub\Adapter\HTTP\Header\ {
    Name, Value
};
use FireHub\Adapter\HTTP\Exception\ {
    InvalidServerEnvironmentException, InvalidServerProtocolException
};
use FireHub\Runtime;

/**
 * ### Creates HTTP requests from the native PHP environment
 *
 * Converts native PHP server variables into FireHub HTTP request objects.
 *
 * This factory does not depend on a specific web server.
 *
 * <code>
 * use FireHub\Adapter\HTTP\Integration\Native\Factory;
 *
 * $factory = new Factory([
 *     'SERVER_PROTOCOL' => 'HTTP/1.1',
 *     'REQUEST_METHOD' => 'GET',
 *     'REQUEST_URI' => '/users?page=1'
 * ]);
 *
 * $request = $factory->create();
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Factory {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param array<non-empty-string, mixed> $server <p>
     * Native PHP server variables.
     * </p>
     * @param array<non-empty-string, string> $headers <p>
     * Native HTTP request headers.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private array $server,
        private array $headers
    ) {}

    /**
     * ### Creates a request from the current PHP environment
     *
     * <code>
     * use FireHub\Adapter\HTTP\Integration\Native\Factory;
     *
     * $factory = Factory::fromGlobals();
     *
     * $request = $factory->create();
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Http\Request::headers() To get the native PHP HTTP request headers.
     *
     * @return self HTTP request.
     */
    public static function fromGlobals ():self {

        /** @var array<non-empty-string, mixed> $_SERVER */
        return new self($_SERVER,  Runtime\Http\Request::headers());

    }

    /**
     * ### Creates a request from native PHP server variables
     *
     * <code>
     * use FireHub\Adapter\HTTP\Integration\Native\Factory;
     *
     * $request = Factory::fromGlobals()->create();
     *
     * $request->method()->value();
     * // GET
     *
     * $request->target()->value();
     * // /users?page=1
     *
     * $request->version()->value;
     * // 1.1
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Integration\Native\Factory::version() To resolve the HTTP protocol version.
     * @uses \FireHub\Adapter\HTTP\Integration\Native\Factory::method() To resolve the HTTP request method.
     * @uses \FireHub\Adapter\HTTP\Integration\Native\Factory::target() To resolve the HTTP request target.
     * @uses \FireHub\Adapter\HTTP\Integration\Native\Factory::headers() To resolve HTTP request headers.
     * @uses \FireHub\Adapter\HTTP\Request\Target\Validator::http11() To validate the HTTP/1.1 request target.
     *
     * @throws \FireHub\Adapter\HTTP\Request\Target\Exception\InvalidTargetCombinationException If the combination is
     * invalid.
     * @throws \FireHub\Adapter\HTTP\Exception\InvalidServerProtocolException If the protocol version is not supported.
     * @throws \FireHub\Adapter\HTTP\Request\Method\Exception\InvalidMethodNameException If the method token is invalid.
     * @throws \FireHub\Adapter\HTTP\Request\Target\Exception\InvalidTargetException If the authority is invalid.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidUriException If the authority or port is invalid.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidPortException If port is not between 0 and 65535.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidSchemeException If scheme has invalid pattern.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidPathException If path has invalid pattern.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidQueryException If query has invalid pattern.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidFragmentException If fragment has invalid pattern.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException If the header name is invalid.
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException If the header value is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return \FireHub\Adapter\HTTP\Request HTTP request.
     */
    public function create ():Request {

        $version = $this->version();
        $method = $this->method();
        $target = $this->target($method);
        $headers = $this->headers($this->headers);

        if ($version === Version::HTTP_1_1)
            Validator::http11($method, $target);

        return new Request(
            $version,
            $method,
            $target,
            null,
            $headers
    );

    }

    /**
     * ### Resolves the HTTP protocol version
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Integration\Native\Factory::required() To get the required server variable.
     * @uses \FireHub\Adapter\HTTP\Message\Version::parse() To parse the protocol version.
     *
     * @throws \FireHub\Adapter\HTTP\Exception\InvalidServerEnvironmentException If the variable is missing or invalid.
     * @throws \FireHub\Adapter\HTTP\Exception\InvalidServerProtocolException If the protocol version is not supported.
     *
     * @return \FireHub\Adapter\HTTP\Message\Version HTTP protocol version.
     */
    private function version ():Version {

        $protocol = $this->required('SERVER_PROTOCOL');

        return Version::parse($protocol)
            ?? throw new InvalidServerProtocolException(
                'Unsupported HTTP protocol version.'
            );

    }

    /**
     * ### Resolves the HTTP request method
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Integration\Native\Factory::required() To get the required server variable.
     *
     * @throws \FireHub\Adapter\HTTP\Exception\InvalidServerEnvironmentException If the variable is missing or invalid.
     * @throws \FireHub\Adapter\HTTP\Request\Method\Exception\InvalidMethodNameException If the method token is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return \FireHub\Adapter\HTTP\Request\Method HTTP request method.
     */
    private function method ():Method {

        $method = $this->required('REQUEST_METHOD');

        return Standard::tryFrom($method) ?? new Custom($method);

    }

    /**
     * ### Resolves the HTTP request target
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Integration\Native\Factory::required() To get the required server variable.
     * @uses \FireHub\Adapter\HTTP\Request\Target\Authority::parse() To parse the authority target.
     * @uses \FireHub\Adapter\HTTP\Request\Target\Absolute::parse() To parse the absolute target.
     * @uses \FireHub\Adapter\HTTP\Request\Target\Origin::parse() To parse the origin target.
     * @uses \FireHub\Runtime\Str\SB\Search::startsWith() To check if the target starts with a specific string.
     *
     * @param \FireHub\Adapter\HTTP\Request\Method $method <p>
     * HTTP request method.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Exception\InvalidServerEnvironmentException If the variable is missing or invalid.
     * @throws \FireHub\Adapter\HTTP\Request\Target\Exception\InvalidTargetException If the authority is invalid.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidUriException If the authority or port is invalid.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidPortException If port is not between 0 and 65535.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidSchemeException If scheme has invalid pattern.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidPathException If path has invalid pattern.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidQueryException If query has invalid pattern.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidFragmentException If fragment has invalid pattern.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return \FireHub\Adapter\HTTP\Request\Target HTTP request target.
     */
    private function target (Method $method):Target {

        $target = $this->required('REQUEST_URI');

        return match (true) {
            $target === '*' => Asterisk::ASTERISK,
            $method === Standard::CONNECT => Authority::parse($target),
            Runtime\Str\SB\Search::startsWith('http://', $target),
            Runtime\Str\SB\Search::startsWith('https://', $target)
                => Absolute::parse($target),
            default => Origin::parse($target)
        };

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
     * @param non-empty-string $name <p>
     * Server variable name.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Exception\InvalidServerEnvironmentException If the variable is missing or invalid.
     *
     * @return non-empty-string Server variable value.
     */
    private function required (string $name):string {

        $value = $this->server[$name] ?? null;

        if ($value === '' || !Runtime\DataIs::string($value))
            throw new InvalidServerEnvironmentException(
                "Missing or invalid PHP server variable: $name."
            );

        return $value;

    }

}