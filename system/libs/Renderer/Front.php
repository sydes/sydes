<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App\Renderer;

use App\Document;
use Module\Themes\Models\Theme;

class Front extends Base
{
    /** @var Theme */
    private $theme;
    private $themePath;

    public function render(Document $doc)
    {
        app('event')->trigger('render.started', [$doc]);

        $this->prepare($doc);

        $this->theme = model('Themes')->getActive();
        $this->themePath = '/themes/'.$this->theme->getId();

        $this->addThemeAssets();

        app('translator')->loadFrom('theme', $this->theme->getId());

        $layout = ifsetor($doc->data['layout'], 'page');
        $template = $this->theme->getLayouts()->getExtended($layout);
        $template = str_replace('{content}', ifsetor($doc->data['content']), $template);
        $template = $this->compile($template);
        unset($doc->data['content']);

        $this->findMetaTags($doc);
        foreach ($doc->meta as $name => $content) {
            $whatName = in_array(substr($name, 0, 3), ['og:', 'fb:', 'al:']) ? 'property' : 'name';
            $this->head[] = '<meta '.$whatName.'="'.$name.'" content="'.$content.'">';
        }

        $this->fillHead();

        foreach ($doc->links as $link) {
            $this->head[] = '<link'.\H::attr($link).'>';
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

        return $template;
    }

    private function compile($html)
    {
        if (!preg_match_all('/{(iblock|t|data):([\w-]+)( .+?)?}/u', $html, $matches)) {
            return $html;
        }

        $count = count($matches[2]) - 1;
        for ($i = 0; $i <= $count; $i++) {
            $method = $matches[1][$i];

            $params = [];
            if (!empty($matches[3][$i])) {
                $params = \H::parseAttr(str_replace(['&amp;', '&quot;'], ['&', '"'], $matches[3][$i]));
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

        app('event')->trigger('iblock.init', [&$name, &$args]);

        $page = $this->document->data;
        ob_start();
        $out = include $iblockDir.'/iblock.php';
        if (!is_null($out)) {
            app('event')->trigger('iblock.render', [&$name, &$args]);

            $template = $iblockDir.'/views/'.$args['template'].'.php';

            if ($tpl = $this->theme->getThemedView('iblock', $name, $args['template'])) {
                include $tpl;
            } elseif (is_file($template)) {
                include $template;
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

    public function settings($key)
    {
        return $this->theme->getSettings($key);
    }

    public function data($key)
    {
        return $this->theme->getData($key);
    }

    private function addThemeAssets()
    {
        $i = 600;
        foreach ($this->theme->getAssets('js') as $key => $source) {
            $this->document->addJs($key, $source, $i++);
        }

        $i = 600;
        foreach ($this->theme->getAssets('css') as $key => $source) {
            $this->document->addCss($key, $source, $i++);
        }
    }

    public function findMetaTags($doc)
    {
        if (isset($doc->data['meta_title'])) {
            $doc->title = $doc->data['meta_title'];
            unset($doc->data['meta_title']);
        } elseif (isset($doc->data['title'])) {
            $doc->title = $doc->data['title'];
        }

        foreach ($doc->data as $key => $value) {
            if (substr($key, 0, 5) == 'meta_') {
                $doc->meta[substr($key, 5)] = $value;
                unset($doc->data[$key]);
            }
        }

        $doc->meta['generator'] = 'SyDES';
    }
}
