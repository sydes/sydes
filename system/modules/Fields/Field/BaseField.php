<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Fields\Field;

abstract class BaseField implements FieldInterface
{
    protected $name;
    protected $value;
    protected $settings;
    protected $defaultSettings;
    protected $contains = 'plain';
    protected $formatters = [
        'default' => [
            'name' => 'default',
            'method' => 'defaultFormatter',
        ]
    ];

    /**
     * @param string $name
     * @param string $value
     * @param array  $settings
     */
    public function __construct($name, $value, $settings = [])
    {
        $this->name = $name;
        $this->fromString($value);
        $this->settings = $settings;
    }

    /**
     * @param string $value
     */
    public function fromString($value)
    {
        if ($this->contains == 'array') {
            if (is_string($value)) {
                $value = json_decode($value, true);
            } elseif (empty($value)) {
                $value = [];
            }
        }

        $this->value = $value;
    }

    /**
     * @return string
     */
    public function toString()
    {
        if ($this->contains == 'array') {
            return json_encode($this->value, JSON_UNESCAPED_UNICODE);
        }

        return $this->value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @return array
     */
    public function getFormatters()
    {
        return $this->formatters;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        return true;
    }

    /**
     * @param callable $formatter
     * @return string
     */
    public function render($formatter = null)
    {
        if ($formatter) {
            return call_user_func_array($formatter, [$this->name, $this->value, $this->settings]);
        }

        if (!isset($this->formatters[$this->settings['formatter']])) {
            throw new \RuntimeException('Field formatter for "'.$this->name.'" not found');
        }

        return call_user_func([$this, $this->formatters[$this->settings['formatter']]['method']]);
    }

    protected function defaultFormatter()
    {
        return $this->value;
    }
}
