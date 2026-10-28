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

namespace FireHub\Tests\Adapter\HTTP\Unit\Message;

use FireHub\Testing\FireHubTestCase;
use FireHub\Adapter\HTTP\Header\ {
    Name, Value
};
use FireHub\Adapter\HTTP\Message\Headers;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small
};

/**
 * ### Tests the immutable HTTP header collection
 * @since 1.0.0
 */
#[Small]
#[Group('message')]
#[CoversClass(Headers::class)]
final class HeadersTest extends FireHubTestCase {

    /**
     * ### Tests creating an empty collection
     * @since 1.0.0
     *
     * @return void
     */
    public function testEmpty ():void {

        $headers = Headers::empty();

        self::assertSame([], $headers->all());
        self::assertFalse($headers->has(new Name('Accept')));
        self::assertSame([], $headers->get(new Name('Accept')));

    }

    /**
     * ### Tests appending a header
     * @since 1.0.0
     *
     * @return void
     */
    public function testAdd ():void {

        $headers = Headers::empty()
            ->add(new Name('Accept'), new Value('application/json'));

        self::assertCount(1, $headers->all());

        self::assertSame(
            'Accept',
            $headers->all()[0]['name']->value()
        );

        self::assertSame(
            'application/json',
            $headers->all()[0]['value']->value()
        );

    }

    /**
     * ### Tests preserving insertion order
     * @since 1.0.0
     *
     * @return void
     */
    public function testAddPreservesOrder ():void {

        $headers = Headers::empty()
            ->add(new Name('X-First'), new Value('1'))
            ->add(new Name('X-Second'), new Value('2'))
            ->add(new Name('X-Third'), new Value('3'));

        self::assertSame(
            ['X-First', 'X-Second', 'X-Third'],
            array_map(
                static fn(array $header):string => $header['name']->value(),
                $headers->all()
            )
        );

    }

    /**
     * ### Tests preserving interleaved header occurrences
     * @since 1.0.0
     *
     * @return void
     */
    public function testAddPreservesInterleavedOccurrences ():void {

        $headers = Headers::empty()
            ->add(new Name('X-A'), new Value('1'))
            ->add(new Name('X-B'), new Value('2'))
            ->add(new Name('x-a'), new Value('3'));

        self::assertSame(
            [
                ['X-A', '1'],
                ['X-B', '2'],
                ['x-a', '3']
            ],
            array_map(
                static fn(array $header):array => [
                    $header['name']->value(),
                    $header['value']->value()
                ],
                $headers->all()
            )
        );

    }

    /**
     * ### Tests preserving duplicate header values
     * @since 1.0.0
     *
     * @return void
     */
    public function testAddPreservesDuplicateValues ():void {

        $headers = Headers::empty()
            ->add(new Name('X-Custom'), new Value('same'))
            ->add(new Name('X-Custom'), new Value('same'));

        self::assertCount(
            2,
            $headers->get(new Name('X-Custom'))
        );

        self::assertSame(
            ['same', 'same'],
            array_map(
                static fn(Value $value):string => $value->value(),
                $headers->get(new Name('X-Custom'))
            )
        );

    }

    /**
     * ### Tests case-insensitive header lookup
     * @since 1.0.0
     *
     * @return void
     */
    public function testHasIsCaseInsensitive ():void {

        $headers = Headers::empty()
            ->add(new Name('Content-Type'), new Value('application/json'));

        self::assertTrue($headers->has(new Name('Content-Type')));
        self::assertTrue($headers->has(new Name('content-type')));
        self::assertTrue($headers->has(new Name('CONTENT-TYPE')));

        self::assertFalse($headers->has(new Name('Authorization')));

    }

