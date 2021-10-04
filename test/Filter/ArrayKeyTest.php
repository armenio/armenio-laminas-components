<?php

/**
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio
 */

declare(strict_types=1);

namespace ArmenioTest\Filter;

use Armenio\Filter\ArrayKey as ArrayKeyFilter;
use PHPUnit\Framework\TestCase;

/**
 * Class ArrayKeyTest
 *
 * @package ArmenioTest\Filter
 */
class ArrayKeyTest extends TestCase
{
    /**
     * @return void
     */
    public function testOptions()
    {
        $filter = new ArrayKeyFilter([
            'key' => 'testKey',
        ]);
        $this->assertEquals('testKey', $filter->getKey());
    }

    /**
     * @return void
     */
    public function testGetSetOptions()
    {
        $filter = new ArrayKeyFilter();
        $filter->setKey('testKey0');
        $this->assertEquals('testKey0', $filter->getKey());
    }

    public static function basicDataProvider()
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
     * @param $key
     * @param $input
     * @param $expected
     *
     * @dataProvider basicDataProvider
     * @return void
     */
    public function testBasic($key, $input, $expected)
    {
        $filter = new ArrayKeyFilter([
            'key' => $key,
        ]);
        $this->assertEquals($expected, $filter($input));
    }

    public static function invalidKeyDataProvider()
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
     * @param $key
     * @param $input
     * @param $expected
     *
     * @dataProvider basicDataProvider
     * @return void
     */
    public function testInvalidKey($key, $input, $expected)
    {
        $filter = new ArrayKeyFilter([
            'key' => $key,
        ]);
        $this->assertEquals($expected, $filter($input));
    }

    public function returnUnfilteredDataProvider()
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
     * @param $key
     * @param $input
     * @param $expected
     *
     * @dataProvider returnUnfilteredDataProvider
     * @return void
     */
    public function testReturnUnfiltered($key, $input, $expected)
    {
        $filter = new ArrayKeyFilter([
            'key' => $key,
        ]);
        $this->assertEquals($expected, $filter($input));
    }
}
