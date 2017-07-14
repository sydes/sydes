<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Entity\Plugins\Fields;

use DateTime;
use Module\Entity\Models\Field;
use Sydes\Database\Connection;
use Sydes\Database\Schema\Blueprint;

class DateTimeField extends Field
{
    /** @var DateTime */
    protected $value;
    protected $settings = [
        'touch_at' => 'none',
        'can_touch_at' => ['none', 'creating', 'updating'],
        'format' => 'Y-m-d H:i:s',
    ];

    public function input()
    {
        return \H::textInput(
            $this->name,
            $this->value->format($this->getSettings('format')),
            [
                'required' => $this->getSettings('required'),
                'class'    => ['datetime-picker'],
            ]
        );
    }

    public function defaultFormatter()
    {
        return $this->value->format($this->getSettings('format'));
    }

    public function fromString($value)
    {
        if (empty($value)) {
            return;
        }

        if ($this->value instanceof DateTime) {
            $this->value->setTimestamp($value);
        } else {
            $this->value = (new DateTime())->setTimestamp($value);
        }
    }

    public function toString()
    {
        if ($this->value instanceof DateTime) {
            return $this->value->getTimestamp();
        }

        return '';
    }

    public function set($value)
    {
        $this->value = DateTime::createFromFormat($this->getSettings('format'), $value);
    }

    public function onCreate(Blueprint $t, Connection $db)
    {
        $t->timestamp($this->name)->nullable();
    }

    public function creating(Connection $db)
    {
        if ($this->getSettings('touch_at') == 'creating') {
            $this->value = new DateTime();
        }
    }

    public function updating(Connection $db)
    {
        if ($this->getSettings('touch_at') == 'updating') {
            $this->value = new DateTime();
        }
    }
}
