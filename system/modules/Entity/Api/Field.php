<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Entity\Api;

use Sydes\Database\Connection;
use Sydes\Database\Schema\Blueprint;

abstract class Field implements FieldInterface
{
    protected $name;
    protected $value;
    protected $settings = [];
    protected $contains = 'text';
    protected $formatters = [
        'default' => 'default_formatter',
    ];
    protected $searchable = false;
    protected $filterable = true;
    protected $sortable = true;

    /**
     * {@inheritDoc}
     */
    public function __construct($name, $value, array $settings = [])
    {
        $this->name = $name;
        $this->settings = array_merge([
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
            if (!is_null($this->settings['default'])) {
                $this->value = $this->settings['default'];
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

    public function label()
    {
        return t($this->settings['label']);
    }

    /**
     * {@inheritDoc}
     */
    public function settings($key = null)
    {
        return !is_null($key) ? $this->settings[$key] : $this->settings;
    }

    /**
     * {@inheritDoc}
     */
    public function setSettings($key, $value = null)
    {
        if (is_array($key)) {
            $this->settings = $key;
        } else {
            $this->settings[$key] = $value;
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
    public function output($formatter = null, $value = null)
    {
        if ($formatter instanceof \Closure) {
            return $formatter($this);
        }

        if ($formatter === null) {
            $formatter = $this->settings['formatter'];
        }

        $formatters = $this->formatters + ['table' => 1, 'filter' => 1];
        if (!is_string($formatter) || !isset($formatters[$formatter])) {
            throw new \RuntimeException('Field formatter for "'.$this->name.'" not found');
        }

        return $this->{$formatter.'Output'}($value);
    }

    protected function defaultOutput()
    {
        return $this->value;
    }

    protected function filterOutput($value)
    {
        return \H::formGroup(
            $this->label(),
            \H::textInput('filter['.$this->name.']', $value)
        );
    }

    protected function tableOutput()
    {
        return $this->value;
    }

    /**
     * {@inheritDoc}
     */
    public function input($wrapper = null)
    {
        if (is_null($wrapper)) {
            $wrapper = function (FieldInterface $field) {
                return \H::formGroup(
                    $field->label(),
                    $field->defaultInput(),
                    t($field->settings('helpText'))
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

    /**
     * @return bool
     */
    public function isSearchable()
    {
        return $this->searchable;
    }

    /**
     * @return bool
     */
    public function isFilterable()
    {
        return $this->filterable;
    }

    /**
     * @return bool
     */
    public function isSortable()
    {
        return $this->sortable;
    }
}
