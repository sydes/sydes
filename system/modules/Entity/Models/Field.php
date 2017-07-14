<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Entity\Models;

use Sydes\Database\Connection;
use Sydes\Database\Schema\Blueprint;

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

    /**
     * {@inheritDoc}
     */
    public function __construct($name, $value, $settings = [])
    {
        $this->name = $name;
        $this->_settings = array_merge([
            'required' => false,
            'helpText' => '',
            'multiple' => false,
            'default' => null,
            'label' => '',
            'formatter' => 'default',
        ], $this->settings, $settings);
        $this->fromString($value);
    }

    /**
     * {@inheritDoc}
     */
    public function fromString($value)
    {
        if (is_null($value)) {
            if (!is_null($this->_settings['default'])) {
                $this->value = $this->_settings['default'];
            }
        } else {
            if ($this->contains == 'array' && is_string($value)) {
                $value = empty($value) ? [] : json_decode($value, true);
            }

            $this->value = $value;
        }

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
    public function value($key = null)
    {
        if (is_null($key)) {
            return $this->value;
        } elseif (is_array($this->value) && isset($this->value[$key])) {
            return $this->value[$key];
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function name()
    {
        return $this->name;
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
    public function setSettings($key, $value = null)
    {
        if (is_array($key)) {
            $this->_settings = $key;
        } else {
            $this->_settings[$key] = $value;
        }

        return $this;
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

    /**
     * {@inheritDoc}
     */
    public function formInput($wrapper = null)
    {
        if (!$wrapper) {
            $wrapper = function (FieldInterface $field) {
                return \H::formGroup(
                    t($field->getSettings('label')),
                    $field->input(),
                    t($field->getSettings('helpText'))
                );
            };
        }

        return $wrapper($this);
    }

    public function formSettings()
    {
        return '';
    }

    public function onCreate(Blueprint $t, Connection $db)
    {
        $t->string($this->name);
    }

    public function onDrop(Connection $db)
    {
    }

    public function saving(Connection $db)
    {
    }

    public function saved(Connection $db)
    {
    }

    public function creating(Connection $db)
    {
    }

    public function created(Connection $db)
    {
    }

    public function updating(Connection $db)
    {
    }

    public function updated(Connection $db)
    {
    }

    public function deleting(Connection $db)
    {
    }

    public function deleted(Connection $db)
    {
    }
}
