<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Theme\Models;

class Layouts
{
    private $dir;
    private $list = [];
    private $data = [];
    private $ext = 'html';

    public function __construct($theme)
    {
        $this->dir = DIR_THEME.'/'.$theme.'/layouts/';
        $this->list = str_replace([$this->dir, '.'.$this->ext], '', glob($this->dir.'*.'.$this->ext));

        foreach ($this->list as $layout) {
            $this->data[$layout] = $this->parse($layout);
        }
    }

    /**
     * @return array
     */
    public function getNames()
    {
        return $this->list;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->data;
    }

    /**
     * @param string $name
     * @return array
     */
    public function get($name)
    {
        if (!$this->exists($name)) {
            throw new \RuntimeException(t('error_layout_not_found', ['id' => $name]));
        }

        return $this->data[$name];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function exists($name)
    {
        return in_array($name, $this->list);
    }

    /**
     * @param string $name
     * @return array
     */
    public function parse($name)
    {
        $content = file_get_contents($this->dir.$name.'.'.$this->ext);
        $firstLine = strtok($content, "\n");
        $data = [];

        if (preg_match_all('!@([a-z]+)\(([\w -/]+)\)!u', $firstLine, $matches)) {
            foreach ($matches[1] as $i => $key) {
                $data[$key] = $matches[2][$i];
            }
        }

        $data['content'] = str_replace($firstLine, '', $content);

        return $data;
    }

    /**
     * @param string $name alphanumeric name of layout
     * @param array  $data array with keys content, extends, as
     * @return bool|int
     */
    public function store($name, array $data)
    {
        $content = arrayRemove($data, 'content', '');
        $formatted = '';
        foreach ($data as $key => $value) {
            $formatted .= "@{$key}({$value})";
        }
        $formatted .= "\n".$content;

        return file_put_contents($this->dir.$name.'.'.$this->ext, $formatted);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function delete($name)
    {
        return unlink($this->dir.$name.'.'.$this->ext);
    }

    /**
     * @param string $name
     * @return array
     */
    public function getExtended($name)
    {
        $data = $this->get($name);

        $i = 0;
        while (isset($data['extends']) && $i++ != 10) {
            $data = $this->doExtend($data);
        }
        return $data['content'];
    }

    private function doExtend($data)
    {
        if (strpos($data['extends'], '/') !== false) {
            $part = explode('/', $data['extends']);
            $parent = $this->get($part[1]);
        } else {
            $parent = ['content' => file_get_contents($this->dir.'../'.$data['extends'].'.'.$this->ext)];
        }

        $parent['content'] = str_replace('{layout}', $data['content'], $parent['content']);

        return $parent;
    }
}
