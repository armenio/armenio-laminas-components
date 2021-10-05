<?php

/**
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio
 */

namespace Armenio\I18n\Form\Element;

use Laminas\Form\Element\Number as VendorNumber;

/**
 * Class Number
 *
 * @package Armenio\I18n\Form\Element
 */
class Number extends VendorNumber
{
    /**
     * @var string[]
     */
    protected $attributes = [
        'type' => 'text',
    ];

    /**
     * @var string|null
     */
    protected $locale;

    /**
     * @var int
     */
    protected $style = \NumberFormatter::DEFAULT_STYLE;

    /**
     * @var int
     */
    protected $type = \NumberFormatter::TYPE_DOUBLE;

    /**
     * @var int|null
     */
    protected $decimals;

    /**
     * @var array
     */
    protected $textAttributes = [];

    /**
     * @var \NumberFormatter|null
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
    public function getStyle(): int
    {
        return $this->style;
    }

    /**
     * @param int $style
     *
     * @return $this
     */
    public function setStyle(int $style): self
    {
        $this->style = $style;
        return $this;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     *
     * @return $this
     */
    public function setType(int $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getDecimals(): ?int
    {
        return $this->decimals;
    }

    /**
     * @param int|null $decimals
     *
     * @return $this
     */
    public function setDecimals(?int $decimals): self
    {
        $this->decimals = $decimals;
        return $this;
    }

    /**
     * @return array
     */
    public function getTextAttributes(): array
    {
        return $this->textAttributes;
    }

    /**
     * @param array $textAttributes
     *
     * @return $this
     */
    public function setTextAttributes(array $textAttributes): self
    {
        $this->textAttributes = $textAttributes;
        return $this;
    }

    /**
     * @return \NumberFormatter|null
     */
    public function getFormatter(): ?\NumberFormatter
    {
        if (null !== $this->formatter) {
            return $this->formatter;
        }

        $formatter = new \NumberFormatter($this->getLocale(), $this->getStyle());

        $decimals = $this->getDecimals();

        if ($decimals !== null) {
            $formatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $decimals);
            $formatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $decimals);
        }

        $textAttributes = $this->getTextAttributes();
        foreach ($textAttributes as $textAttribute => $value) {
            $formatter->setTextAttribute($textAttribute, $value);
        }

        return $formatter;
    }

    /**
     * @param \NumberFormatter|null $formatter
     *
     * @return $this
     */
    public function setFormatter(?\NumberFormatter $formatter): self
    {
        $this->formatter = $formatter;
        return $this;
    }

    /**
     * @param array|\Traversable $options
     *
     * @return $this|Number
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        if (isset($this->options['locale'])) {
            $this->setLocale($this->options['locale']);
        }

        if (isset($this->options['format_style'])) {
            $this->setStyle($this->options['format_style']);
        }

        if (isset($this->options['format_type'])) {
            $this->setType($this->options['format_type']);
        }

        if (isset($this->options['decimals'])) {
            $this->setDecimals($this->options['decimals']);
        }

        if (isset($this->options['text_attributes'])) {
            $this->setTextAttributes($this->options['text_attributes']);
        }

        if (null === $this->locale) {
            $locale = \Locale::getDefault();
            $this->setLocale($locale);
        }

        return $this;
    }

    /**
     * @param bool $returnFormattedValue
     *
     * @return false|mixed|string
     */
    public function getValue($returnFormattedValue = true)
    {
        $value = parent::getValue();
        if (! $returnFormattedValue) {
            return $value;
        }

        $formatter = $this->getFormatter();

        if (false === $formatter || intl_is_failure($formatter->getErrorCode())) {
            return $value;
        }

        $formatted = $formatter->format($value, $this->getType());

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

        $spec['filters'][] = $this->getNumberParseFilter();

        return $spec;
    }

    /**
     * @return array
     */
    protected function getNumberParseFilter()
    {
        return [
            'name' => 'numberParse',
            'options' => [
                'locale' => $this->getLocale(),
                'style' => $this->getStyle(),
                'type' => $this->getType(),
            ],
        ];
    }
}
