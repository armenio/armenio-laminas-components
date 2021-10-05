<?php

/**
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio
 */

namespace Armenio\I18n\Form\Element;

use Laminas\Form\Element\Date as VendorDate;

/**
 * Class Date
 *
 * @package Armenio\I18n\Form\Element
 */
class Date extends VendorDate
{
    use DateTimeTrait;

    const DATETIME_DATE_TYPE = \IntlDateFormatter::SHORT;
    const DATETIME_TIME_TYPE = \IntlDateFormatter::NONE;
    const DATETIME_PATTERN = 'yyyy-MM-dd';

    /**
     * @var string[]
     */
    protected $attributes = [
        'type' => 'text',
    ];
}
