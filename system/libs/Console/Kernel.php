<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Sydes\Console;

use Sydes\Container;

class Kernel
{
    /** @var Container */
    private $container;
    private $output;

    public function __construct(array $values = [])
    {
        session_id('cli');
        session_start();
        mb_internal_encoding('UTF-8');

        error_reporting(-1);
        set_error_handler('sydesErrorHandler');

        if (!isset($_SESSION['site'])) {
            $_SESSION['site'] = 1;
        }

        $_SERVER['HTTP_HOST'] = 'from.cli';

        $config = include DIR_CONFIG.'/app.php';
        $this->container = new Container($values, $config);
        Container::setContainer($this->container);

        $this->output = new Output();
        $this->container['siteId'] = $_SESSION['site'];
    }

    public function run($argv, $argc)
    {
        if ($argc < 2) {
            $argv[1] = 'help';
        }

        unset($argv[0]);

        $this->container['translator']->init('en');

        try {
            $has = $this->parse($argv);
            $commands = $this->getCommands();

            list($expects, $callable, $description, $expression) = $commands->get($has['name']);

            if (isset($has['options']['h']) || isset($has['options']['help'])) {
                $this->showHelp($description, $expression);
            } else {
                $this->exec($has, $expects, $callable);
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    protected function getCommands()
    {
        $commands = new Commands();
        if (is_dir(DIR_SITE.'/'.$_SESSION['site'])) {
            $siteConf = include DIR_SITE.'/'.$_SESSION['site'].'/config.php';
        } else {
            $siteConf = ['modules' => ['main' => []]];
        }

        foreach ($siteConf['modules'] as $name => $module) {
            $dir = moduleDir($name);
            if (file_exists($dir.'/Cli.php')) {
                $class = 'Module\\'.$name.'\\Cli';
                new $class($commands, $this->output);
            }
        }

        return $commands;
    }

    protected function parse($argv)
    {
        $name = array_shift($argv);
        $arguments = [];
        $options = [];

        foreach ($argv as $arg) {
            if (substr($arg, 0, 2) == '--') {
                $option = substr($arg, 2);

                if (strpos($option, '=') !== false) {
                    $parts = explode('=', $option, 2);
                    $options[$parts[0]] = $parts[1];
                } else {
                    $options[$option] = true;
                }

                continue;
            }

            if (substr($arg, 0, 1) == '-') {
                $option = substr($arg, 1);
                if (strlen($option) > 1) {
                    $options[$option[0]] = substr($option, 1);
                } else {
                    $options[$option] = true;
                }

                continue;
            }

            $arguments[] = $arg;
        }

        return [
            'name' => $name,
            'arguments' => $arguments,
            'options' => $options,
        ];
    }

    protected function exec($has, $expects, $callable)
    {
        $reflection = is_array($callable) ?
            new \ReflectionMethod($callable[0], $callable[1]) :
            new \ReflectionFunction($callable);
        $params = $reflection->getParameters();

        $args = [];
        foreach ($params as $param) {

            $token = $param->name;

            if (!isset($expects[$token])) {
                $token = snake_case($token, '-');
                if (!isset($expects[$token])) {
                    $this->output->say('The expression does not contain param '.$param->name);
                    return;
                }
            }

            $arg = $expects[$token];

            if ($arg['type'] == 'argument') {
                if (isset($has['arguments'][$arg['pos']])) {
                    $value = $has['arguments'][$arg['pos']];
                } else {
                    if (!$arg['required']) {
                        if ($param->isOptional()) {
                            $value = $param->getDefaultValue();
                        } else {
                            $value = false;
                        }
                    } else {
                        $this->output->say('Argument '.$token.' is required');
                        return;
                    }
                }
            } elseif ($arg['type'] == 'option') {
                $value = isset($has['options'][$token]) || isset($has['options'][$arg['shortcut']]);
            } elseif ($arg['type'] == 'option_value') {
                if (isset($has['options'][$token])) {
                    $value = $has['options'][$token];
                    if ($value === true) {
                        $this->output->say('Option --'.$token.' must contain value');
                        return;
                    }
                } else {
                    $value = $param->getDefaultValue();
                }
            }

            $args[] = $value;
        }

        call_user_func_array($callable, $args);
    }

    protected function showHelp($description, $expression)
    {
        $this->output->say('Usage:')
            ->say('  '.$expression);

        if (empty($description)) {
            return;
        }

        $max = 0;
        $args = $opts = [];
        foreach ($description[1] as $token => $text) {
            $max = max($max, strlen($token));
            if (substr($token, 0, 1) == '-') {
                $opts[$token] = $text;
            } else {
                $args[$token] = $text;
            }
        }

        if (!empty($args)) {
            $this->output->say('')
                ->say('Arguments:');
            foreach ($args as $token => $text) {
                $this->output->say("  \033[36m".str_pad($token, $max+2)."\033[0m".$text);
            }
        }

        if (!empty($opts)) {
            $this->output->say('')
                ->say('Options:');
            foreach ($opts as $token => $text) {
                $this->output->say("  \033[36m".str_pad($token, $max+2)."\033[0m".$text);
            }
        }

        $this->output->say('')
            ->say('Help:')
            ->say($description[0]);
    }
}
