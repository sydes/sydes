<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App;

class Event
{
    protected $events = [];

    /**
     * @param string   $event
     * @param string   $context
     * @param callable $callback
     * @param int      $priority
     */
    public function on($event, $context, $callback, $priority = 0)
    {
        if (!isset($this->events[$event])) {
            $this->events[$event] = [];
        }
        $this->events[$event][] = ['contexts' => $context, 'fn' => $callback, 'prio' => $priority];
    }

    /**
     * @param string $event
     */
    public function off($event)
    {
        if (!isset($this->events[$event])) {
            $this->events[$event] = [];
        }
    }

    /**
     * @param string $event
     * @param array  $params
     * @param string $context
     */
    public function trigger($event, $params = [], $context = '')
    {
        if (empty($this->events[$event])) {
            return;
        }

        $queue = new \SplPriorityQueue();
        foreach ($this->events[$event] as $index => $action) {
            $queue->insert($index, $action['prio']);
        }

        $queue->top();
        while ($queue->valid()) {
            $index = $queue->current();
            if (!empty($context)) {
                $contexts = explode(',', $this->events[$event][$index]['contexts']);
                $current_context = false;
                foreach ($contexts as $route) {
                    if (fnmatch(trim($route), $context)) {
                        $current_context = true;
                        break;
                    }
                }
            } else {
                $current_context = true;
            }
            if ($current_context && is_callable($this->events[$event][$index]['fn'])) {
                if (call_user_func_array($this->events[$event][$index]['fn'], $params) === false) {
                    break;
                }
            }
            $queue->next();
        }
    }
}
