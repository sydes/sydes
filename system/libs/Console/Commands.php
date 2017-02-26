<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App\Console;

class Commands
{
    protected $container = [];

    public function get($command)
    {
        return $this->container[$command];
    }

    public function add($expression, $callable, $description = [])
    {
        $tokens = explode(' ', $expression);
        $tokens = array_map('trim', $tokens);
        $tokens = array_values(array_filter($tokens));

        $name = array_shift($tokens);
        $args = [];

        foreach ($tokens as $pos => $token) {
            if ($this->startsWith($token, '[-')) {
                list($token, $data) = $this->parseOption($token);
            } else {
                list($token, $data) = $this->parseArgument($token, $pos);
            }
            $args[$token] = $data;
        }

        $this->container[$name] = [$args, $callable, $description, $expression];
    }

    public function getCommands()
    {
        return array_keys($this->container);
    }

    private function startsWith($haystack, $needle)
    {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }

    private function endsWith($haystack, $needle)
    {
        return substr($haystack, -strlen($needle)) === $needle;
    }

    private function parseArgument($token, $pos)
    {
        if ($this->startsWith($token, '[')) {
            $required = false;
            $name = trim($token, '[]');
        } else {
            $required = true;
            $name = $token;
        }
        return [$name, ['type' => 'argument', 'pos' => $pos, 'required' => $required]];
    }

    private function parseOption($token)
    {
        $token = trim($token, '[]');

        $shortcut = null;
        if (strpos($token, '|') !== false) {
            list($shortcut, $token) = explode('|', $token, 2);
            $shortcut = ltrim($shortcut, '-');
        }

        $name = ltrim($token, '-');

        if ($this->endsWith($token, '=')) {
            $type = 'option_value';
            $name = rtrim($name, '=');
        } else {
            $type = 'option';
        }

        return [$name, ['type' => $type, 'shortcut' => $shortcut]];
    }
}
