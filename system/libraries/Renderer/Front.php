<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace App\Renderer;

use App\Document;
use App\HTML;

class Front extends Renderer
{

    /**
     * @var \App\Config
     */
    private $config;
    private $theme;

    public function render(Document $doc)
    {
        //$this->config = config('front'); TODO uncomment
        $this->prepare($doc);

        $this->theme = $theme = app('config')['site']['theme'];
        $layout = ifsetor($doc->data['layout'], 'page');
        app('translator')->setLocale(app('contentLang'))->loadFrom('theme', $theme);

        $doc->addScript('sydes-front', '/system/assets/js/front.js');
        $doc->addStyle('sydes-front', '/system/assets/css/front.css');

        $template = $this->getTemplate($theme, $layout);
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
            $this->head[] = '<link'.HTML::attr($link).'>';
        }

        $this->fillFooter();

        if (app('user')->isEditor()) {
            $this->footer[] = $this->getToolbar();
        }

        $toReplace = array_merge($doc->data, [
            'language' => app('contentLang'),
            'head'     => implode("\n    ", $this->head),
            'footer'   => implode("\n    ", $this->footer),
            'year'     => date('Y'),
            'theme'    => 'themes/'.$theme,
            'csrf_token' => token(32),
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
        $types = [];
        foreach (app('config')['site']['page_types'] as $type => $data) {
            if (!isset($data['hidden'])) {
                $types[$type] = $data['title'];
            }
        }

        $menu = [];
        foreach ($this->document->context_menu as $key => $data) {
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
            'types'       => $types,
            'theme'       => $this->theme,
            'menu'        => $menu,
            'request_uri' => app('request')->requestUri,
        ]);
    }

    private function getTemplate($theme, $layout)
    {
        $file = DIR_THEME.'/'.$theme.'/layout/'.$layout.'.html';
        if (!file_exists($file)) {
            trigger_error(sprintf(t('error_file_not_found'), $file));
        }
        $layoutContent = file_get_contents($file);

        $firstLine = strtok($layoutContent, "\n");
        if (!$data = parseLayoutData($firstLine)) {
            return $layoutContent;
        }

        $layoutContent = str_replace($firstLine, '', $layoutContent);

        $file = DIR_THEME.'/'.$theme.'/'.$data['extends'].'.html';
        if (!file_exists($file)) {
            trigger_error(sprintf(t('error_file_not_found'), $file));
        }
        $template = file_get_contents($file);

        return str_replace('{layout}', $layoutContent, $template);
    }

    private function compile($html)
    {
        if (!preg_match_all('/{(iblock|t|config):([\w-]+)( .+?)?}/', $html, $matches)) {
            return $html;
        }

        for ($i = 0; $i <= $count = count($matches[2]) - 1; $i++) {
            $method = $matches[1][$i];
            $params = [];
            if ($matches[3][$i]) {
                $matches[3][$i] = str_replace(['&amp;', '&quot;'], ['&', '"'], $matches[3][$i]);
                $params = HTML::parseAttr($matches[3][$i]);
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

    private function iblock($name, $params = false)
    {
        $iblockDir = findExt('iblock', $name);
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
        $out = include $iblockDir.'/index.php';
        if (!is_null($out)) {
            $tplOverride = DIR_THEME.'/'.$this->theme.'/iblock/'.$name.'/'.$args['template'].'.php';
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

    private function t($text)
    {
        return app('translator')->translate($text);
    }

    private function config($key)
    {
        return $this->config->get($key);
    }

}
