<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace App\Html;

use Psr\Http\Message\RequestInterface;

class BS4 extends Base
{
    /**
     * @param string $type
     * @param string $name
     * @param string $value
     * @param array  $attr
     * @return string
     */
    public static function input($type, $name, $value = '', array $attr = [])
    {
        $attr['class'][] = 'form-control';

        if (isset($attr['size'])) {
            $attr['class'][] = 'form-control-'.$attr['size'];
            unset($attr['size']);
        }

        return parent::input($type, $name, $value, $attr);
    }

    /**
     * @param string $label
     * @param string $input
     * @param string $help
     * @return string
     */
    public static function formGroup($label, $input, $help = '')
    {
        $help = $help ? static::tag('small', $help, ['class'=>'form-text text-muted']) : '';

        return '<div class="form-group"><label>'.$label.'</label>'.$input.$help.'</div>';
    }

    /**
     * @param array $crumbs with ['title' => '', 'url' => '']
     * @return string
     */
    public static function breadcrumb($crumbs)
    {
        $html = '';
        foreach ($crumbs as $crumb) {
            if (isset($crumb['url'])) {
                $html .= '<li class="breadcrumb-item"><a href="'.$crumb['url'].'">'.$crumb['title'].'</a></li>';
            } else {
                $html .= '<li class="breadcrumb-item active">'.$crumb['title'].'</li>';
            }
        }

        return '<ol class="breadcrumb">'.$html.'</ol>';
    }

    /**
     * @param RequestInterface $request
     * @param int              $total    Total pages
     * @param int              $maxLinks Max width of pagination, odd number is better
     * @param bool             $prevNext To show links for next page?
     * @param array            $text     Texts for additional links
     * @param string           $class    Html class for container
     * @return string
     */
    public static function pagination(RequestInterface $request, $total, $maxLinks = 7,
        $prevNext = false, array $text = [], $class = 'pagination')
    {
        if ($total < 2) {
            return '';
        }

        $text = array_merge([
            'first' => '&laquo;',
            'prev' => '&lsaquo;',
            'next' => '&rsaquo;',
            'last' => '&raquo;',
        ], $text);

        $page = 1;
        $uri = $request->getUri();
        $path = $uri->getPath();
        parse_str($uri->getQuery(), $query);

        if (isset($query['page']) && (int)$query['page'] > 0) {
            $page = (int)$query['page'];
        }
        unset($query['page']);

        if ($total <= $maxLinks) {
            $from = 1;
            $to = $total;
        } else {
            if ($page < floor($maxLinks / 2) + 1) {
                $from = 1;
                $to = $maxLinks;
            } elseif ($page <= $total - floor($maxLinks / 2)) {
                $from = $page - floor($maxLinks / 2);
                $to = $page + floor($maxLinks / 2);
            } else {
                $from = $total - $maxLinks + 1;
                $to = $total;
            }
        }

        $prev = $next = $first = $last = $all = '';

        for ($i = $from; $i <= $to; $i++) {
            if ($i != $page) {
                $newQuery = $query + ['page' => $i];
                $all .= static::paginationLink($i, $path, $newQuery);
            } else {
                $all .= static::paginationLink($i);
            }
        }

        if ($total > $maxLinks) {
            if ($page > 1) {
                $first = static::paginationLink($text['first'], $path, $query);
            }
            if ($page < $total) {
                $last = static::paginationLink($text['last'], $path, $query + ['page' => $total]);
            }
        }

        if ($prevNext && $page > 1) {
            $prev = static::paginationLink($text['prev'], $path, $query + ['page' => $page - 1]);
        }
        if ($prevNext && $page < $total) {
            $next = static::paginationLink($text['next'], $path, $query + ['page' => $page + 1]);
        }

        return '<ul class="'.$class.'">'.$first.$prev.$all.$next.$last.'</ul>';
    }

    protected static function paginationLink($title, $path = false, $query = [])
    {
        if ($path) {
            if (isset($query['page']) && $query['page'] == 1) {
                unset($query['page']);
            }

            $query = empty($query) ? '' : '?'.http_build_query($query);

            $act = '';
            $link = '<a class="page-link" href="'.$path.$query.'">'.$title.'</a>';
        } else {
            $act =  ' active';
            $link = '<span class="page-link">'.$title.'</span>';
        }

        return '<li class="page-item'.$act.'">'.$link.'</li>';
    }

