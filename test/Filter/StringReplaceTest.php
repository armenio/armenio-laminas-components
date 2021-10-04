<?php

/**
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio
 */

declare(strict_types=1);

namespace ArmenioTest\Filter;

use Armenio\Filter\StringReplace as StringReplaceFilter;
use PHPUnit\Framework\TestCase;

/**
 * Class StringReplaceTest
 *
 * @package ArmenioTest\Filter
 */
class StringReplaceTest extends TestCase
{
    /**
     * @return void
     */
    public function testOptions()
    {
        $filter = new StringReplaceFilter([
            'search' => 1.1,
            'replace' => 'strongToReplace',
        ]);
        $this->assertEquals('1.1', $filter->getSearch());
        $this->assertEquals('strongToReplace', $filter->getReplace());
    }

    /**
     * @return void
     */
    public function testGetSetOptions()
    {
        $filter = new StringReplaceFilter();

        $this->assertEquals('', $filter->getSearch());
        $this->assertEquals('', $filter->getReplace());

        $filter->setSearch('stringToSearch');
        $filter->setReplace(2.0000);

        $this->assertEquals('stringToSearch', $filter->getSearch());
        $this->assertEquals('2', $filter->getReplace());
    }

    public static function basicDataProvider()
    {
        $string = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return [
            [
                '0123456789',
                '',
                $string,
                'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            ],
            [
                '0123456789',
                '012345',
                $string,
                '012345abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            ],
            [
                'abcdefghijklm',
                '',
                $string,
                '0123456789nopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            ],
            [
                'mnopqrstuvwxyz',
                '',
                $string,
                '0123456789abcdefghijklABCDEFGHIJKLMNOPQRSTUVWXYZ',
            ],
            [
                'ABCDEFGHIJKLM',
                '',
                $string,
                '0123456789abcdefghijklmnopqrstuvwxyzNOPQRSTUVWXYZ',
            ],
            [
                'NOPQRSTUVWXYZ',
                '',
                $string,
                '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLM',
            ],
            [
                'abcdefg',
                'abcdefgabcdefg',
                $string,
                '0123456789abcdefgabcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            ],
            [
                'abc',
                '__',
                $string . $string,
                '0123456789__defghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' .
                '0123456789__defghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            ],
            [
                123456789,
                '',
                $string,
                '0abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            ],
            [
                123456789.0,
                '',
                $string,
                '0abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            ],
            [
                true,
                '',
                $string,
                '023456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            ],
            [
                false,
                '',
                $string,
                '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            ],
            [
                '0123456789',
                '',
                [
                    $string,
                    $string,
                ],
                [
                    'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
                    'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
                ],
            ],
            [
                null,
                '',
                $string,
                '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            ],
        ];
    }

    /**
     * @param $search
     * @param $replace
     * @param $subject
     * @param $expected
     *
     * @dataProvider basicDataProvider
     * @return void
     */
    public function testBasic($search, $replace, $subject, $expected)
    {
        $filter = new StringReplaceFilter([
            'search' => $search,
            'replace' => $replace,
        ]);
        $this->assertEquals($expected, $filter($subject));
    }

    public function returnUnfilteredDataProvider()
    {
        $object = new \stdClass();
        $resource = opendir(__DIR__);

        return [
            [
                '',
                '',
                $object,
                $object,
            ],
            [
                '',
                '',
                $resource,
                $resource,
            ],
        ];
    }

    /**
     * @param $search
     * @param $replace
     * @param $subject
     * @param $expected
     *
     * @dataProvider returnUnfilteredDataProvider
     * @return void
     */
    public function testReturnUnfiltered($search, $replace, $subject, $expected)
    {
        $filter = new StringReplaceFilter([
            'search' => $search,
            'replace' => $replace,
        ]);
        $this->assertEquals($expected, $filter($subject));
    }

    /**
     * @return void
     */
    public function testDefaultOptions()
    {
        $expected = $subject = 'stringExpected';
        $filter = new StringReplaceFilter();
        $this->assertEquals($expected, $filter($subject));
    }
}
