<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App\Renderer;

use App\Document;
use H;

class Front extends Base
{
    private $config;
    private $theme;
    private $themePath;

    public function render(Document $doc)
    {
        app('event')->trigger('render.started', [$doc]);

        $this->prepare($doc);

        $this->theme = app('site')->get('theme');
        $this->config = app('theme')->getConfig();
        $this->themePath = '/themes/'.$this->theme;

        if (isset($this->config['js'])) {
            $i = 600;
            foreach ($this->config['js'] as $key => $source) {
                $source = $this->prependPath($source);
                $this->document->addJs($key, $source, $i++);
            }
        }

        if (isset($this->config['css'])) {
            $i = 600;
            foreach ($this->config['css'] as $key => $source) {
                $source = $this->prependPath($source);
                $this->document->addCss($key, $source, $i++);
            }
        }

        app('translator')->loadFrom('theme', $this->theme);

        $layout = ifsetor($doc->data['layout'], 'page');
        $template = $this->getTemplate($layout);
        $template = str_replace('{content}', ifsetor($doc->data['content']), $template);
        $template = $this->compile($template);
        unset($doc->data['content']);

        $doc->findMetaTags();

        $doc->meta['generator'] = 'SyDES';
        foreach ($doc->meta as $name => $content) {
            $whatName = in_array(substr($name, 0, 3), ['og:', 'fb:', 'al:']) ? 'property' : 'name';
            $this->head[] = '<meta '.$whatName.'="'.$name.'" content="'.$content.'">';
        }

        $this->fillHead();

        foreach ($doc->links as $link) {
            $this->head[] = '<link'.H::attr($link).'>';
        }

        $this->fillFooter();

        $toReplace = array_merge($doc->data, [
            'language' => app('locale'),
            'head'     => implode("\n    ", $this->head),
            'footer'   => implode("\n    ", $this->footer),
            'year'     => date('Y'),
            'theme_path' => $this->themePath,
        ]);

        $find = $replace = [];
        foreach ($toReplace as $key => $val) {
            $find[] = '{'.$key.'}';
            $replace[] = $val;
        }

        $template = str_replace($find, $replace, $template);

        app('event')->trigger('render.ended', [&$template]);
        return preg_replace('!{\w+}!', '', $template);
    }

    private function getTemplate($layout)
    {
        $theme = app('theme');
        $data = $theme->getLayout($layout);

        $i = 0;
        while (isset($data['extends']) && $i++ != 10) {
            $data = $theme->extendLayout($data);
        }

        return $data['content'];
    }

    private function compile($html)
    {
        if (!preg_match_all('/{(iblock|t|data):([\w-]+)( .+?)?}/', $html, $matches)) {
            return $html;
        }

        $count = count($matches[2]) - 1;
        for ($i = 0; $i <= $count; $i++) {
            $method = $matches[1][$i];

            $params = [];
            if (!empty($matches[3][$i])) {
                $params = H::parseAttr(str_replace(['&amp;', '&quot;'], ['&', '"'], $matches[3][$i]));
            }

            $content = $this->$method($matches[2][$i], $params);

            $html = str_replace($matches[0][$i], $content, $html);
        }

        return $html;
    }

    public function iblock($name, $params = false)
    {
        if (!$iblockDir = iblockDir($name)) {
            return t('error_iblock_not_found', ['name' => $name]);
        }

        app('translator')->loadFrom('iblock', $name);

        $args = ['template' => 'default'];
        if ($params) {
            $args = array_merge($args, $params);
        }

        if (strpos($args['template'], '.') !== false) {
            return '';
        }

        $page = $this->document->data;
        ob_start();
        $out = include $iblockDir.'/iblock.php';
        if (!is_null($out)) {
            $tplOverride = DIR_THEME.'/'.$this->theme.'/iblock/'.$name.'/views/'.$args['template'].'.php';
            $tplOriginal = $iblockDir.'/views/'.$args['template'].'.php';

            if (is_file($tplOverride)) {
                include $tplOverride;
            } elseif (is_file($tplOriginal)) {
                include $tplOriginal;
            } elseif ($args['template'] != 'default') {
                ob_end_clean();
                return t('error_iblock_template_not_found', ['template' => $args['template'], 'name' => $name]);
            }
        }

        $result = ob_get_clean();

        if (!isset($args['nowrap'])) {
            $result = '<div class="iblock-'.$name.'">'.$result.'</div>';
        }

        return $result;
    }

    public function t($text)
    {
        return app('translator')->translate($text);
    }

    public function data($key)
    {
        return ifsetor($this->config['data'][$key], false);
    }

    private function prependPath($source)
    {
        $arr = [];
        foreach ((array)$source as $path) {
            $arr[] = ($path[0] != '/' && substr($path, 0, 4) != 'http') ? $this->themePath.'/'.$path : $path;
        }
        return $arr;
    }
}