    /**
     * Gets tree from flat array
     *
     * @param array    $items     Flat array with elements.
     *                            Each element must have at least 'level'.
     *                            Each element can have 'attr' for LI and other data
     * @param int      $max_level Maximum tree depth
     * @param array    $attr      Attributes for UL
     * @param callable $formatter A callback that can return a string with html
     * @return string
     */
    public static function treeNav(array $items, callable $formatter, array $attr = [], $max_level = 20)
    {
        reset($items);
        $cur = current($items);
        $prev_level = $cur['level'];
        $html = '<ul'.static::attr($attr).'>';

        foreach ($items as $item) {
            if (isset($item['skip'])) {
                continue;
            }

            if ($prev_level != $item['level']) {
                if ($max_level < $item['level']) {
                    continue;
                }
                if ($prev_level < $item['level']) {
                    $html = substr($html, 0, -5);
                    $html .= '<ul>';
                } else {
                    $delta = ($prev_level - $item['level']) * 10;
                    $html .= str_pad('', $delta, '</ul></li>');
                }
            }

            $attr = isset($item['attr']) ? ' '.static::attr($item['attr']) : '';
            $html .= '<li'.$attr.'>'.$formatter($item).'</li>';
            $prev_level = $item['level'];
        }

        $delta = ($prev_level - 1) * 10;

        return $html.str_pad('', $delta, '</ul></li>').'</ul>';
    }

    /**
     * @param array  $items
     * @param string $current
     * @param array  $attr
     * @return string
     */
    public static function flatNav(array $items, $current = '', array $attr = [])
    {
        $html = '';
        foreach ($items as $link => $title) {
            $active = $current == $link ? ' class="active"' : '';
            $html .= '<li'.$active.'><a href="'.$link.'">'.$title.'</a></li>';
        }

        return '<ul'.static::attr($attr).'>'.$html.'</ul>';
    }

    /**
     * @param array  $items
     * @param string $current
     * @param array  $attr
     * @return string
     */
    public static function tabs(array $items, $current = '', array $attr = [])
    {
        $titles = [];
        $contents = '';

        foreach ($items as $key => $d) {
            $active = $current == $key ? ' active' : '';

            $titles[] = [
                'active' => $active,
                'url' => '#'.$key,
                'title' => $d['title'],
                'attr' => [
                    'data-toggle' => 'tab',
                ]
            ];

            $contents .= '<div class="tab-pane'.$active.'" id="'.$key.'">'.$d['content'].'</div>';
        }

        return '<div '.static::attr($attr).'>'.static::nav($titles, 'nav-tabs').
            '<div class="tab-content">'.$contents.'</div></div>';
    }

    /**
     * @param array  $items
     * @param string $style
     * @return string
     */
    public static function nav(array $items, $style = '')
    {
        $html = '';
        foreach ($items as $item) {
            $item['attr']['class'][] = 'nav-link';
            if (!empty($item['active'])) {
                $item['attr']['class'][] = 'active';
            }

            $html .= '<li class="nav-item">'.static::a($item['title'], $item['url'], $item['attr']).'</li>';
        }

        return '<ul class="nav '.$style.'">'.$html.'</ul>';
    }

    /**
     * @param array  $items
     * @param string $current
     * @param bool   $independent Close all other cards on open?
     * @return string
     */
    public static function accordion(array $items, $current = '', $independent = false)
    {
        $html = '';
        $parent = $independent ? '' : 'data-parent=".accordion"';
        foreach ($items as $key => $d) {
            $active = $current == $key ? ' show' : '';
            $html .= '
    <div class="card">
        <div class="card-header" data-toggle="collapse" '.$parent.' data-target="#acc-'.$key.'">
            '.$d['title'].'
        </div>
        <div id="acc-'.$key.'" class="collapse'.$active.'">
            <div class="card-block">'.$d['content'].'</div>
        </div>
    </div>';
        }

        return '<div class="accordion">'.$html.'</div>';
    }

    /**
     * @param string $title
     * @param string $body
     * @param string $footer
     * @param string $form_url
     * @return string
     */
    public static function modal($title, $body = '', $footer = '', $form_url = '')
    {
        $html = '
        <div class="modal-header">
            <h5 class="modal-title">'.$title.'</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">'.$body.'</div>
        <div class="modal-footer">'.$footer.'</div>
        ';

        if ($form_url) {
            $html = '<form name="modal-form" method="post" enctype="multipart/form-data" action="'.$form_url.'">'.$html.'</form>';
        }

        return $html;
    }

