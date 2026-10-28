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
use FireHub\Adapter\HTTP\Header\Name;
use FireHub\Adapter\HTTP\Header\Exception\InvalidFieldNameException;
use PHPUnit\Framework\Attributes\ {
    CoversClass, DataProvider, Group, Small
};

/**
 * ### Tests HTTP header field names
 * @since 1.0.0
 */
#[Small]
#[Group('header')]
#[CoversClass(Name::class)]
final class NameTest extends FireHubTestCase {

    /**
     * ### Tests valid HTTP header field names
     * @since 1.0.0
     *
     * @return void
     */
    #[DataProvider('validNames')]
    public function testValidName (string $name):void {

        $header = new Name($name);

        self::assertSame($name, $header->value());

    }

    /**
     * ### Provides valid HTTP header field names
     * @since 1.0.0
     *
     * @return iterable<string, array{string}>
     */
    public static function validNames ():iterable {

        yield 'standard' => ['Content-Type'];
        yield 'lowercase' => ['content-type'];
        yield 'uppercase' => ['CONTENT-TYPE'];
        yield 'single character' => ['X'];
        yield 'digits' => ['X-123'];
        yield 'underscore' => ['X_Custom'];
        yield 'period' => ['X.Custom'];
        yield 'exclamation' => ['X!Custom'];
        yield 'hash' => ['X#Custom'];
        yield 'dollar' => ['X$Custom'];
        yield 'percent' => ['X%Custom'];
        yield 'ampersand' => ['X&Custom'];
        yield 'apostrophe' => ["X'Custom"];
        yield 'asterisk' => ['X*Custom'];
        yield 'plus' => ['X+Custom'];
        yield 'caret' => ['X^Custom'];
        yield 'backtick' => ['X`Custom'];
        yield 'pipe' => ['X|Custom'];
        yield 'tilde' => ['X~Custom'];

    }

    /**
     * ### Tests invalid HTTP header field names
     * @since 1.0.0
     *
     * @return void
     */
    #[DataProvider('invalidNames')]
    public function testInvalidName (string $name):void {

        $this->expectException(InvalidFieldNameException::class);

        new Name($name);

    }

    /**
     * ### Provides invalid HTTP header field names
     * @since 1.0.0
     *
     * @return iterable<string, array{string}>
     */
    public static function invalidNames ():iterable {

        yield 'empty' => [''];
        yield 'space' => ['Content Type'];
        yield 'leading space' => [' Content-Type'];
        yield 'trailing space' => ['Content-Type '];
        yield 'colon' => ['Content:Type'];
        yield 'slash' => ['Content/Type'];
        yield 'backslash' => ['Content\\Type'];
        yield 'question mark' => ['Content?Type'];
        yield 'at sign' => ['Content@Type'];
        yield 'parentheses' => ['Content(Type)'];
        yield 'brackets' => ['Content[Type]'];
        yield 'braces' => ['Content{Type}'];
        yield 'equals' => ['Content=Type'];
        yield 'comma' => ['Content,Type'];
        yield 'semicolon' => ['Content;Type'];
        yield 'double quote' => ['Content"Type'];
        yield 'tab' => ["Content\tType"];
        yield 'newline' => ["Content\nType"];
        yield 'carriage return' => ["Content\rType"];
        yield 'null byte' => ["Content\0Type"];
        yield 'non ASCII' => ['Čustom'];

    }

}