    /**
     * ### Tests retrieving all matching values
     * @since 1.0.0
     *
     * @return void
     */
    public function testGet ():void {

        $headers = Headers::empty()
            ->add(new Name('Accept'), new Value('application/json'))
            ->add(new Name('X-Custom'), new Value('first'))
            ->add(new Name('x-custom'), new Value('second'));

        $values = $headers->get(new Name('X-CUSTOM'));

        self::assertCount(2, $values);

        self::assertSame(
            ['first', 'second'],
            array_map(
                static fn(Value $value):string => $value->value(),
                $values
            )
        );

        self::assertSame(
            [],
            $headers->get(new Name('Authorization'))
        );

    }

    /**
     * ### Tests replacing all occurrences of a header
     * @since 1.0.0
     *
     * @return void
     */
    public function testWithReplacesAllOccurrences ():void {

        $headers = Headers::empty()
            ->add(new Name('X-Custom'), new Value('first'))
            ->add(new Name('Accept'), new Value('application/json'))
            ->add(new Name('x-custom'), new Value('second'));

        $updated = $headers->with(
            new Name('X-Custom'),
            new Value('updated')
        );

        self::assertSame(
            ['updated'],
            array_map(
                static fn(Value $value):string => $value->value(),
                $updated->get(new Name('X-Custom'))
            )
        );

        self::assertCount(2, $updated->all());

    }

    /**
     * ### Tests replacing a header at its first occurrence
     * @since 1.0.0
     *
     * @return void
     */
    public function testWithPreservesFirstOccurrencePosition ():void {

        $headers = Headers::empty()
            ->add(new Name('X-A'), new Value('1'))
            ->add(new Name('X-B'), new Value('2'))
            ->add(new Name('X-A'), new Value('3'))
            ->add(new Name('X-C'), new Value('4'));

        $updated = $headers->with(
            new Name('X-A'),
            new Value('10'),
            new Value('20')
        );

        self::assertSame(
            [
                ['X-A', '10'],
                ['X-A', '20'],
                ['X-B', '2'],
                ['X-C', '4']
            ],
            array_map(
                static fn(array $header):array => [
                    $header['name']->value(),
                    $header['value']->value()
                ],
                $updated->all()
            )
        );

    }

    /**
     * ### Tests appending a replacement when the header is absent
     * @since 1.0.0
     *
     * @return void
     */
    public function testWithAppendsMissingHeader ():void {

        $headers = Headers::empty()
            ->add(new Name('Accept'), new Value('application/json'));

        $updated = $headers->with(
            new Name('X-New'),
            new Value('value')
        );

        self::assertSame(
            ['Accept', 'X-New'],
            array_map(
                static fn(array $header):string => $header['name']->value(),
                $updated->all()
            )
        );

    }

    /**
     * ### Tests removing every occurrence of a header
     * @since 1.0.0
     *
     * @return void
     */
    public function testWithoutRemovesAllOccurrences ():void {

        $headers = Headers::empty()
            ->add(new Name('X-A'), new Value('1'))
            ->add(new Name('X-B'), new Value('2'))
            ->add(new Name('x-a'), new Value('3'));

        $updated = $headers->without(new Name('X-A'));

        self::assertFalse($updated->has(new Name('X-A')));

        self::assertSame(
            ['X-B'],
            array_map(
                static fn(array $header):string => $header['name']->value(),
                $updated->all()
            )
        );

    }

    /**
     * ### Tests that modifications do not mutate the original collection
     * @since 1.0.0
     *
     * @return void
     */
    public function testImmutability ():void {

        $original = Headers::empty()
            ->add(new Name('X-Test'), new Value('original'));

        $added = $original->add(
            new Name('X-Test'),
            new Value('additional')
        );

        $replaced = $original->with(
            new Name('X-Test'),
            new Value('replacement')
        );

        $removed = $original->without(
            new Name('X-Test')
        );

        self::assertCount(1, $original->all());
        self::assertCount(2, $added->all());
        self::assertCount(1, $replaced->all());
        self::assertSame([], $removed->all());

        self::assertSame(
            'original',
            $original->get(new Name('X-Test'))[0]->value()
        );

        self::assertSame(
            'replacement',
            $replaced->get(new Name('X-Test'))[0]->value()
        );

    }

}