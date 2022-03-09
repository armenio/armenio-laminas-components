<?php

/**
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio
 */

namespace Armenio\I18n\Form\Element;

use Laminas\I18n\Validator\DateTime as DateTimeI18nValidator;

/**
 * Trait DateTimeTrait
 *
 * @package Armenio\I18n\Form\Element
 */
trait DateTimeTrait
{
    /**
     * @var string|null
     */
    protected $locale;

    /**
     * @var int
     */
    protected $dateType = self::DATETIME_DATE_TYPE;

    /**
     * @var int
     */
    protected $timeType = self::DATETIME_TIME_TYPE;

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
    protected $pattern = self::DATETIME_PATTERN;

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

        $locale = $this->getLocale();
        $dateType = $this->getDateType();
        $timeType = $this->getTimeType();
        $pattern = null;

        // pt_BR ICU workaround
        if ($locale === 'pt_BR') {
            $pattern = '';
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
     * @return $this
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        if (isset($this->options['locale'])) {
            $this->setLocale($this->options['locale']);
        }

        if (isset($this->options['date_type'])) {
            $this->setDateType($this->options['date_type']);
        }

        if (isset($this->options['time_type'])) {
            $this->setTimeType($this->options['time_type']);
        }

        if (isset($this->options['timezone'])) {
            $this->setTimezone($this->options['timezone']);
        }

        if (isset($this->options['pattern'])) {
            $this->setPattern($this->options['pattern']);
        }

        if (isset($this->options['calendar'])) {
            $this->setCalendar($this->options['calendar']);
        }

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
     * @param bool $returnFormattedValue
     *
     * @return \DateTimeInterface|false|mixed|string
     */
    public function getValue($returnFormattedValue = true)
    {
        $value = parent::getValue(false);
        if (! $value instanceof \DateTimeInterface || ! $returnFormattedValue) {
            return $value;
        }

        $formatter = $this->getFormatter();

        if (false === $formatter || intl_is_failure($formatter->getErrorCode())) {
            return $value;
        }

        // current timestamp
        $timestamp = $value->getTimestamp();

        $formatted = $formatter->format($timestamp);

        if (intl_is_failure($formatter->getErrorCode())) {
            return $value;
        }

        return $formatted;
    }

    /**
     * @return array
     */
    public function getInputSpecification()
    {
        $spec = parent::getInputSpecification();

        if (! isset($spec['filters'])) {
            $spec['filters'] = [];
        }

        $spec['filters'][] = $this->getDateTimeParseFilter();

        return $spec;
    }

    /**
     * @return DateTimeI18nValidator
     */
    protected function getDateValidator()
    {
        return new DateTimeI18nValidator([
            'locale' => $this->getLocale(),
            'dateType' => $this->getDateType(),
            'timeType' => $this->getTimeType(),
            'timezone' => $this->getTimezone(),
            'calendar' => $this->getCalendar(),
            'pattern' => $this->getPattern(),
        ]);
    }

    /**
     * @return array
     */
    protected function getDateTimeParseFilter()
    {
        return [
            'name' => 'Armenio\I18n\Filter\DateTimeParse',
            'options' => [
                'locale' => $this->getLocale(),
                'dateType' => $this->getDateType(),
                'timeType' => $this->getTimeType(),
                'timezone' => $this->getTimezone(),
                'calendar' => $this->getCalendar(),
                'pattern' => $this->getPattern(),
            ],
        ];
    }
}
