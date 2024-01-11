<?php

/**
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio
 */

namespace Armenio\Filter;

use Laminas\Filter\AbstractFilter;

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
     * @param mixed|string $search
     */
    public function setSearch($search = ''): StringReplace
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
     * @param mixed|string $replace
     */
    public function setReplace($replace = ''): StringReplace
    {
        $this->options['replace'] = (string)$replace;
        return $this;
    }

    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }

    /**
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
