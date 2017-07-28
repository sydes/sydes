<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Entity\Plugins\Fields;

use Module\Entity\Api\Field;
use Sydes\Database\Connection;
use Sydes\Database\Schema\Blueprint;

class YesNoField extends Field
{
    public function defaultInput()
    {
        return \H::yesNo($this->name, $this->value);
    }

    public function onCreate(Blueprint $t, Connection $db)
    {
        $t->integer($this->name)->default($this->settings('default'));
    }

    public function defaultOutput()
    {
        $choice = [t('no'), t('yes')];

        return $choice[$this->value];
    }

    public function tableOutput()
    {
        $choice = [t('no'), t('yes')];

        return $choice[$this->value];
    }

    public function filterOutput($value)
    {
        $choice = ['' => t('any'), 1 => t('yes'), 0 => t('no')];

        return \H::formGroup(
            $this->label(),
            \H::select('filter['.$this->name.']', $value, $choice)
        );
    }
}