    /**
     * @param array       $items
     * @param array|bool $label
     * @param array       $align
     * @return string
     */
    public static function dropdown(array $items, $label = false, $align = ['right', 'down'])
    {
        if (!$label) {
            $first = array_shift($items);

            $first = static::dropdownPrepareButton($first);

            if (isset($first['url'])) {
                $action = static::a($first['label'], $first['url'], $first['attr']);
            } else {
                $first['attr']['type'] = ifsetor($first['type'], 'button');
                $action = static::button($first['label'], $first['attr']);
            }

            $drop = static::button('', [
                'class' => ['btn', 'btn-'.$first['style'], $first['size'], 'dropdown-toggle', 'dropdown-toggle-split'],
                'data-toggle' => 'dropdown',
            ]);

            $toggle = $action.$drop;
        } else {
            $label = static::dropdownPrepareButton($label);
            $label['attr']['class'][] = 'dropdown-toggle';
            $label['attr']['data-toggle'] = 'dropdown';

            $toggle = static::button($label['label'], $label['attr']);
        }

        $menu = '';
        foreach ($items as $item) {
            $item['attr']['class'][] = 'dropdown-item';

            if (isset($item['url'])) {
                $menu .= static::a($item['label'], $item['url'], $item['attr']);
            } elseif (isset($item['type'])) {
                $item['attr']['type'] = $item['type'];
                $menu .= static::button($item['label'], $item['attr']);
            } elseif (isset($item['divider'])) {
                $menu .= '<div class="dropdown-divider"></div>';
            } elseif (isset($item['header'])) {
                $menu .= '<h6 class="dropdown-header">'.$item['label'].'</h6>';
            } else {
                $menu .= $item['html'];
            }
        }

        $up = in_array('up', $align) ? 'dropup' : '';
        $right = in_array('right', $align) ? 'dropdown-menu-right' : '';

        return '<div class="btn-group '.$up.'">'.$toggle.'<div class="dropdown-menu '.$right.'">'.$menu.'</div></div>';
    }

    protected static function dropdownPrepareButton($button)
    {
        if (!isset($button['style'])) {
            $button['style'] = 'secondary';
        }

        if (isset($button['size'])) {
            $button['size'] = 'btn-'.$button['size'];
        } else {
            $button['size'] = '';
        }

        if (!isset($button['attr'])) {
            $button['attr'] = [
                'class' => ['btn', 'btn-'.$button['style'], $button['size']],
            ];
        }

        return $button;
    }

    /**
     * @param string $type
     * @param string $name
     * @param array  $items
     * @param mixed  $selected
     * @param array  $attr
     * @return string
     */
    protected static function optionElement($type, $name, $items, $selected, array $attr = [])
    {
        if (!$items) {
            return '<div>'.t('empty').'</div>';
        }

        $inline = false;
        if (isset($attr['inline'])) {
            $inline = $attr['inline'];
            unset($attr['inline']);
        }

        if ($inline) {
            $attr['class'][] = $type;
            $pre = '<label class="'.$type.'-inline">';
            $post = '</label>';
        } else {
            $pre = '<div class="'.$type.'"><label>';
            $post = '</label></div>';
        }

        $html = '';
        foreach ($items as $value => $title) {
            $html .= $pre.static::input($type, $name, $value, [
                    'checked' => in_array($value, (array)$selected, true)
                ]).' '.$title.$post;
        }

        return '<div'.static::attr($attr).'>'.$html.'</div>';
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $items List of items 'value' => 'title'
     * @param array  $attr
     * @return string
     */
    public static function checkboxList($name, $value, array $items, array $attr = [])
    {
        $name .= count($items) > 1 ? '[]' : '';

        return static::optionElement('checkbox', $name, $items, $value, $attr);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $items List of items 'value' => 'title'
     * @param array  $attr
     * @return string
     */
    public static function radioList($name, $value, array $items, array $attr = [])
    {
        return static::optionElement('radio', $name, $items, $value, $attr);
    }

    /**
     * @param string   $name
     * @param int|bool $status
     * @return string
     */
    public static function yesNo($name, $status)
    {
        return static::optionElement('radio', $name, ['1' => t('yes'), '0' => t('no')], (int)$status,
            ['inline' => true]);
    }

}
