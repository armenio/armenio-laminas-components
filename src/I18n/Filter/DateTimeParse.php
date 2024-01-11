<?php

/**
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio
 */

namespace Armenio\I18n\Filter;

use Laminas\Filter\AbstractFilter;

class DateTimeParse extends AbstractFilter
{
    protected ?string $locale = null;

    protected int $dateType = \IntlDateFormatter::SHORT;

    protected int $timeType = \IntlDateFormatter::MEDIUM;

    protected ?string $timezone = null;

    protected int $calendar = \IntlDateFormatter::GREGORIAN;

    protected string $pattern = 'yyyy-MM-dd HH:mm:ss';

    protected ?\IntlDateFormatter $formatter = null;

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): DateTimeParse
    {
        $this->locale = $locale;
        return $this;
    }

    public function getDateType(): int
    {
        return $this->dateType;
    }

    public function setDateType(int $dateType): DateTimeParse
    {
        $this->dateType = $dateType;
        return $this;
    }

    public function getTimeType(): int
    {
        return $this->timeType;
    }

    public function setTimeType(int $timeType): DateTimeParse
    {
        $this->timeType = $timeType;
        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(?string $timezone): DateTimeParse
    {
        $this->timezone = $timezone;
        return $this;
    }

    public function getCalendar(): int
    {
        return $this->calendar;
    }

    public function setCalendar(int $calendar): DateTimeParse
    {
        $this->calendar = $calendar;
        return $this;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function setPattern(string $pattern): DateTimeParse
    {
        $this->pattern = $pattern;
        return $this;
    }

    public function getFormatter(): ?\IntlDateFormatter
    {
        if (null !== $this->formatter) {
            return $this->formatter;
        }

        $locale = $this->getLocale();
        $dateType = $this->getDateType();
        $timeType = $this->getTimeType();
        $pattern = null;

        // pt_BR ICU workaround
        if ($locale === 'pt_BR') {
            $pattern = '';

            if ($timeType !== \IntlDateFormatter::NONE) {
                switch ($dateType) {
                    case \IntlDateFormatter::SHORT:
                        $pattern .= "dd/MM/y";
                        break;
                    case \IntlDateFormatter::MEDIUM:
                        $pattern .= "d 'de' MMM 'de' y";
                        break;
                    case \IntlDateFormatter::LONG:
                        $pattern .= "d 'de' MMMM 'de' y";
                        break;
                    case \IntlDateFormatter::FULL:
                        $pattern .= "EEEE, d 'de' MMMM 'de' y";
                        break;
                }
            }

            if ($timeType !== \IntlDateFormatter::NONE) {
                if ($pattern !== '') {
                    $pattern .= " ";
                }

                switch ($timeType) {
                    case \IntlDateFormatter::SHORT:
                        $pattern .= "HH:mm";
                        break;
                    case \IntlDateFormatter::MEDIUM:
                        $pattern .= "HH:mm:ss";
                        break;
                    case \IntlDateFormatter::LONG:
                        $pattern .= "HH:mm:ss z";
                        break;
                    case \IntlDateFormatter::FULL:
                        $pattern .= "HH:mm:ss zzzz";
                        break;
                }
            }
        }

        $formatter = new \IntlDateFormatter(
            $locale,
            $dateType,
            $timeType,
            $this->getTimezone(),
            $this->getCalendar(),
            $pattern
        );

        $formatter->setLenient(false);

        return $formatter;
    }

    public function setFormatter(?\IntlDateFormatter $formatter): DateTimeParse
    {
        $this->formatter = $formatter;
        return $this;
    }

    /**
     * @param array|iterable $options
     */
    public function setOptions($options): DateTimeParse
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

    public function __construct($options)
    {
        $this->setOptions($options);
    }

    /**
     * @param mixed $value
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
