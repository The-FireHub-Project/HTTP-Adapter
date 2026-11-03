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

namespace FireHub\Adapter\HTTP\Header\ContentSecurityPolicy;

use FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\Source\ {
    Hash, Host, Nonce, Scheme
};
use FireHub\Adapter\HTTP\Header\Exception\InvalidContentSecurityPolicyException;

/**
 * ### Content Security Policy Source Validator
 *
 * Validates source expressions against the requirements of individual Content Security Policy source-list directives.
 *
 * Ensures that source expressions are used only with directives that support their syntax and semantics.
 *
 * This validator does not determine whether a policy provides sufficient protection against application-specific
 * threats.
 * @since 1.0.0
 */
final class SourceValidator {

    /**
     * ### Validates source expressions
     *
     * Checks whether the specified directive supports source expressions and whether each supplied expression is
     * valid for that directive.
     *
     * Duplicate expressions are permitted and may be removed by the policy serializer.
     *
     * The 'none' keyword cannot be combined with other sources.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceValidator::supportsSources() To validate the
     * directive.
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceValidator::validateSource() To validate the source
     * expressions.
     *
     * @param Directive $directive <p>
     * Content Security Policy directive.
     * </p>
     * @param SourceExpression ...$sources <p>
     * Source expressions to validate.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentSecurityPolicyException If the directive or
     * source expressions are invalid.
     *
     * @return void
     */
    public static function validate (Directive $directive, SourceExpression ...$sources):void {

        if (!self::supportsSources($directive))
            throw new InvalidContentSecurityPolicyException(
                'The specified directive does not accept source expressions.'
            );

        $has_none = false; $has_other = false;
        foreach ($sources as $source) {

            self::validateSource($directive, $source);

            if ($source === Source::NONE)
                $has_none = true;
            else
                $has_other = true;

        }

        if ($has_none && $has_other)
            throw new InvalidContentSecurityPolicyException(
                "The 'none' source cannot be combined with other sources."
            );

    }

    /**
     * ### Checks whether a directive accepts source expressions
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\Directive $directive <p>
     * Directive to inspect.
     * </p>
     *
     * @return bool True if the directive accepts source expressions.
     */
    public static function supportsSources (Directive $directive):bool {

        return match ($directive) {
            Directive::DEFAULT_SRC,
            Directive::SCRIPT_SRC,
            Directive::SCRIPT_SRC_ELEM,
            Directive::SCRIPT_SRC_ATTR,
            Directive::STYLE_SRC,
            Directive::STYLE_SRC_ELEM,
            Directive::STYLE_SRC_ATTR,
            Directive::IMG_SRC,
            Directive::FONT_SRC,
            Directive::CONNECT_SRC,
            Directive::MEDIA_SRC,
            Directive::OBJECT_SRC,
            Directive::FRAME_SRC,
            Directive::CHILD_SRC,
            Directive::WORKER_SRC,
            Directive::MANIFEST_SRC,
            Directive::BASE_URI,
            Directive::FORM_ACTION,
            Directive::FRAME_ANCESTORS
                => true,
            default => false

        };

    }

    /**
     * ### Validates a source expression
     *
     * Applies directive-specific restrictions to source keywords and source expression types.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceValidator::validateKeyword() To validate source
     * keywords.
     * @uses \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceValidator::supportsNonceOrHash() To validate
     * nonce or hash sources.
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\Directive $directive <p>
     * Source-list directive.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\SourceExpression $source <p>
     * Source expression to validate.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentSecurityPolicyException If the source expression is not supported.
     *
     * @return void
     */
    private static function validateSource (Directive $directive, SourceExpression $source):void {

        if ($source instanceof Host || $source instanceof Scheme)
            return;

        if ($source instanceof Nonce || $source instanceof Hash) {

            if (!self::supportsNonceOrHash($directive))
                throw new InvalidContentSecurityPolicyException(
                    'The specified directive does not support nonce or hash sources.'
                );

            return;

        }

        if ($source instanceof Source) {

            self::validateKeyword($directive, $source);

            return;

        }

        throw new InvalidContentSecurityPolicyException(
            'Unsupported CSP source expression type.'
        );

    }

    /**
     * ### Validates a CSP source keyword
     *
     * Checks whether the specified source keyword is meaningful for the selected directive.
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\Directive $directive <p>
     * Source-list directive.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\Source $source <p>
     * CSP source keyword.
     * </p>
     *
     * @throws \FireHub\Adapter\HTTP\Header\Exception\InvalidContentSecurityPolicyException If the keyword is not supported.
     *
     * @return void
     */
    private static function validateKeyword (Directive $directive, Source $source):void {

        $allowed = match ($source) {
            Source::SELF,
            Source::NONE
                => true,
            Source::UNSAFE_INLINE
                => self::supportsInline($directive),
            Source::UNSAFE_EVAL,
            Source::WASM_UNSAFE_EVAL,
            Source::STRICT_DYNAMIC
                => $directive === Directive::SCRIPT_SRC || $directive === Directive::DEFAULT_SRC,
            Source::UNSAFE_HASHES
                => self::supportsNonceOrHash($directive),
            Source::REPORT_SAMPLE => match ($directive) {
                Directive::SCRIPT_SRC,
                Directive::SCRIPT_SRC_ELEM,
                Directive::SCRIPT_SRC_ATTR,
                Directive::STYLE_SRC,
                Directive::STYLE_SRC_ELEM,
                Directive::STYLE_SRC_ATTR
                    => true,
                default => false

            }

        };

        if (!$allowed) {
            throw new InvalidContentSecurityPolicyException(
                'The specified source keyword is not supported by this directive.'
            );
        }

    }

    /**
     * ### Checks whether a directive supports nonce or hash sources
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\Directive $directive <p>
     * Directive to inspect.
     * </p>
     *
     * @return bool True if nonce or hash sources are supported.
     */
    private static function supportsNonceOrHash (Directive $directive):bool {

        return match ($directive) {
            Directive::DEFAULT_SRC,
            Directive::SCRIPT_SRC,
            Directive::SCRIPT_SRC_ELEM,
            Directive::SCRIPT_SRC_ATTR,
            Directive::STYLE_SRC,
            Directive::STYLE_SRC_ELEM,
            Directive::STYLE_SRC_ATTR
                => true,
            default => false

        };

    }

    /**
     * ### Checks whether a directive supports unsafe inline content
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\ContentSecurityPolicy\Directive $directive <p>
     * Directive to inspect.
     * </p>
     *
     * @return bool True if the directive supports inline content.
     */
    private static function supportsInline (Directive $directive):bool {

        return match ($directive) {
            Directive::DEFAULT_SRC,
            Directive::SCRIPT_SRC,
            Directive::SCRIPT_SRC_ELEM,
            Directive::SCRIPT_SRC_ATTR,
            Directive::STYLE_SRC,
            Directive::STYLE_SRC_ELEM,
            Directive::STYLE_SRC_ATTR
                => true,
            default => false

        };

    }

}