<?php

/**
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio
 */

namespace Armenio\I18n\Form\Element;

use Laminas\Form\Element\Number as VendorNumber;

class Number extends VendorNumber
{
    /**
     * @var string[]
     */
    protected $attributes = [
        'type' => 'text',
    ];

    protected ?string $locale = null;

    protected int $style = \NumberFormatter::DEFAULT_STYLE;

    protected int $type = \NumberFormatter::TYPE_DOUBLE;

    protected ?int $decimals = null;

    protected array $textAttributes = [];

    protected ?\NumberFormatter $formatter = null;

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): Number
    {
        $this->locale = $locale;
        return $this;
    }

    public function getStyle(): int
    {
        return $this->style;
    }

    public function setStyle(int $style): Number
    {
        $this->style = $style;
        return $this;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): Number
    {
        $this->type = $type;
        return $this;
    }

    public function getDecimals(): ?int
    {
        return $this->decimals;
    }

    public function setDecimals(?int $decimals): Number
    {
        $this->decimals = $decimals;
        return $this;
    }

    public function getTextAttributes(): array
    {
        return $this->textAttributes;
    }

    public function setTextAttributes(array $textAttributes): Number
    {
        $this->textAttributes = $textAttributes;
        return $this;
    }

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

    public function setFormatter(?\NumberFormatter $formatter): Number
    {
        $this->formatter = $formatter;
        return $this;
    }

    /**
     * @param array|\Traversable $options
     */
    public function setOptions($options): Number
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
     * @return false|float|int|mixed|string
     */
    public function getValue(bool $returnFormattedValue = true)
    {
        $value = parent::getValue();
        if (! is_numeric($value) || ! $returnFormattedValue) {
            return $value;
        }

        $formatter = $this->getFormatter();

        if ($formatter === false || intl_is_failure($formatter->getErrorCode())) {
            return $value;
        }

        $formatted = $formatter->format($value, $this->getType());

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

        $spec['filters'][] = $this->getNumberParseFilter();

        return $spec;
    }

    protected function getNumberParseFilter(): array
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
