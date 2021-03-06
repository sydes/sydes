<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Entity\Plugins\Fields;

use Sydes\Database\Entity\Field;

class HiddenField extends Field
{
    public function defaultInput()
    {
        return \H::hiddenInput($this->name, $this->value);
    }
}
