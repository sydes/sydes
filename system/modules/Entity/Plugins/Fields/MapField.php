<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Entity\Plugins\Fields;

use Module\Entity\Models\Field;

class MapField extends Field
{
    protected $contains = 'array';

    public function input()
    {
        return \H::textInput($this->name, $this->value, ['placeholder' => 'map coords']);
    }

    public function defaultFormatter()
    {
        return '<div>'.$this->value['map']['lat'].' -'.$this->value['map']['lon'].'</div>';
    }
}
