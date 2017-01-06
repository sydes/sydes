<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App\Field;

abstract class Field implements FieldInterface
{
    protected $name;
    protected $value;
    protected $type = 'plain';

    public function __construct($value)
    {
        switch ($this->type) {
            case 'array':
                $this->value = json_decode($value, true);
                break;
            case 'plain':
            default:
                $this->value = $value;
        }
    }

    public function getValue()
    {
        return $this->value;
    }

    public function serialize()
    {
        switch ($this->type) {
            case 'array':
                $value = json_encode($this->value, JSON_UNESCAPED_UNICODE);
                break;
            case 'plain':
            default:
                $value = $this->value;
        }
        return $value;
    }

    public function validate($params)
    {
        return true;
    }
}
