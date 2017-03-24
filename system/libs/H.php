<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

use Psr\Http\Message\RequestInterface;

class H
{
    private static $extenders = [];

    /**
     * Adds callable to methods
     *
     * @param string   $name
     * @param callable $closure
     */
    public static function extend($name, callable $closure)
    {
        self::$extenders[$name] = $closure;
    }

    /**
     * Gets the extender by name
     *
     * @param string $name
     * @param array  $arguments
     * @return string
     * @throws RuntimeException
     */
    public static function __callStatic($name, array $arguments)
    {
        if (!array_key_exists($name, self::$extenders)) {
            throw new InvalidArgumentException('Undefined HTML extender "'.$name.'"');
        }
        return call_user_func_array(self::$extenders[$name], $arguments);
    }

    /**
     * @param string $label
     * @param string $url
     * @param array  $attr
     * @return string
     */
    public static function a($label, $url = '#', array $attr = [])
    {
        $attr['href'] = $url;

        return '<a'.self::attr($attr).'>'.$label.'</a>';
    }

    /**
     * @param string $label
     * @param string $type
     * @param array  $attr
     * @return string
     */
    public static function button($label = 'Submit', $type = 'submit', array $attr = [])
    {
        $attr['type'] = $type;

        return '<button'.self::attr($attr).'>'.$label.'</button>';
    }

    /**
     * @param string $label
     * @param string $input
     * @param string $help
     * @return string
     */
    public static function formGroup($label, $input, $help = '')
    {
        $help = $help ? '<small class="form-text text-muted">'.$help.'</small>' : '';

        return '<div class="form-group"><label>'.$label.'</label>'.$input.$help.'</div>';
    }

    /**
     * @param string $name  
     * @param string $value
     * @param array  $source List of items 'value' => 'title'
     * @param array  $attr  
     * @return string
     */
    public static function select($name, $value, array $source, array $attr = [])
    {
        if (empty($source)) {
            $source[''] = ' - ';
        }

        $attr['name'] = $name.(isset($attr['multiple']) ? '[]' : '');

        $html = '<select'.self::attr($attr).'>';
        foreach ($source as $val => $title) {
            $selected = $val == $value ? 'selected' : '';
            $html .= '<option value="'.$val.'" '.$selected.'>'.$title.'</option>';
        }

        return $html.'</select>';
    }

