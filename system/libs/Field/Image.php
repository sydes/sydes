<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App\Field;

class Image extends Field
{
	public function __construct($value)
	{
		// добавить один раз в документ скрипт и стили специфичные для поля
		parent::__construct($value);
	}
	
	public function getView($params)
	{
        $html = '';
		
		foreach ($this->value as $item) {
			$html .= '<img src="'.$item.'">';
		}
		
		return $html;
	}

	public function getInput($params)
	{
		// создать див со мини картинками и дроп2аплодом
		return \H::hidden('name', 'value', ['class' => 'field-image']);
	}
}
