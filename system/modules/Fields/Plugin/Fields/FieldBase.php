<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Fields\Plugin\Fields;

abstract class FieldBase implements FieldInterface
{
    protected $name;
    protected $value;
    protected $settings = [];
    protected $_settings = [];
    protected $contains = 'text';
    protected $formatters = [
        'default' => [
            'name' => 'default',
            'method' => 'defaultFormatter',
        ]
    ];
    protected $schema = 'TEXT';

    /**
     * @param string $name
     * @param string $value
     * @param array  $settings
     */
    public function __construct($name, $value, $settings = [])
    {
        $this->name = $name;
        $this->set($value);
        $this->_settings = array_merge([
            'required' => false,
            'helpText' => '',
            'multiple' => false,
            'label' => '',
            'formatter' => 'default',
        ], $this->settings, $settings);
    }

    /**
     * @param mixed $value
     */
    public function set($value)
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
    public function get()
    {
        if ($this->contains == 'array') {
            return json_encode($this->value, JSON_UNESCAPED_UNICODE);
        }

        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setRaw($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getRaw()
    {
        return $this->value;
    }

    /**
     * @param string|null $key
     * @return array|mixed
     */
    public function getSettings($key = null)
    {
        return $key ? $this->_settings[$key] : $this->_settings;
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
            return call_user_func_array($formatter, [$this->name, $this->value, $this->_settings]);
        }

        if (!isset($this->formatters[$this->_settings['formatter']])) {
            throw new \RuntimeException('Field formatter for "'.$this->name.'" not found');
        }

        return call_user_func([$this, $this->formatters[$this->_settings['formatter']]['method']]);
    }

    protected function defaultFormatter()
    {
        return $this->value;
    }

    public function getSettingsForm()
    {
        return '';
    }

    public function getSchema()
    {
        return $this->name.' '.$this->schema;
    }
}
