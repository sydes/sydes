<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Entity\Plugins\Fields;

use DateTime;
use Sydes\Database\Entity\Event;
use Sydes\Database\Entity\Field;
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

    public function defaultInput()
    {
        return \H::textInput(
            $this->name,
            $this->value->format($this->settings['format']),
            [
                'required' => $this->settings['required'],
                'class'    => ['datetime-picker'],
            ]
        );
    }

    public function defaultOutput()
    {
        return $this->value->format($this->settings['format']);
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
        $this->value = DateTime::createFromFormat($this->settings['format'], $value);
    }

    public function getEventListeners(Event $events)
    {
        $events->on('create', function (Blueprint $t) {
            $t->timestamp($this->name)->nullable();
        });

        if ($this->settings['touch_at'] == 'creating') {
            $events->on('inserting', function () {
                $this->value = new DateTime();
            });
        }

        if ($this->settings['touch_at'] == 'updating') {
            $events->on('updating', function () {
                $this->value = new DateTime();
            });
        }
    }
}
