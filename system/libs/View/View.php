<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Sydes\View;

use Sydes\View\Engines\EngineInterface;

class View
{
    protected $view;
    protected $path;
    protected $engine;
    protected $factory;
    protected $data;

    /**
     * View constructor.
     *
     * @param Factory         $factory
     * @param EngineInterface $engine
     * @param string          $view
     * @param string          $path
     * @param array           $data
     */
    public function __construct(Factory $factory, EngineInterface $engine, $view, $path, $data = [])
    {
        $this->view = $view;
        $this->path = $path;
        $this->engine = $engine;
        $this->factory = $factory;
        $this->data = $data;
    }

    /**
     * Get the string contents of the view.
     *
     * @return string
     */
    public function render()
    {
        $this->factory->event->trigger('view.render.started', [$this], $this->view);

        $contents = $this->engine->get($this->path, $this->gatherData());

        $this->factory->event->trigger('view.render.ended', [&$contents], $this->view);

        return $contents;
    }

    protected function gatherData()
    {
        foreach ($this->data as $key => $value) {
            if ($value instanceof View) {
                $this->data[$key] = $value->render();
            }
        }

        return $this->data;
    }

    /**
     * Add a view instance to the view data.
     *
     * @param  string $key
     * @param  string $view
     * @param  array  $data
     * @return $this
     */
    public function nest($key, $view, array $data = [])
    {
        return $this->with($key, $this->factory->make($view, $data));
    }

    /**
     * Add a piece of data to the view.
     *
     * @param  string|array $key
     * @param  mixed        $value
     * @return $this
     */
    public function with($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * Get the name of the view.
     *
     * @return string
     */
    public function name()
    {
        return $this->view;
    }

    /**
     * Set the path to the view.
     *
     * @param  string $path
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
    }
}
