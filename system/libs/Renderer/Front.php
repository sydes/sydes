<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App\Renderer;

use App\Document;
use H;

class Front extends Renderer
{
    private $config;
    private $theme;

    public function render(Document $doc)
    {
        app('event')->trigger('render.before', [&$doc]);

        $this->prepare($doc);

        $this->theme = app('site')['theme'];
        $this->config = app('theme')->getConfig();
        $themePath = '/themes/'.$this->theme;

        if (isset($this->config['js'])) {
            foreach ($this->config['js'] as $key => $source) {
                $source = $this->addThemePath($source, $themePath);
                $this->document->addJs($key, $source);
            }
        }
        if (isset($this->config['css'])) {
            foreach ($this->config['css'] as $key => $source) {
                $source = $this->addThemePath($source, $themePath);
                $this->document->addCss($key, $source);
            }
        }

        app('translator')->setLocale(app('locale'))->loadFrom('theme', $this->theme);

        $doc->addPackage('sydes-front', '/system/assets/js/front.js', '/system/assets/css/front.css');
        // TODO разделить на администраторские и обычные стили и скрипты


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

        if (app('user')->isEditor()) {
            $this->footer[] = $this->getToolbar();
        }

        $toReplace = array_merge($doc->data, [
            'language' => app('locale'),
            'head'     => implode("\n    ", $this->head),
            'footer'   => implode("\n    ", $this->footer),
            'year'     => date('Y'),
            'theme_path' => $themePath,
        ]);

        $find = $replace = [];
        foreach ($toReplace as $key => $val) {
            $find[] = '{'.$key.'}';
            $replace[] = $val;
        }

        $template = str_replace($find, $replace, $template);

        return preg_replace('!{\w+}!', '', $template);
    }

    private function getToolbar()
    {

        $menu = [];
        foreach ($this->document->sydes['context_menu'] as $key => $data) {
            $menu[$key]['title'] = $data['title'];
            $menu[$key]['link'] = $data['link'];
            foreach ($data['children'] as $child) {
                $modal = '';
                if ($child['modal']) {
                    $size = '';
                    if ($child['modal'] === 'small') {
                        $size = 'data-size="sm"';
                    } elseif ($child['modal'] === 'large') {
                        $size = 'data-size="lg"';
                    }
                    $modal = 'data-toggle="modal" data-target="#modal" '.$size.' ';
                }
                $menu[$key]['children'][] = '<a '.$modal.'href="'.$child['link'].'">'.$child['title'].'</a>';
            }
        }

        return render(DIR_SYSTEM.'/views/toolbar.php', [
            'page'        => $this->document->data,
            'theme'       => $this->theme,
            'menu'        => $menu,
            'request_uri' => app('request')->getUri()->getPath(),
        ]);
    }

    private function getTemplate($layout)
    {
        $theme = app('theme');
        $data = $theme->getLayout($layout);

        while (isset($data['extends'])) {
            $data = $theme->extendLayout($data);
        }

        return $data['content'];
    }

    private function compile($html)
    {
        if (!preg_match_all('/{(iblock|t|config):([\w-]+)( .+?)?}/', $html, $matches)) {
            return $html;
        }

        //TODO как то шорткоды добавить
        for ($i = 0; $i <= $count = count($matches[2]) - 1; $i++) {
            $method = $matches[1][$i];
            $params = [];
            if ($matches[3][$i]) {
                $matches[3][$i] = str_replace(['&amp;', '&quot;'], ['&', '"'], $matches[3][$i]);
                $params = H::parseAttr($matches[3][$i]);
            }
            $content = $this->$method($matches[2][$i], $params);

            /*if (Auth::admin() && in_array($method, ['iblock', 'config'])) {
                if (!$content) {
                    $content = '&nbsp;';
                }
                $tools = '<span data-module="'.$method.'" data-item="'.$matches[2][$i].'" class="block-edit"></span>';
                if (isset($arParams['template']) && file_exists(DIR_THEME.'/'.$this->theme.'/iblock/'.$matches[2][$i].'/'.$arParams['template'].'.php')) {
                    $tools .= '<span data-item="'.$matches[2][$i].'" data-template="'.$arParams['template'].'" class="block-template"></span>';
                }
                $content = '<div class="block-wrapper"><div class="tools">'.$tools.'</div>'.$content.'</div>';
            }*/

            $html = str_replace($matches[0][$i], $content, $html);
        }

        return $html;
    }

    public function iblock($name, $params = false)
    {
        $iblockDir = iblockDir($name);
        if (!$iblockDir) {
            return sprintf(t('error_iblock_not_found'), $name);
        }

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
                return sprintf(t('error_iblock_template_not_found'), $args['template'], $name);
            }
        }

        return ob_get_clean();
    }

    public function t($text)
    {
        return app('translator')->translate($text);
    }

    public function config($key)
    {
        return ifsetor($this->config['data'][$key], false);
    }

    private function addThemePath($source, $themePath)
    {
        $arr = [];
        foreach ((array)$source as $path) {
            $arr[] = ($path[0] != '/' && substr($path, 0, 4) != 'http') ? $themePath.'/'.$path : $path;
        }
        return $arr;
    }
}
