<?php

/**
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio
 */

namespace Armenio\Filter;

use Laminas\Filter\AbstractFilter;

/**
 * Class StringReplace
 *
 * @package Armenio\Filter
 */
class StringReplace extends AbstractFilter
{
    /**
     * @var string[]
     */
    protected $options = [
        'search' => '',
        'replace' => '',
    ];

    /**
     * @return string
     */
    public function getSearch()
    {
        return $this->options['search'];
    }

    /**
     * @param string $search
     *
     * @return $this
     */
    public function setSearch($search = '')
    {
        $this->options['search'] = (string)$search;
        return $this;
    }

    /**
     * @return string
     */
    public function getReplace()
    {
        return $this->options['replace'];
    }

    /**
     * @param string $replace
     *
     * @return $this
     */
    public function setReplace($replace = '')
    {
        $this->options['replace'] = (string)$replace;
        return $this;
    }

    /**
     * StringReplace constructor.
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
     * @return array|bool|float|int|mixed|string|string[]
     */
    public function filter($value)
    {
        if (! is_scalar($value) && ! is_array($value)) {
            return $value;
        }

        return str_replace($this->getSearch(), $this->getReplace(), $value);
    }
}
