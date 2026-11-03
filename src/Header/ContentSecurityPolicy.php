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

use FireHub\Adapter\HTTP\Header\Reporting\Group;
use FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\ {
    TrustedTypes\PolicyName, TrustedTypes\Keyword,
    Directive, SandboxToken, SourceExpression, SourceValidator
};
use FireHub\Foundation\Uri;
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
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceValidator::validate() To validate the sources.
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceExpression::value() To get the source expression.
     * @uses \FireHub\Runtime\Arr\Inspection::inArray() To validate the source expressions.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\Directive $directive <p>
     * Source-list directive.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceExpression ...$sources <p>
     * Allowed source expressions.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentSecurityPolicyException If the directive or
     * source expressions are invalid.
     *
     * @return self Updated policy.
     */
    public function withSources (Directive $directive, SourceExpression ...$sources):self {

        SourceValidator::validate($directive, ...$sources);

        $values = [];

        foreach ($sources as $source) {

            $value = $source->value();

            if (!Runtime\Arr\Inspection::inArray($values, $value))
                $values[] = $value;

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
     * ### Sets the script element source directive
     *
     * Defines allowed sources for JavaScript loaded or executed through script elements.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy::withSources() To validate and configure the sources.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceExpression ...$sources <p>
     * Allowed script element sources.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentSecurityPolicyException If the sources are invalid.
     *
     * @return self Updated policy.
     */
    public function scriptSrcElem (SourceExpression ...$sources):self {

        return $this->withSources(
            Directive::SCRIPT_SRC_ELEM,
            ...$sources
        );

    }

    /**
     * ### Sets the script attribute source directive
     *
     * Defines restrictions for inline JavaScript event handler attributes, such as onclick and onload.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy::withSources() To validate and configure the sources.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceExpression ...$sources <p>
     * Allowed script attribute sources.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentSecurityPolicyException If the sources are invalid.
     *
     * @return self Updated policy.
     */
    public function scriptSrcAttr (SourceExpression ...$sources):self {

        return $this->withSources(
            Directive::SCRIPT_SRC_ATTR,
            ...$sources
        );

    }

    /**
     * ### Sets the style element source directive
     *
     * Defines allowed sources for stylesheets loaded through link elements and styles declared within style elements.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy::withSources() To validate and configure the sources.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceExpression ...$sources <p>
     * Allowed style element sources.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentSecurityPolicyException If the sources are invalid.
     *
     * @return self Updated policy.
     */
    public function styleSrcElem (SourceExpression ...$sources):self {

        return $this->withSources(
            Directive::STYLE_SRC_ELEM,
            ...$sources
        );

    }

    /**
     * ### Sets the style attribute source directive
     *
     * Defines restrictions for inline CSS declarations specified through HTML style attributes.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy::withSources() To validate and configure the sources.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceExpression ...$sources <p>
     * Allowed style attribute sources.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentSecurityPolicyException If the sources are invalid.
     *
     * @return self Updated policy.
     */
    public function styleSrcAttr (SourceExpression ...$sources):self {

        return $this->withSources(
            Directive::STYLE_SRC_ATTR,
            ...$sources
        );

    }

    /**
     * ### Sets the frame source directive
     *
     * Defines allowed sources for nested browsing contexts, including iframe elements.
     *
     * This directive controls which frames the document may load, not which origins may embed the document.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy::withSources() To validate and configure the sources.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceExpression ...$sources <p>
     * Allowed frame sources.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentSecurityPolicyException If the sources are invalid.
     *
     * @return self Updated policy.
     */
    public function frameSrc (SourceExpression ...$sources):self {

        return $this->withSources(
            Directive::FRAME_SRC,
            ...$sources
        );

    }

    /**
     * ### Sets the child source directive
     *
     * Defines allowed sources for nested browsing contexts and workers, subject to Content Security Policy fallback
     * rules.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy::withSources() To validate and configure the sources.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceExpression ...$sources <p>
     * Allowed child sources.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentSecurityPolicyException If the sources are invalid.
     *
     * @return self Updated policy.
     */
    public function childSrc (SourceExpression ...$sources):self {

        return $this->withSources(
            Directive::CHILD_SRC,
            ...$sources
        );

    }

    /**
     * ### Sets the worker source directive
     *
     * Defines allowed sources for Worker, SharedWorker, and ServiceWorker scripts.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy::withSources() To validate and configure the sources.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceExpression ...$sources <p>
     * Allowed worker sources.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentSecurityPolicyException If the sources are invalid.
     *
     * @return self Updated policy.
     */
    public function workerSrc (SourceExpression ...$sources):self {

        return $this->withSources(
            Directive::WORKER_SRC,
            ...$sources
        );

    }

    /**
     * ### Sets the manifest source directive
     *
     * Defines allowed sources for web application manifests referenced by the protected document.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy::withSources() To validate and configure the sources.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceExpression ...$sources <p>
     * Allowed manifest sources.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentSecurityPolicyException If the sources are invalid.
     *
     * @return self Updated policy.
     */
    public function manifestSrc (SourceExpression ...$sources):self {

        return $this->withSources(
            Directive::MANIFEST_SRC,
            ...$sources
        );

    }

    /**
     * ### Sets the base URI directive
     *
     * Restricts the URLs that may be used as the document's base URL through the HTML base element.
     *
     * This directive does not fall back to default-src.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy::withSources() To validate and configure the sources.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceExpression ...$sources <p>
     * Allowed base URI sources.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentSecurityPolicyException If the sources are invalid.
     *
     * @return self Updated policy.
     */
    public function baseUri (SourceExpression ...$sources):self {

        return $this->withSources(
            Directive::BASE_URI,
            ...$sources
        );

    }

    /**
     * ### Sets the form action directive
     *
     * Restricts the destinations to which forms may submit their data.
     *
     * This directive does not fall back to default-src.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy::withSources() To validate and configure the sources.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceExpression ...$sources <p>
     * Allowed form submission destinations.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentSecurityPolicyException If the sources are invalid.
     *
     * @return self Updated policy.
     */
    public function formAction (SourceExpression ...$sources):self {

        return $this->withSources(
            Directive::FORM_ACTION,
            ...$sources
        );

    }

    /**
     * ### Sets the Content Security Policy reporting URIs
     *
     * Defines one or more endpoints to which supporting user agents may send Content Security Policy violation reports.
     *
     * The report-uri directive is deprecated in favor of report-to, but remains useful for compatibility with older
     * browsers.
     *
     * Relative URI references are supported and resolved by the user agent against the protected document's URL.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri::value() To validate the URI.
     * @uses \FireHub\Runtime\Arr\Inspection::inArray() To validate the URIs.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the URIs.
     *
     * @param \FireHub\Foundation\Uri $uri <p>
     * First reporting endpoint URI.
     * </p>
     * @param \FireHub\Foundation\Uri ...$additional <p>
     * Additional reporting endpoint URIs.
     * </p>
     *
     * @return self Updated policy.
     */
    public function reportUri (Uri $uri, Uri ...$additional):self {

        $values = [];

        foreach ([$uri, ...$additional] as $endpoint) {

            $value = $endpoint->value();

            if ($value === '' || Runtime\Str\SB\Regex::match('/[\x00-\x20\x7F;,]/', $value))
                throw new InvalidContentSecurityPolicyException(
                    'Invalid CSP reporting endpoint URI.'
                );

            if (!Runtime\Arr\Inspection::inArray($values, $value))
                $values[] = $value;

        }

        $directives = $this->directives;

        $directives[Directive::REPORT_URI->value] = $values;

        return new self($directives);

    }

    /**
     * ### Removes the Content Security Policy reporting URIs
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy::withoutDirective() To remove the directive.
     *
     * @return self Updated policy.
     */
    public function withoutReportUri ():self {

        return $this->withoutDirective(Directive::REPORT_URI);

    }

    /**
     * ### Sets the Content Security Policy reporting group
     *
     * Configures the reporting group used to deliver Content Security Policy violation reports.
     *
     * The reporting group should be declared using the Reporting-Endpoints HTTP response header.
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\Reporting\Group $group <p>
     * Reporting endpoint group.
     * </p>
     *
     * @return self Updated policy.
     */
    public function reportTo (Group $group):self {

        $directives = $this->directives;

        $directives[Directive::REPORT_TO->value] = [
            $group->value()
        ];

        return new self($directives);

    }

    /**
     * ### Removes the Content Security Policy reporting group
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy::withoutDirective() To remove the directive.
     *
     * @return self Updated policy.
     */
    public function withoutReportTo ():self {

        return $this->withoutDirective(Directive::REPORT_TO);

    }

    /**
     * ### Sets the Trusted Types directive
     *
     * Restricts the Trusted Types policies that may be created by JavaScript within the protected document.
     *
     * Supports explicit policy names and the special 'none' and 'allow-duplicates' keywords.
     *
     * The 'none' keyword cannot be combined with any other policy name or keyword.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\TrustedTypes\PolicyName::value() To validate the policy name.
     * @uses \FireHub\Runtime\Arr\Inspection::inArray() To validate the policy names and keywords.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To validate the number of policies.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\TrustedTypes\PolicyName|\FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\TrustedTypes\Keyword ...$policies <p>
     * Allowed Trusted Types policy names and keywords.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentSecurityPolicyException If the directive configuration is invalid.
     *
     * @return self Updated policy.
     */
    public function trustedTypes (PolicyName|Keyword ...$policies):self {

        $values = [];

        $has_none = false;

        foreach ($policies as $policy) {

            $value = $policy instanceof PolicyName
                ? $policy->value()
                : $policy->value;

            if ($policy instanceof PolicyName)
                $value = $policy->value();

            if ($policy === Keyword::NONE)
                $has_none = true;

            if (!Runtime\Arr\Inspection::inArray($values, $value))
                $values[] = $value;

        }

        if ($has_none && Runtime\Arr\Inspection::count($values) > 1)
            throw new InvalidContentSecurityPolicyException(
                "The 'none' keyword cannot be combined with other Trusted Types policies."
            );

        $directives = $this->directives;

        $directives[Directive::TRUSTED_TYPES->value] = $values;

        return new self($directives);

    }

    /**
     * ### Requires Trusted Types for script injection sinks
     *
     * Enables Trusted Types enforcement for supported DOM APIs that accept potentially dangerous script-related
     * content.
     *
     * This directive helps prevent DOM-based cross-site scripting by requiring Trusted Types values at supported
     * injection sinks.
     * @since 1.0.0
     *
     * @return self Updated policy.
     */
    public function requireTrustedTypesFor ():self {

        $directives = $this->directives;

        $directives[Directive::REQUIRE_TRUSTED_TYPES_FOR->value] = [
            "'script'"
        ];

        return new self($directives);

    }

    /**
     * ### Removes the Trusted Types directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy::withoutDirective() To remove the directive.
     *
     * @return self Updated policy.
     */
    public function withoutTrustedTypes ():self {

        return $this->withoutDirective(Directive::TRUSTED_TYPES);

    }

    /**
     * ### Removes the Trusted Types enforcement directive
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy::withoutDirective() To remove the directive.
     *
     * @return self Updated policy.
     */
    public function withoutRequireTrustedTypesFor ():self {

        return $this->withoutDirective(
            Directive::REQUIRE_TRUSTED_TYPES_FOR
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

}