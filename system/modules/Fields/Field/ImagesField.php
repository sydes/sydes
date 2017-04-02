<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Fields\Field;

class ImagesField extends BaseField
{
    protected $contains = 'array';

    public function getField()
    {
        // создать див с мини картинками и дроп2аплодом
        return \H::fileInput($this->name.'_new').\H::hiddenInput($this->name, $this->value);
    }

    public function getConfigurator()
    {
        // TODO: Implement getConfigurator() method.
    }

    public function defaultFormatter()
    {
        $html = '';

        foreach ($this->value as $item) {
            $html .= '<img src="'.$item.'">';
        }

        return $html;
    }
}
