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

namespace FireHub\Adapter\HTTP\Header;

use FireHub\Core\Type\ValueObject;

use FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\ {
    Directive, Source, SourceExpression, SandboxToken
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidContentSecurityPolicyException;
use FireHub\Runtime;

/**
 * ### Content Security Policy
 *
 * Represents an immutable Content Security Policy.
 *
 * Provides typed configuration for source-list directives, sandbox restrictions, and insecure request upgrades.
 *
 * Each modification returns a new policy instance without changing the original instance.
 *
 * Directive values are validated before serialization.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<string>
 */
final readonly class ContentSecurityPolicy extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param array<string, list<string>> $directives <p>
     * Internal directive map containing serialized values.
     * </p>
     *
     * @return void
     */
    private function __construct (
        private array $directives = []
    ) {}

    /**
     * ### Creates an empty Content Security Policy
     * @since 1.0.0
     *
     * @return self Empty policy.
     */
    public static function create ():self {

        return new self;

    }

    /**
     * ### Sets a source-list directive
     *
     * Replaces the values of a source-list directive.
     *
     * An empty source list is permitted and represents a directive that does not allow matching sources.
     *
     * The 'none' keyword cannot be combined with other source expressions.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy::isSourceDirective() To validate the directive.
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceExpression::value() To validate the source
     * expressions.
     * @uses \FireHub\Runtime\Arr\Inspection::inArray() To validate the source expressions.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To validate the source expressions.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\Directive $directive <p>
     * Source-list directive.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceExpression ...$sources <p>
     * Allowed source expressions.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentSecurityPolicyException If the directive or
     * sources are invalid.
     *
     * @return self Updated policy.
     */
    public function withSources (Directive $directive, SourceExpression ...$sources):self {

        if (!self::isSourceDirective($directive))
            throw new InvalidContentSecurityPolicyException(
                'The specified directive does not accept source expressions.'
            );

        $values = [];

        foreach ($sources as $source) {

            $value = $source->value();

            if (!Runtime\Arr\Inspection::inArray($values, $value))
                $values[] = $value;

        }

        if (
            Runtime\Arr\Inspection::count($values) > 1
            && Runtime\Arr\Inspection::inArray($values, Source::NONE->value)
        ) {
            throw new InvalidContentSecurityPolicyException(
                "The 'none' source cannot be combined with other sources."
            );
        }

        $directives = $this->directives;

        $directives[$directive->value] = $values;

        return new self($directives);

    }

    /**
     * ### Sets the default source directive
     *
     * Defines fallback resource sources for applicable fetch directives.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy::withSources() To validate the sources.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceExpression ...$sources <<p>
     * Allowed sources.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentSecurityPolicyException If the directive or
     * sources are invalid.
     *
     * @return self Updated policy.
     */
    public function defaultSrc (SourceExpression ...$sources):self {

        return $this->withSources(
            Directive::DEFAULT_SRC,
            ...$sources
        );

    }

    /**
     * ### Sets the script source directive
     *
     * Defines allowed JavaScript sources.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy::withSources() To validate the sources.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceExpression ...$sources <p>
     * Allowed sources.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentSecurityPolicyException If the directive or
     * sources are invalid.
     *
     * @return self Updated policy.
     */
    public function scriptSrc (SourceExpression ...$sources):self {

        return $this->withSources(
            Directive::SCRIPT_SRC,
            ...$sources
        );

    }

    /**
     * ### Sets the style source directive
     *
     * Defines allowed stylesheet sources.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy::withSources() To validate the sources.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceExpression ...$sources <p>
     * Allowed sources.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentSecurityPolicyException If the directive or
     * sources are invalid.
     *
     * @return self Updated policy.
     */
    public function styleSrc (SourceExpression ...$sources):self {

        return $this->withSources(
            Directive::STYLE_SRC,
            ...$sources
        );

    }

    /**
     * ### Sets the image source directive
     *
     * Defines allowed image sources.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy::withSources() To validate the sources.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceExpression ...$sources <p>
     * Allowed sources.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentSecurityPolicyException If the directive or
     * sources are invalid.
     *
     * @return self Updated policy.
     */
    public function imgSrc (SourceExpression ...$sources):self {

        return $this->withSources(
            Directive::IMG_SRC,
            ...$sources
        );

    }

    /**
     * ### Sets the font source directive
     *
     * Defines allowed font sources.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy::withSources() To validate the sources.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceExpression ...$sources <p>
     * Allowed sources.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentSecurityPolicyException If the directive or
     * sources are invalid.
     *
     * @return self Updated policy.
     */
    public function fontSrc (SourceExpression ...$sources):self {

        return $this->withSources(
            Directive::FONT_SRC,
            ...$sources
        );

    }

    /**
     * ### Sets the connection source directive
     *
     * Defines allowed destinations for connections initiated through APIs such as Fetch and WebSocket.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy::withSources() To validate the sources.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceExpression ...$sources <p>
     * Allowed sources.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentSecurityPolicyException If the directive or
     * sources are invalid.
     *
     * @return self Updated policy.
     */
    public function connectSrc (SourceExpression ...$sources):self {

        return $this->withSources(
            Directive::CONNECT_SRC,
            ...$sources
        );

    }

    /**
     * ### Sets the media source directive
     *
     * Defines allowed audio and video sources.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy::withSources() To validate the sources.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceExpression ...$sources <p>
     * Allowed sources.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentSecurityPolicyException If the directive or
     * sources are invalid.
     *
     * @return self Updated policy.
     */
    public function mediaSrc (SourceExpression ...$sources):self {

        return $this->withSources(
            Directive::MEDIA_SRC,
            ...$sources
        );

    }

    /**
     * ### Sets the object source directive
     *
     * Defines allowed sources for embedded objects.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy::withSources() To validate the sources.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceExpression ...$sources <p>
     * Allowed sources.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentSecurityPolicyException If the directive or
     * sources are invalid.
     *
     * @return self Updated policy.
     */
    public function objectSrc (SourceExpression ...$sources):self {

        return $this->withSources(
            Directive::OBJECT_SRC,
            ...$sources
        );

    }

    /**
     * ### Sets the frame ancestors directive
     *
     * Defines which origins may embed the protected resource in a frame or similar browsing context.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy::withSources() To validate the sources.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceExpression ...$sources <p>
     * Allowed sources.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentSecurityPolicyException If the directive or
     * sources are invalid.
     *
     * @return self Updated policy.
     */
    public function frameAncestors (SourceExpression ...$sources):self {

        return $this->withSources(
            Directive::FRAME_ANCESTORS,
            ...$sources
        );

    }

    /**
     * ### Sets sandbox restrictions
     *
     * Applies sandbox restrictions to the protected document.
     *
     * An empty token list applies all sandbox restrictions. Each supplied token selectively restores a permission.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Inspection::inArray() To validate the tokens.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SandboxToken ...$tokens <p>
     * Sandbox permissions.
     * </p>
     *
     * @return self Updated policy.
     */
    public function sandbox (SandboxToken ...$tokens):self {

        $values = [];

        foreach ($tokens as $token)
            if (!Runtime\Arr\Inspection::inArray($values, $token->value))
                $values[] = $token->value;

        $directives = $this->directives;

        $directives[Directive::SANDBOX->value] = $values;

        return new self($directives);

    }

    /**
     * ### Enables insecure request upgrades
     *
     * Instructs supporting user agents to upgrade eligible insecure resource requests to secure requests.
     * @since 1.0.0
     *
     * @return self Updated policy.
     */
    public function upgradeInsecureRequests ():self {

        $directives = $this->directives;

        $directives[Directive::UPGRADE_INSECURE_REQUESTS->value] = [];

        return new self($directives);

    }

    /**
     * ### Removes a directive
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\Directive $directive <p>
     * Directive to remove.
     * </p>
     *
     * @return self Updated policy.
     */
    public function withoutDirective (Directive $directive):self {

        $directives = $this->directives;

        unset($directives[$directive->value]);

        return new self($directives);

    }

    /**
     * ### Removes sandbox restrictions
     * @since 1.0.0
     *
     * @return self Updated policy.
     */
    public function withoutSandbox ():self {

        return $this->withoutDirective(Directive::SANDBOX);

    }

    /**
     * ### Disables insecure request upgrades
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy::withoutDirective() To remove the directive.
     *
     * @return self Updated policy.
     */
    public function withoutUpgradeInsecureRequests ():self {

        return $this->withoutDirective(
            Directive::UPGRADE_INSECURE_REQUESTS
        );

    }

    /**
     * ### Checks whether a directive is defined
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::keyExists() To check for the directive.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\Directive $directive <p>
     * Directive to check.
     * </p>
     *
     * @return bool True if the directive is present.
     */
    public function hasDirective (Directive $directive):bool {

        return Runtime\Arr\Access::keyExists(
            $this->directives,
            $directive->value
        );

    }

    /**
     * ### Gets the values of a directive
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\Directive $directive <p>
     * Directive to inspect.
     * </p>
     *
     * @return null|list<string> Directive values, or null if absent.
     */
    public function directive (Directive $directive):?array {

        return $this->directives[$directive->value] ?? null;

    }

    /**
     * ### Serializes the Content Security Policy
     *
     * Converts the configured directives into a valid Content Security Policy header field value.
     *
     * Directives are serialized in insertion order.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To serialize the directives.
     *
     * @return string Serialized Content Security Policy.
     */
    public function value ():string {

        $parts = [];

        foreach ($this->directives as $directive => $values) {

            $parts[] = $directive.(
                $values !== []
                    ? ' '.Runtime\Str\SB\Delimiter::implode($values, ' ')
                    : ''
                );

        }

        return Runtime\Str\SB\Delimiter::implode($parts, '; ');

    }

    /**
     * ### Checks whether a directive accepts source expressions
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\Directive $directive <p>
     * Directive to check.
     * </p>
     *
     * @return bool True if the directive accepts source expressions.
     */
    private static function isSourceDirective (Directive $directive):bool {

        return match ($directive) {
            Directive::DEFAULT_SRC, Directive::SCRIPT_SRC, Directive::SCRIPT_SRC_ELEM, Directive::SCRIPT_SRC_ATTR,
            Directive::STYLE_SRC, Directive::STYLE_SRC_ELEM, Directive::STYLE_SRC_ATTR, Directive::IMG_SRC,
            Directive::FONT_SRC, Directive::CONNECT_SRC, Directive::MEDIA_SRC, Directive::OBJECT_SRC,
            Directive::FRAME_SRC, Directive::CHILD_SRC, Directive::WORKER_SRC, Directive::MANIFEST_SRC,
            Directive::BASE_URI, Directive::FORM_ACTION, Directive::FRAME_ANCESTORS
                => true,
            default => false
        };

    }

}