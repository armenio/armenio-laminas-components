<?php

/**
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio
 */

namespace Armenio\I18n\Form\Element;

use Laminas\Form\Element\DateTime as VendorDateTime;

/**
 * Class DateTime
 *
 * @package Armenio\I18n\Form\Element
 */
class DateTime extends VendorDateTime
{
    use DateTimeTrait;

    const DATETIME_DATE_TYPE = \IntlDateFormatter::SHORT;
    const DATETIME_TIME_TYPE = \IntlDateFormatter::MEDIUM;
    const DATETIME_PATTERN = 'yyyy-MM-dd HH:mm:ss';

    /**
     * @var string[]
     */
    protected $attributes = [
        'type' => 'text',
    ];
}
