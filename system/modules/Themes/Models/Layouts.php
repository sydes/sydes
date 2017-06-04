<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Themes\Models;

class Layouts
{
    /** @var self */
    private $parentLayouts;
    private $dir;
    private $list = [];
    private $data = [];
    private $ext = '.html';

    public function __construct($theme, $parent = null)
    {
        $this->dir = app('dir.theme').'/'.$theme.'/layouts/';
        $this->parentLayouts = $parent;
        $this->list = str_replace([$this->dir, $this->ext], '', glob($this->dir.'*'.$this->ext));

        foreach ($this->list as $layout) {
            $this->data[$layout] = $this->parse($layout);
        }
    }

    /**
     * @return array
     */
    public function getList()
    {
        return array_unique(array_merge($this->parentLayouts->getList(), $this->list));
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return array_merge($this->parentLayouts->getAll(), $this->data);
    }

    /**
     * @param string $id
     * @return array
     */
    public function get($id)
    {
        if (isset($this->data[$id])) {
            $layout = $this->data[$id];
        } elseif ($this->parentLayouts) {
            $layout = $this->parentLayouts->get($id);
        } else {
            throw new \RuntimeException(t('error_layout_not_found', ['id' => $id]));
        }

        return $layout;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function exists($id)
    {
        $parent = $this->parentLayouts ? $this->parentLayouts->exists($id) : false;

        return in_array($id, $this->list) || $parent;
    }

    /**
     * @param string $id
     * @return array
     */
    public function parse($id)
    {
        $content = file_get_contents($this->dir.$id.$this->ext);
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
     * @param string $id   alphanumeric name of layout
     * @param array  $data array with keys 'extends', 'name', 'content'
     * @return bool|int
     */
    public function store($id, array $data)
    {
        $content = arrayRemove($data, 'content', '');
        $formatted = '';
        foreach ($data as $key => $value) {
            $formatted .= "@{$key}({$value})";
        }
        $formatted .= "\n".$content;

        return file_put_contents($this->dir.$id.$this->ext, $formatted);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function delete($id)
    {
        return unlink($this->dir.$id.$this->ext);
    }

    /**
     * @param string $id
     * @return array
     */
    public function getExtended($id)
    {
        $data = $this->get($id);

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
            $parent = ['content' => $this->getWrapper($data['extends'])];
        }

        $parent['content'] = str_replace('{layout}', $data['content'], $parent['content']);

        return $parent;
    }

    public function getWrapper($id)
    {
        if (file_exists($this->dir.'../'.$id.$this->ext)) {
            return file_get_contents($this->dir.'../'.$id.$this->ext);
        } elseif ($this->parentLayouts) {
            return $this->parentLayouts->getWrapper($id);
        }

        throw new \RuntimeException(t('error_layout_wrapper_not_found', ['id' => $id]));

    }
}
