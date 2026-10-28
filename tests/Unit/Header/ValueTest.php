<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=7.4
 * @package Tests\Adapter\HTTP
 */

namespace FireHub\Tests\Adapter\HTTP\Unit\Header;

use FireHub\Testing\FireHubTestCase;
use FireHub\Adapter\HTTP\Header\Value;
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldValueException;
use PHPUnit\Framework\Attributes\ {
    CoversClass, DataProvider, Group, Small
};

/**
 * ### Tests HTTP header field values
 * @since 1.0.0
 */
#[Small]
#[Group('header')]
#[CoversClass(Value::class)]
final class ValueTest extends FireHubTestCase {

    /**
     * ### Tests valid HTTP header field values
     * @since 1.0.0
     *
     * @return void
     */
    #[DataProvider('validValues')]
    public function testValidValue (string $value):void {

        $header = new Value($value);

        self::assertSame($value, $header->value());

    }

    /**
     * ### Provides valid HTTP header field values
     * @since 1.0.0
     *
     * @return iterable<string, array{string}>
     */
    public static function validValues ():iterable {

        yield 'empty' => [''];
        yield 'plain text' => ['application/json'];
        yield 'numeric' => ['123'];
        yield 'spaces' => ['hello world'];
        yield 'leading space' => [' value'];
        yield 'trailing space' => ['value '];
        yield 'horizontal tab' => ["value\tdata"];
        yield 'quoted value' => ['"hello world"'];
        yield 'semicolon' => ['text/html; charset=utf-8'];
        yield 'comma separated' => ['gzip, deflate, br'];
        yield 'unicode bytes' => ["\xC4\x8C"];

    }

    /**
     * ### Tests invalid HTTP header field values
     * @since 1.0.0
     *
     * @return void
     */
    #[DataProvider('invalidValues')]
    public function testInvalidValue (string $value):void {

        $this->expectException(InvalidFieldValueException::class);

        new Value($value);

    }

    /**
     * ### Provides invalid HTTP header field values
     * @since 1.0.0
     *
     * @return iterable<string, array{string}>
     */
    public static function invalidValues ():iterable {

        yield 'null byte' => ["value\0data"];
        yield 'line feed' => ["value\ndata"];
        yield 'carriage return' => ["value\rdata"];
        yield 'vertical tab' => ["value\x0Bdata"];
        yield 'form feed' => ["value\x0Cdata"];
        yield 'escape' => ["value\x1Bdata"];
        yield 'delete' => ["value\x7Fdata"];
        yield 'control 0x01' => ["value\x01data"];
        yield 'control 0x08' => ["value\x08data"];
        yield 'control 0x1F' => ["value\x1Fdata"];

    }

}