    private static function optionElement($type, $name, $data, $selected, array $attr = [])
    {
        if (!$data) {
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

        $html = '<div'.self::attr($attr).'>';
        foreach ($data as $value => $title) {
            $checked = in_array($value, (array)$selected, true) ? ' checked' : '';
            $html .= $pre.'<input type="'.$type.'" name="'.$name.'" value="'.$value.'"'.$checked.'> '.$title.$post;
        }

        return $html.'</div>';
    }

    /**
     * @param string $name  
     * @param string $value
     * @param array  $source List of items 'value' => 'title'
     * @param array  $attr  
     * @return string
     */
    public static function checkbox($name, $value, array $source, array $attr = [])
    {
        $name .= count($source) > 1 ? '[]' : '';

        return self::optionElement('checkbox', $name, $source, $value, $attr);
    }

    /**
     * @param string $name  
     * @param string $value
     * @param array  $source List of items 'value' => 'title'
     * @param array  $attr  
     * @return string
     */
    public static function radio($name, $value, array $source, array $attr = [])
    {
        return self::optionElement('radio', $name, $source, $value, $attr);
    }

    /**
     * @param string   $name
     * @param int|bool $status
     * @return string
     */
    public static function yesNo($name, $status)
    {
        return self::optionElement('radio', $name, ['1' => t('yes'), '0' => t('no')], (int)$status, ['inline' => true]);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $attr
     * @return string
     */
    public static function textarea($name, $value, array $attr = [])
    {
        $attr['name'] = $name;

        return '<textarea'.self::attr($attr).'>'.$value.'</textarea>';
    }

    /**
     * @param string $type
     * @param string $name
     * @param string $value
     * @param array  $attr
     * @return string
     */
    public static function input($type, $name, $value = '', array $attr = [])
    {
        $attr['type'] = $type;
        $attr['name'] = $name;
        if ($value) {
            $attr['value'] = $value;
        }

        return '<input'.self::attr($attr).'>';
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $attr
     * @return string
     */
    public static function text($name, $value, array $attr = [])
    {
        return self::input('text', $name, $value, $attr);
    }

    /**
     * @param string $name
     * @param array  $attr
     * @return string
     */
    public static function password($name, array $attr = [])
    {
        return self::input('password', $name, '', $attr);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $attr
     * @return string
     */
    public static function hidden($name, $value, array $attr = [])
    {
        return self::input('hidden', $name, $value, $attr);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $attr
     * @return string
     */
    public static function color($name, $value, array $attr = [])
    {
        return self::input('color', $name, $value, $attr);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $attr
     * @return string
     */
    public static function date($name, $value, array $attr = [])
    {
        return self::input('date', $name, $value, $attr);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $attr
     * @return string
     */
    public static function email($name, $value, array $attr = [])
    {
        return self::input('email', $name, $value, $attr);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $attr
     * @return string
     */
    public static function number($name, $value, array $attr = [])
    {
        return self::input('number', $name, $value, $attr);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $attr
     * @return string
     */
    public static function range($name, $value, array $attr = [])
    {
        return self::input('range', $name, $value, $attr);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $attr
     * @return string
     */
    public static function search($name, $value, array $attr = [])
    {
        return self::input('search', $name, $value, $attr);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $attr
     * @return string
     */
    public static function tel($name, $value, array $attr = [])
    {
        return self::input('tel', $name, $value, $attr);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $attr
     * @return string
     */
    public static function time($name, $value, array $attr = [])
    {
        return self::input('time', $name, $value, $attr);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $attr
     * @return string
     */
    public static function url($name, $value, array $attr = [])
    {
        return self::input('url', $name, $value, $attr);
    }

    /**
     * @param array $crumbs with ['title' => '', 'url' => '']
     * @return string
     */
    public static function breadcrumb($crumbs)
    {
        $html = '<ol class="breadcrumb">';
        foreach ($crumbs as $crumb) {
            if (isset($crumb['url'])) {
                $html .= '<li class="breadcrumb-item"><a href="'.$crumb['url'].'">'.$crumb['title'].'</a></li>';
            } else {
                $html .= '<li class="breadcrumb-item active">'.$crumb['title'].'</li>';
            }
        }

        return $html.'</ol>';
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
                $all .= self::paginationLink($i, $path, $newQuery);
            } else {
                $all .= self::paginationLink($i);
            }
        }

        if ($total > $maxLinks) {
            if ($page > 1) {
                $first = self::paginationLink($text['first'], $path, $query);
            }
            if ($page < $total) {
                $last = self::paginationLink($text['last'], $path, $query + ['page' => $total]);
            }
        }

        if ($prevNext && $page > 1) {
            $prev = self::paginationLink($text['prev'], $path, $query + ['page' => $page - 1]);
        }
        if ($prevNext && $page < $total) {
            $next = self::paginationLink($text['next'], $path, $query + ['page' => $page + 1]);
        }

        return '<ul class="'.$class.'">'.$first.$prev.$all.$next.$last.'</ul>';
    }

    private static function paginationLink($title, $path = false, $query = [])
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
     * @param callable $callback  Function name, that can return a string with html
     * @return string
     */
    public static function treeList(array $items, callable $callback, array $attr = [], $max_level = 20)
    {
        reset($items);
        $cur = current($items);
        $prev_level = $cur['level'];
        $html = '<ul'.self::attr($attr).'>';

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

            $attr = isset($item['attr']) ? ' '.self::attr($item['attr']) : '';
            $html .= '<li'.$attr.'>'.$callback($item).'</li>';
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
    public static function flatList(array $items, $current = '', array $attr = [])
    {
        $html = '<ul'.self::attr($attr).'>';
        foreach ($items as $link => $title) {
            $active = $current == $link ? ' class="active"' : '';
            $html .= '<li'.$active.'><a href="'.$link.'">'.$title.'</a></li>';
        }

        return $html.'</ul>';
    }

    /**
     * @param array $rows
     * @param array $header
     * @param array $attr
     * @return string
     */
    public static function table(array $rows, array $header = [], array $attr = [])
    {
        $html = '<table'.self::attr($attr).'>';
        if (!empty($header)) {
            $html .= '<thead><tr>';
            foreach ($header as $col) {
                $html .= '<th>'.$col.'</th>';
            }
            $html .= '</tr></thead>';
        }

        $html .= '<tbody>';
        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($row as $col) {
                $html .= '<td>'.$col.'</td>';
            }
            $html .= '</tr>';
        }

        return $html.'</tbody></table>';
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

        return '<div '.self::attr($attr).'>'.self::nav($titles, 'nav-tabs').
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

            $html .= '<li class="nav-item">'.self::a($item['title'], $item['url'], $item['attr']).'</li>';
        }

        return '<ul class="nav '.$style.'">'.$html.'</ul>';
    }

    public static function accordion($data, $current = '')
    {
        $id = rand(0, 10000);
        $html = '<div class="panel-group" id="accordion'.$id.'">';
        foreach ($data as $key => $d) {
            $active = $current == $key ? ' in' : '';
            $html .= '
    <div class="panel panel-default">
        <div class="panel-heading" data-toggle="collapse" data-parent="#accordion'.$id.'" data-target="#'.$key.'">
            <span class="panel-title">'.$d['title'].'</span>
        </div>
        <div id="'.$key.'" class="panel-collapse collapse'.$active.'">
            <div class="panel-body">'.$d['content'].'</div>
        </div>
    </div>';
        }
        return $html.'</div>';
    }

    public static function modal($title, $body = '', $footer = '', $form_url = '')
    {
        $html = '
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">'.$title.'</h4>
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
     * @param array $data
     * @return string
     */
    public static function form($data)
    {
        $form = '';
        foreach ($data as $name => $input) {
            $piece = (isset($input['label']) && $input['type'] != 'hidden') ? '<label>'.$input['label'].'</label>' : '';

            $type = $input['type'];
            $attr = ifsetor($input['attr'], '');
            $list = ifsetor($input['list'], []);

            switch ($input['type']) {
                case 'select':
                case 'checkbox':
                case 'radio':
                    $piece .= self::$type($name, $input['value'], $list, $attr);
                    break;
                case 'password':
                    $piece .= self::$type($name, $attr);
                    break;
                default:
                    $piece .= self::$type($name, $input['value'], $attr);
            }

            $form .= $input['type'] != 'hidden' ? '<div class="form-group">'.$piece.'</div>' : $piece;
        }
        return $form;
    }

    public static function dropdown($title, array $items, $pos = 'left')
    {
        $pos = $pos == 'right' ? 'dropdown-menu-right' : '';
        $html = '<button type="button" class="dropdown-toggle" data-toggle="dropdown">'.
            $title.'<span class="caret"></span></button><ul class="dropdown-menu '.$pos.'">';

        foreach ($items as $item) {
            $html .= isset($item['link']) ?
                '<li><a href="'.$item['link'].'">'.$item['title'].'</a></li>' :
                '<li>'.$item['html'].'</li>';
        }

        return '<div class="dropdown">'.$html.'</ul></div>';
    }

    /**
     * Parse string with attributes to array
     * @param string $string
     * @return array
     */
    public static function parseAttr($string)
    {
        $attr = [];

        $pattern = '/([\w-]+)\s*(=\s*"([^"]*)")?/';
        preg_match_all($pattern, $string, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $name = strtolower($match[1]);

            if (isset($match[3])) {
                $value = trim($match[3]);
                if (strpos($value, ' ')) {
                    $value = explode(' ', $value);
                }
            } else {
                $value = true;
            }

            $attr[$name] = $value;
        }

        return $attr;
    }

    /**
     * Generates string with attributes from array
     *
     * @param string|array $attr
     * @return string
     */
    public static function attr($attr)
    {
        if (is_string($attr)) {
            return ' '.$attr;
        }

        $str = '';
        foreach ($attr as $key => $value) {
            $str .= ' '.$key;
            if ($value === true) {
            } elseif (is_array($value)) {
                $str .= '="'.implode(' ', $value).'"';
            } else {
                $str .= '="'.$value.'"';
            }
        }

        return $str;
    }
}
