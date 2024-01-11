<?php

/**
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio
 */

namespace ArmenioTest\Filter;

use Armenio\Filter\ArrayKey as ArrayKeyFilter;
use Laminas\Filter\Exception\ExceptionInterface;
use PHPUnit\Framework\TestCase;

class ArrayKeyTest extends TestCase
{
    public function testOptions(): void
    {
        $filter = new ArrayKeyFilter([
            'key' => 'testKey',
        ]);
        $this->assertEquals('testKey', $filter->getKey());
    }

    public function testGetSetOptions(): void
    {
        $filter = new ArrayKeyFilter();
        $filter->setKey('testKey0');
        $this->assertEquals('testKey0', $filter->getKey());
    }

    public static function basicDataProvider(): array
    {
        $array = [
            0 => 'a',
            1 => 'b',
            2 => 'c',
        ];

        return [
            [
                0,
                $array,
                'a',
            ],
            [
                1,
                $array,
                'b',
            ],
            [
                2,
                $array,
                'c',
            ],
        ];
    }

    /**
     * @dataProvider basicDataProvider
     *
     * @param int|string $key
     * @param mixed $input
     * @param mixed $expected
     *
     * @throws ExceptionInterface
     */
    public function testBasic($key, $input, $expected): void
    {
        $filter = new ArrayKeyFilter([
            'key' => $key,
        ]);
        $this->assertEquals($expected, $filter($input));
    }

    public static function invalidKeyDataProvider(): array
    {
        $array = [
            'a' => 'a',
            'b' => 'b',
            'c' => 'c',
        ];

        return [
            [
                0,
                $array,
                $array,
            ],
            [
                1,
                $array,
                $array,
            ],
            [
                2,
                $array,
                $array,
            ],
        ];
    }

    /**
     * @dataProvider basicDataProvider
     *
     * @param int|string $key
     * @param mixed $input
     * @param mixed $expected
     *
     * @throws ExceptionInterface
     */
    public function testInvalidKey($key, $input, $expected): void
    {
        $filter = new ArrayKeyFilter([
            'key' => $key,
        ]);
        $this->assertEquals($expected, $filter($input));
    }

    public function returnUnfilteredDataProvider(): array
    {
        $object = new \stdClass();
        $resource = opendir(__DIR__);

        return [
            [
                0,
                'string',
                'string',
            ],
            [
                1,
                1,
                1,
            ],
            [
                2,
                -1,
                -1,
            ],
            [
                3,
                1.0,
                1.0,
            ],
            [
                4,
                -1.0,
                -1.0,
            ],
            [
                5,
                true,
                true,
            ],
            [
                6,
                false,
                false,
            ],
            [
                7,
                $object,
                $object,
            ],
            [
                8,
                null,
                null,
            ],
            [
                9,
                $resource,
                $resource,
            ],
        ];
    }

    /**
     * @dataProvider returnUnfilteredDataProvider
     *
     * @param int|string $key
     * @param mixed $input
     * @param mixed $expected
     *
     * @throws ExceptionInterface
     */
    public function testReturnUnfiltered($key, $input, $expected): void
    {
        $filter = new ArrayKeyFilter([
            'key' => $key,
        ]);
        $this->assertEquals($expected, $filter($input));
    }
}
