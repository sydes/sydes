<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Entity\Plugins\Fields;

use Sydes\Database\Entity\Event;
use Sydes\Database\Entity\Field;
use Sydes\Database\Schema\Blueprint;

class PrimaryField extends Field
{
    protected $settings = [
        'type' => 'string',
    ];

    public function defaultInput()
    {
        $tag = $this->settings['type'] == 'integer' ? 'numberInput' : 'textInput';
        $props = ['required' => true,];

        if ($this->value) {
            $props['disabled'] = true;
        }

        return \H::$tag($this->name, $this->value, $props);
    }

    public function getEventListeners(Event $events)
    {
        $events->on('create', function (Blueprint $t) {
            if ($this->settings['type'] == 'integer') {
                $t->integer($this->name);
            } else {
                $t->string($this->name);
            }
        });
    }
}
