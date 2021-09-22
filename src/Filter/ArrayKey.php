<?php

/**
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio
 */

declare(strict_types=1);

namespace Armenio\Filter;

use Laminas\Filter\AbstractFilter;

/**
 * Class ArrayKey
 *
 * @package Armenio\Filter
 */
class ArrayKey extends AbstractFilter
{
    /**
     * @var int[]|string[]
     */
    protected $options = [
        'key' => 0,
    ];

    /**
     * @return int|string
     */
    public function getKey()
    {
        return $this->options['key'];
    }

    /**
     * @param $key
     *
     * @return $this
     */
    public function setKey($key)
    {
        $this->options['key'] = $key;
        return $this;
    }

    /**
     * ArrayKey constructor.
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->setOptions($options);
    }

    /**
     * @param mixed $value
     *
     * @return array|mixed
     */
    public function filter($value)
    {
        if (! is_array($value) || ! array_key_exists($this->getKey(), $value)) {
            return $value;
        }

        return $value[$this->getKey()];
    }
}
