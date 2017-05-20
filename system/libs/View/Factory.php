<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Sydes\View;

use Sydes\Event;
use Sydes\View\Engines\EngineInterface;
use Sydes\View\Engines\EngineResolver;

class Factory
{
    public $event;
    protected $engines;
    protected $extensions = [
        'php' => 'php',
        'css' => 'file',
        'js'  => 'file',
    ];

    /**
     * View factory constructor.
     *
     * @param EngineResolver $engines
     * @param Event          $event
     */
    public function __construct(EngineResolver $engines, Event $event)
    {
        $this->engines = $engines;
        $this->event = $event;
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param string $view module-name/view-name
     * @param array  $data
     * @return View
     */
    public function make($view, array $data = [])
    {
        $path = $this->findPath(
            $view = $this->normalizeName($view)
        );

        return new View($this, $this->getEngineFromPath($path), $view, $path, $data);
    }

    protected function normalizeName($name)
    {
        if (strpos($name, '.') === false) {
            $name .= '.php';
        }

        return $name;
    }

    protected function findPath($view)
    {
        list($module, $view) = explode('/', $view, 2);
        $path = moduleDir($module).'/views/'.$view;

        if (!is_file($path)) {
            throw new \RuntimeException(t('error_file_not_found', ['file' => $path]));
        }

        return $path;
    }

    /**
     * Get the appropriate view engine for the given path.
     *
     * @param  string $path
     * @return EngineInterface
     */
    public function getEngineFromPath($path)
    {
        if (!$extension = $this->getExtension($path)) {
            throw new \InvalidArgumentException("Unrecognized extension in file: $path");
        }

        $engine = $this->extensions[$extension];

        return $this->engines->resolve($engine);
    }

    protected function getExtension($path)
    {
        $extensions = array_keys($this->extensions);

        foreach ($extensions as $ext) {
            if (substr($path, -strlen('.'.$ext)) === '.'.$ext) {
                return $ext;
            }
        }

        return false;
    }

    /**
     * Register a valid view extension and its engine.
     *
     * @param  string   $extension
     * @param  string   $engine
     * @param  \Closure $resolver
     */
    public function addExtension($extension, $engine, \Closure $resolver = null)
    {
        if (isset($resolver)) {
            $this->engines->register($engine, $resolver);
        }

        unset($this->extensions[$extension]);

        $this->extensions = array_merge([$extension => $engine], $this->extensions);
    }
}
