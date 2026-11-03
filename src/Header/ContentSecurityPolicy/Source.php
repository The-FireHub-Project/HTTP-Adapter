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

namespace FireHub\Adapter\HTTP\Header\ContentSecurityPolicy;

/**
 * ### Defines Content Security Policy source keywords
 *
 * Provides standardized source expressions used to control resource loading, script execution, and style application.
 * @since 1.0.0
 */
enum Source:string implements SourceExpression{

    /**
     * ### Same-origin source
     *
     * Allows resources originating from the same origin as the protected document.
     * @since 1.0.0
     */
    case SELF = "'self'";

    /**
     * ### No sources allowed
     *
     * Disallows all resources for the specified directive.
     *
     * Must be used without additional source expressions to represent an effective deny-all policy.
     * @since 1.0.0
     */
    case NONE = "'none'";

    /**
     * ### Allow unsafe inline content
     *
     * Allows certain inline scripts or styles, depending on the directive and other policy expressions.
     *
     * Weakens protection against cross-site scripting attacks and should generally be avoided.
     * @since 1.0.0
     */
    case UNSAFE_INLINE = "'unsafe-inline'";

    /**
     * ### Allow unsafe JavaScript evaluation
     *
     * Allows JavaScript execution through string evaluation mechanisms such as eval() and Function().
     *
     * Weakens protection against code injection attacks.
     * @since 1.0.0
     */
    case UNSAFE_EVAL = "'unsafe-eval'";

    /**
     * ### Allow WebAssembly compilation
     *
     * Allows WebAssembly compilation and execution without enabling unrestricted JavaScript string evaluation.
     * @since 1.0.0
     */
    case WASM_UNSAFE_EVAL = "'wasm-unsafe-eval'";

    /**
     * ### Strict dynamic script trust
     *
     * Extends trust from authorized scripts to scripts dynamically loaded by those trusted scripts.
     *
     * Typically used with nonce or hash source expressions.
     * @since 1.0.0
     */
    case STRICT_DYNAMIC = "'strict-dynamic'";

    /**
     * ### Allow hashed inline event handlers
     *
     * Enables matching hash source expressions against supported inline event handlers and style attributes.
     * @since 1.0.0
     */
    case UNSAFE_HASHES = "'unsafe-hashes'";

    /**
     * ### Include samples in violation reports
     *
     * Requests that supporting user agents include relevant code samples in CSP violation reports.
     * @since 1.0.0
     */
    case REPORT_SAMPLE = "'report-sample'";

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->value;

    }

}