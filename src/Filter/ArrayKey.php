<?php

/**
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio
 */

namespace Armenio\Filter;

use Laminas\Filter\AbstractFilter;

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
     * @param int|string $key
     */
    public function setKey($key): ArrayKey
    {
        $this->options['key'] = $key;
        return $this;
    }

    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }

    /**
     * @return mixed
     */
    public function filter($value)
    {
        if (! is_array($value) || ! array_key_exists($this->getKey(), $value)) {
            return $value;
        }

        return $value[$this->getKey()];
    }
}
