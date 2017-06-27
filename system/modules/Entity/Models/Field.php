<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Entity\Models;

abstract class Field implements FieldInterface
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
     * {@inheritDoc}
     */
    public function __construct($name, $value, $settings = [])
    {
        $this->name = $name;
        $this->fromString($value);
        $this->_settings = array_merge([
            'required' => false,
            'helpText' => '',
            'multiple' => false,
            'label' => '',
            'formatter' => 'default',
        ], $this->settings, $settings);
    }

    /**
     * {@inheritDoc}
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

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function toString()
    {
        if ($this->contains == 'array') {
            return json_encode($this->value, JSON_UNESCAPED_UNICODE);
        }

        return $this->value;
    }

    /**
     * {@inheritDoc}
     */
    public function set($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * {@inheritDoc}
     */
    public function getSettings($key = null)
    {
        return $key ? $this->_settings[$key] : $this->_settings;
    }

    /**
     * {@inheritDoc}
     */
    public function getFormatters()
    {
        return $this->formatters;
    }

    /**
     * {@inheritDoc}
     */
    public function validate()
    {
        return true;
    }

    /**
     * {@inheritDoc}
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

    public function formSettings()
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function onCreate(array $cols)
    {
        $cols[] = $this->name.' '.$this->schema;

        return $cols;
    }

    /**
     * {@inheritDoc}
     */
    public function onDrop()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function beforeSave()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function afterSave()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function beforeDelete()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function afterDelete()
    {
    }
}
