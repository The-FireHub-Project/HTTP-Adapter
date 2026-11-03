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

namespace FireHub\Adapter\HTTP\Header\PermissionsPolicy;

use FireHub\Core\Type\ValueObject;
use FireHub\Foundation\Uri\Origin;
use FireHub\Runtime;

/**
 * ### Permissions Policy Allowlist
 *
 * Represents an immutable allowlist for a Permissions-Policy directive.
 *
 * Supports denying all origins, allowing all origins, allowing the current origin, and explicitly specifying
 * permitted origins.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Allowlist extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param bool $all <p>
     * Whether all origins are allowed.
     * </p>
     * @param bool $self <p>
     * Whether the current origin is allowed.
     * </p>
     * @param list<\FireHub\Foundation\Uri\Origin> $origins <p>
     * Explicitly allowed origins.
     * </p>
     *
     * @return void
     */
    private function __construct (
        private bool $all,
        private bool $self,
        private array $origins
    ) {}

    /**
     * ### Denies all origins
     * @since 1.0.0
     *
     * @return self Empty allowlist.
     */
    public static function none ():self {

        return new self(false, false, []);

    }

    /**
     * ### Allows the current origin
     *
     * Additional origins may also be permitted.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::values() To get the origin values.
     *
     * @param \FireHub\Foundation\Uri\Origin ...$origins <p>
     * Additional permitted origins.
     * </p>
     *
     * @return self Allowlist containing self and additional origins.
     */
    public static function self (Origin ...$origins):self {

        return new self(false, true, Runtime\Arr\Access::values($origins));

    }

    /**
     * ### Allows all origins
     * @since 1.0.0
     *
     * @return self Unrestricted allowlist.
     */
    public static function all ():self {

        return new self(true, false, []);

    }

    /**
     * ### Allows explicitly specified origins
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::values() To get the origin values.
     *
     * @param \FireHub\Foundation\Uri\Origin $origin <p>
     * First permitted origin.
     * </p>
     * @param \FireHub\Foundation\Uri\Origin ...$additional <p>
     * Additional permitted origins.
     * </p>
     *
     * @return self Origin-specific allowlist.
     */
    public static function origins (Origin $origin, Origin ...$additional):self {

        return new self(
            false,
            false,
            [$origin, ...Runtime\Arr\Access::values($additional)]
        );

    }

    /**
     * ### Returns the serialized allowlist
     *
     * Produces the Structured Fields representation required by the Permissions-Policy HTTP response header.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Origin::value() To serialize the origin.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To serialize the allowlist.
     *
     * @return non-empty-string Serialized allowlist.
     */
    public function value ():string {

        if ($this->all) return '*';

        $members = [];

        if ($this->self) $members[] = 'self';

        foreach ($this->origins as $origin)
            $members[] = '"'.$origin->value().'"';

        return '('.Runtime\Str\SB\Delimiter::implode($members, ' ').')';

    }

    /**
     * ### Returns the normalized comparison value
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Origin::value() To normalize the origin.
     * @uses \FireHub\Runtime\Arr\Access::keys() To get the origin keys.
     * @uses \FireHub\Runtime\Arr\Ordering::sort() To sort the origin keys.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To serialize the allowlist.
     *
     * @return non-empty-string Normalized allowlist.
     */
    protected function comparisonValue ():string {

        if ($this->all) return '*';

        $origins = [];
        foreach ($this->origins as $origin)
            $origins[$origin->value()] = true;

        $keys = Runtime\Arr\Access::keys($origins);
        Runtime\Arr\Ordering::sort($keys, type: Runtime\Type\Arr\SortType::STRING);

        return ($this->self ? 'self|' : 'origins|')
            .Runtime\Str\SB\Delimiter::implode($keys, '|');

    }

}