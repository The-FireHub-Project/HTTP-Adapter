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
use FireHub\Adapter\HTTP\Header\ClearSiteData\Directive;
use FireHub\Runtime;

/**
 * ### Clear Site Data
 *
 * Represents the Clear-Site-Data HTTP response header.
 *
 * Requests that the browser clear selected categories of site data, including caches, cookies, and storage.
 *
 * The header may also request reloading active execution contexts.
 *
 * This object is immutable. All modifications return new instances.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<string>
 */
final readonly class ClearSiteData extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param array<string, \FireHub\Adapter\HTTP\Header\ClearSiteData\Directive> $directives <p>
     * Configured directives.
     * </p>
     *
     * @return void
     */
    private function __construct (
        private array $directives
    ) {}

    /**
     * ### Creates an empty Clear-Site-Data policy
     * @since 1.0.0
     *
     * @return self Empty policy.
     */
    public static function empty ():self {

        return new self([]);

    }

    /**
     * ### Creates a Clear-Site-Data policy
     *
     * Initializes a policy with one or more directives.
     * @since 1.0.0
     *
     * @uses \FireHub\Adapter\HTTP\Header\ClearSiteData::empty() To create an empty policy.
     * @uses \FireHub\Adapter\HTTP\Header\ClearSiteData::withDirective() To add directives.
     *
     * @param \FireHub\Adapter\HTTP\Header\ClearSiteData\Directive $directive <p>
     * Initial directive.
     * </p>
     * @param \FireHub\Adapter\HTTP\Header\ClearSiteData\Directive ...$additional <p>
     * Additional directives.
     * </p>
     *
     * @return self Configured policy.
     */
    public static function of (Directive $directive, Directive ...$additional):self {

        $policy = self::empty();

        foreach ([$directive, ...$additional] as $item)
            $policy = $policy->withDirective($item);

        return $policy;

    }

    /**
     * ### Adds a directive
     *
     * Adds the specified directive without creating duplicates.
     *
     * The wildcard directive replaces all other directives. Adding a specific directive to a wildcard policy replaces
     * the wildcard with that directive.
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\ClearSiteData\Directive $directive <p>
     * Directive to add.
     * </p>
     *
     * @return self Updated policy.
     */
    public function withDirective (Directive $directive):self {

        if ($directive === Directive::ALL)
            return new self([
                Directive::ALL->value => Directive::ALL
            ]);

        $directives = $this->directives;

        unset($directives[Directive::ALL->value]);

        $directives[$directive->value] = $directive;

        return new self($directives);

    }

    /**
     * ### Removes a directive
     *
     * Removes the specified directive if it exists.
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\ClearSiteData\Directive $directive <p>
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
     * ### Checks whether a directive exists
     * @since 1.0.0
     *
     * @param \FireHub\Adapter\HTTP\Header\ClearSiteData\Directive $directive <p>
     * Directive to check.
     * </p>
     *
     * @return bool True if the directive is configured.
     */
    public function hasDirective (Directive $directive):bool {

        return isset($this->directives[$directive->value]);

    }

    /**
     * ### Checks whether the policy is empty
     * @since 1.0.0
     *
     * @return bool True if no directives are configured.
     */
    public function isEmpty ():bool {

        return $this->directives === [];

    }

    /**
     * ### Returns the configured directives
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::values() To get the directive values.
     *
     * @return list<\FireHub\Adapter\HTTP\Header\ClearSiteData\Directive> Configured directives.
     */
    public function directives ():array {

        return Runtime\Arr\Access::values($this->directives);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To serialize the directives.
     */
    public function value ():string {

        $values = [];

        foreach ($this->directives as $directive)
            $values[] = '"'.$directive->value.'"';

        return Runtime\Str\SB\Delimiter::implode($values, ', ');

    }

    /**
     * ### Returns the normalized comparison value
     *
     * Directive order does not affect policy equality.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Ordering::sortByKeys() To sort the directives by key.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To serialize the directives.
     *
     * @return string Normalized policy.
     */
    protected function comparisonValue ():string {

        $directives = $this->directives;

        Runtime\Arr\Ordering::sortByKeys($directives, type: Runtime\Type\Arr\SortType::STRING);

        $values = [];

        foreach ($directives as $directive)
            $values[] = '"'.$directive->value.'"';

        return Runtime\Str\SB\Delimiter::implode($values, ', ');

    }

}