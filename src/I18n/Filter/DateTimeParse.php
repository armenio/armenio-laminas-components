<?php

/**
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio
 */

namespace Armenio\I18n\Filter;

use Laminas\Filter\AbstractFilter;

/**
 * Class DateTimeParse
 *
 * @package Armenio\I18n\Filter
 */
class DateTimeParse extends AbstractFilter
{
    /**
     * @var string|null
     */
    protected $locale;

    /**
     * @var int
     */
    protected $dateType = \IntlDateFormatter::SHORT;

    /**
     * @var int
     */
    protected $timeType = \IntlDateFormatter::MEDIUM;

    /**
     * @var string|null
     */
    protected $timezone;

    /**
     * @var int
     */
    protected $calendar = \IntlDateFormatter::GREGORIAN;

    /**
     * @var string
     */
    protected $pattern = 'yyyy-MM-dd HH:mm:ss';

    /**
     * @var \IntlDateFormatter|null
     */
    protected $formatter;

    /**
     * @return string|null
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * @param string|null $locale
     *
     * @return $this
     */
    public function setLocale(?string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @return int
     */
    public function getDateType(): int
    {
        return $this->dateType;
    }

    /**
     * @param int $dateType
     *
     * @return $this
     */
    public function setDateType(int $dateType): self
    {
        $this->dateType = $dateType;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimeType(): int
    {
        return $this->timeType;
    }

    /**
     * @param int $timeType
     *
     * @return $this
     */
    public function setTimeType(int $timeType): self
    {
        $this->timeType = $timeType;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    /**
     * @param string|null $timezone
     *
     * @return $this
     */
    public function setTimezone(?string $timezone): self
    {
        $this->timezone = $timezone;
        return $this;
    }

    /**
     * @return int
     */
    public function getCalendar(): int
    {
        return $this->calendar;
    }

    /**
     * @param int $calendar
     *
     * @return $this
     */
    public function setCalendar(int $calendar): self
    {
        $this->calendar = $calendar;
        return $this;
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * @param string $pattern
     *
     * @return $this
     */
    public function setPattern(string $pattern): self
    {
        $this->pattern = $pattern;
        return $this;
    }

    /**
     * @return \IntlDateFormatter|null
     */
    public function getFormatter(): ?\IntlDateFormatter
    {
        if (null !== $this->formatter) {
            return $this->formatter;
        }

        $formatter = new \IntlDateFormatter(
            $this->getLocale(),
            $this->getDateType(),
            $this->getTimeType(),
            $this->getTimezone(),
            $this->getCalendar()
        );

        $formatter->setLenient(false);

        return $formatter;
    }

    /**
     * @param \IntlDateFormatter|null $formatter
     *
     * @return $this
     */
    public function setFormatter(?\IntlDateFormatter $formatter): self
    {
        $this->formatter = $formatter;
        return $this;
    }

    /**
     * @param array|\Traversable $options
     *
     * @return $this|DateTimeParse
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        if (null === $this->locale) {
            $locale = \Locale::getDefault();
            $this->setLocale($locale);
        }

        if (null === $this->timezone) {
            $timezone = date_default_timezone_get();
            $this->setTimezone($timezone);
        }

        return $this;
    }

    /**
     * DateTimeParse constructor.
     *
     * @param $options
     */
    public function __construct($options)
    {
        $this->setOptions($options);
    }

    /**
     * @param mixed $value
     *
     * @return false|mixed|string
     */
    public function filter($value)
    {
        $formatter = $this->getFormatter();

        if (false === $formatter || intl_is_failure($formatter->getErrorCode())) {
            return $value;
        }

        // parse current date
        $timestamp = $formatter->parse($value);

        // new pattern
        $pattern = $this->getPattern();

        $formatter->setPattern($pattern);

        $formatted = $formatter->format($timestamp);

        if (intl_is_failure($formatter->getErrorCode())) {
            return $value;
        }

        return $formatted;
    }
}
