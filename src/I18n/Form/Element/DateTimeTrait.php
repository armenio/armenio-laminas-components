<?php

/**
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio
 */

namespace Armenio\I18n\Form\Element;

use Laminas\I18n\Validator\DateTime as DateTimeI18nValidator;
use Laminas\Validator\ValidatorInterface;

trait DateTimeTrait
{
    protected ?string $locale = null;

    protected int $dateType = self::DATETIME_DATE_TYPE;

    protected int $timeType = self::DATETIME_TIME_TYPE;

    protected ?string $timezone = null;

    protected int $calendar = \IntlDateFormatter::GREGORIAN;

    protected string $pattern = self::DATETIME_PATTERN;

    protected ?\IntlDateFormatter $formatter = null;

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    public function getDateType(): int
    {
        return $this->dateType;
    }

    public function setDateType(int $dateType): self
    {
        $this->dateType = $dateType;
        return $this;
    }

    public function getTimeType(): int
    {
        return $this->timeType;
    }

    public function setTimeType(int $timeType): self
    {
        $this->timeType = $timeType;
        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(?string $timezone): self
    {
        $this->timezone = $timezone;
        return $this;
    }

    public function getCalendar(): int
    {
        return $this->calendar;
    }

    public function setCalendar(int $calendar): self
    {
        $this->calendar = $calendar;
        return $this;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function setPattern(string $pattern): self
    {
        $this->pattern = $pattern;
        return $this;
    }

    public function getFormatter(): ?\IntlDateFormatter
    {
        if ($this->formatter !== null) {
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

    public function setFormatter(?\IntlDateFormatter $formatter): self
    {
        $this->formatter = $formatter;
        return $this;
    }

    /**
     * @param array|\Traversable $options
     */
    public function setOptions($options): self
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
     * @return \DateTimeInterface|false|mixed|string
     */
    public function getValue(bool $returnFormattedValue = true)
    {
        $value = parent::getValue(false);
        if (! $value instanceof \DateTimeInterface || ! $returnFormattedValue) {
            return $value;
        }

        $formatter = $this->getFormatter();

        if ($formatter === false || intl_is_failure($formatter->getErrorCode())) {
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

    public function getInputSpecification(): array
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
    protected function getDateValidator(): ValidatorInterface
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

    protected function getDateTimeParseFilter(): array
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
