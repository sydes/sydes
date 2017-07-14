<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Entity\Plugins\Fields;

use Module\Entity\Models\Field;
use Sydes\Database\Connection;

class FileField extends Field
{
    public function input()
    {
        return \H::fileInput($this->name.'_new').\H::hiddenInput($this->name, $this->value);
    }

    public function saving(Connection $db)
    {
        // upload file and set path to value
    }

    public function defaultFormatter()
    {
        $html = '';

        foreach ($this->value as $item) {
            $html .= '<a href="'.$item.'">'.$item.'</a>';
        }

        return $html;
    }
}
