<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Fields\Field;

class MapField extends BaseField
{
    protected $contains = 'array';

    public function getField()
    {
        // TODO: Implement getField() method.
    }

    public function getConfigurator()
    {
        // TODO: Implement getConfigurator() method.
    }

    public function defaultFormatter()
    {
        return '<div>'.$this->value['map']['lat'].' -'.$this->value['map']['lon'].'</div>';
    }
}
