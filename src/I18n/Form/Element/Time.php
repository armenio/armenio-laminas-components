<?php

/**
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio
 */

namespace Armenio\I18n\Form\Element;

use Laminas\Form\Element\Time as VendorTime;

class Time extends VendorTime
{
    use DateTimeTrait;

    const DATETIME_DATE_TYPE = \IntlDateFormatter::NONE;
    const DATETIME_TIME_TYPE = \IntlDateFormatter::MEDIUM;
    const DATETIME_PATTERN = 'HH:mm:ss';

    /**
     * @var string[]
     */
    protected $attributes = [
        'type' => 'text',
    ];
}
