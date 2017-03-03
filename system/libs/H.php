<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

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
     * Get the extender by its name
     *
     * @param string $name
     * @param array  $arguments
     * @return mixed
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
     * @param string $name   Input name
     * @param string $value
     * @param array  $source List of items 'value' => 'title' or 0 => 'value'
     * @param array  $attr   Input attributes, like class
     * @return string
     */
    public static function select($name, $value, array $source, array $attr = [])
    {
        if (empty($source)) {
            $source[] = ' - ';
        }
        if (array_values($source) === $source) {
            $source = array_combine($source, $source);
        }
		$name .= (isset($attr['multiple'])) ? '[]' : '';

        $html = '<select name="'.$name.'"'.self::attr($attr).'>'.PHP_EOL;
        foreach ($source as $val => $title) {
            $selected = $val == $value ? ' selected' : '';
            $html .= '<option value="'.$val.'"'.$selected.'>'.$title.'</option>'.PHP_EOL;
        }
        return $html.'</select>';
    }

    private static function optionElement($type, $name, $data, $selected, array $attr = [])
    {
        if (!$data) {
            return '<div>'.t('empty').'</div>';
        }
        if (array_values($data) === $data) {
            $data = array_combine($data, $data);
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
        $name .= ($type == 'checkbox' && count($data) > 1) ? '[]' : '';
        $html = '<div'.self::attr($attr).'>';
        foreach ($data as $value => $title) {
            $checked = in_array($value, (array)$selected, true) ? ' checked' : '';
            $html .= $pre.'<input type="'.$type.'" name="'.$name.'" value="'.$value.'"'.$checked.'> '.$title.$post.PHP_EOL;
        }
        return $html.'</div>';
    }

    /**
     * @param string $name   Input name
     * @param string $value
     * @param array  $source List of items 'value' => 'title' or 0 => 'value'
     * @param array  $attr   Input attributes, like class
     * @return string
     */
    public static function checkbox($name, $value, array $source, array $attr = [])
    {
        return self::optionElement('checkbox', $name, $source, $value, $attr);
    }

    /**
     * @param string $name   Input name
     * @param string $value
     * @param array  $source List of items 'value' => 'title' or 0 => 'value'
     * @param array  $attr   Input attributes, like class
     * @return string
     */
    public static function radio($name, $value, array $source, array $attr = [])
    {
        return self::optionElement('radio', $name, $source, $value, $attr);
    }

    /**
     * @param string      $name Input name
     * @param int|boolean $status
     * @return string
     */
    public static function yesNo($name, $status)
    {
        return self::optionElement('radio', $name, ['1' => t('yes'), '0' => t('no')], (int)$status, ['inline' => true]);
    }

    /**
     * Returns text input or textarea
     *
     * @param string $name Input name
     * @param string $value
     * @param array  $attr Input attributes, like class
     * @return string
     */
    public static function text($name, $value, array $attr = [])
    {
        if (isset($attr['rows']) && $attr['rows'] > 1) {
            return '<textarea name="'.$name.'"'.self::attr($attr).'>'.$value.'</textarea>';
        } else {
            return '<input type="text" value="'.$value.'" name="'.$name.'"'.self::attr($attr).'>';
        }
    }

    /**
     * @param string $name Input name
     * @param string $value
     * @param array  $attr Input attributes, like class
     * @return string
     */
    public static function input($name, $value, array $attr = [])
    {
        return '<input value="'.$value.'" name="'.$name.'"'.self::attr($attr).'>';
    }

    /**
     * @param string $name Input name
     * @param string $value
     * @param array  $attr Input attributes, like class
     * @return string
     */
    public static function hidden($name, $value, array $attr = [])
    {
        return '<input type="hidden" value="'.$value.'" name="'.$name.'"'.self::attr($attr).'>';
    }

    /**
     * @param string $label
     * @param string $type
     * @param array  $attr Input attributes, like class
     * @return string
     */
    public static function button($label = 'Submit', $type = 'submit', array $attr = [])
    {
        return '<button type="'.$type.'"'.self::attr($attr).'>'.$label.'</button>';
    }

    /**
     * @param string $name
     * @param array  $attr Input attributes, like class
     * @return string
     */
    public static function password($name, array $attr = [])
    {
        return '<input type="password" name="'.$name.'"'.self::attr($attr).'>';
    }

    /**
     * @param string $title
     * @param string $href
     * @param array  $attr Input attributes, like class
     * @return string
     */
    public static function link($href, $title, array $attr = [])
    {
        return '<a href="'.$href.'"'.self::attr($attr).'>'.$title.'</a>';
    }

    /**
     * @param string $file
     * @param string $button
     * @return string
     */
    public static function saveButton($file = '', $button = '')
    {
        if (!$file || (is_writable($file) && is_writable(dirname($file)))) {
            $btn = $button ? $button : '<button type="submit" class="btn btn-primary btn-block">'.t('save').'</button>';
        } else {
            $btn = '<button type="button" class="btn btn-primary btn-block disabled">'.t('not_writeable').'</button>';
        }
        return '<div class="form-group">'.$btn.'</div>';
    }

    public static function mastercodeInput()
    {
        return isset($_SESSION['admin']) ? '' : '<div class="form-group">
    <input type="text" name="mastercode" class="form-control" placeholder="'.t('mastercode').'" required>
</div>';
    }

    /**
     * @param array $crumbs 'title' => ''[, 'url' => '']
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
     * @param string $url     Base url
     * @param int    $total   Items count
     * @param int    $current From 'skip'
     * @param int    $limit   Per page
     * @param string $class   Class to div-wrapper
     * @param int    $links   The number of links on the left and right of the current
     * @return string
     */
    public static function pagination($url, $total, $current, $limit = 10, $class = 'pagination', $links = 3)
    {
        $pages = ceil($total / $limit);
        if ($pages < 2) {
            return '';
        }

        // TODO переписать под psr-7 и заменить скип на пейдж
        $get = app('request')->getUri();
        unset($get['skip']);
        if (count($get)) {
            $url .= '?'.str_replace('%2F', '/', http_build_query($get)).'&';
        } else {
            $url .= '?';
        }

        $thisPage = floor($current / $limit);

        if ($pages < ($links * 2) + 2) {
            $from = 1;
            $to = $pages;
        } else {
            if ($thisPage < $links + 1) {
                $from = 1;
                $to = ($links * 2) + 1;
            } elseif ($thisPage < $pages - $links - 1) {
                $from = $thisPage - ($links - 1);
                $to = $thisPage + ($links + 1);
            } else {
                $from = $pages - ($links * 2);
                $to = $pages;
            }
        }
        $html = '';
        for ($i = $from; $i <= $to; $i++) {
            $skip = ($i - 1) * $limit;
            if ($current == $skip) {
                $html .= '<li class="active"><span>'.$i.'</span></li>';
            } else {
                $html .= '<li><a href="'.$url.'skip='.$skip.'">'.$i.'</a></li>';
            }
        }
        if ($pages > ($links * 2) + 1) {
            $html = '<li><a href="'.$url.'skip=0">&laquo;</a></li>'.$html.'<li><a href="'.$url.'skip='.($pages - 1) * $limit.'">&raquo;</a></li>';
        }

        return '<ul class="'.$class.'">'.$html.'</ul>';
    }

    /**
     * Gets tree from flat array
     *
     * @param array    $data      Flat array with elements.
     *                            Each element must have at least 'level'.
     *                            Each element can have 'attr' for LI and other data
     * @param int      $max_level Maximum tree depth
     * @param array    $attr      Attributes for UL
     * @param callable $callback  Function name, that can return a string with html
     * @return string
     */
    public static function treeList(array $data, callable $callback, array $attr = [], $max_level = 20)
    {
        reset($data);
        $cur = current($data);
        $prev_level = $cur['level'];
        $html = '<ul'.self::attr($attr).'>';

        foreach ($data as $item) {
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
     * @param        $data
     * @param string $current
     * @param array  $attr
     * @return string
     */
    public static function listLinks($data, $current = '', array $attr = [])
    {
        $html = '<ul'.self::attr($attr).'>';
        foreach ($data as $link => $title) {
            $active = $current == $link ? ' class="active"' : '';
            $html .= '<li'.$active.'><a href="'.$link.'">'.$title.'</a></li>';
        }
        return $html.'</ul>';
    }

    public static function table($rows, $header = [], $attr = '')
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

    public static function tab($data, $current = '', $position = 'top', $attr = '')
    {
        $titles = $contents = '';
        foreach ($data as $key => $d) {
            $active = $current == $key ? ' active' : '';
            $titles .= '<li class="'.$active.'"><a href="#'.$key.'" data-toggle="tab">'.$d['title'].'</a></li>';
            $contents .= '<div class="tab-pane'.$active.'" id="'.$key.'">'.$d['content'].'</div>';
        }
        if ($position == 'left') {
            return '<div class="row tab-container"><div class="col-xs-2"><ul class="nav nav-tabs-left">'.$titles.'</ul></div><div class="col-xs-10"><div class="tab-content">'.$contents.'</div></div></div>';
        } elseif ($position == 'right') {
            return '<div class="row tab-container"><div class="col-xs-10"><div class="tab-content">'.$contents.'</div></div><div class="col-xs-2"><ul class="nav nav-tabs-right">'.$titles.'</ul></div></div>';
        }
        return '<div '.$attr.'><ul class="nav nav-tabs">'.$titles.'</ul><div class="tab-content">'.$contents.'</div></div>';
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
     * @param string $attr_string
     * @return array
     */
    public static function parseAttr($attr_string)
    {
        $attr = [];

        $pattern = '/([\w-]+)\s*(=\s*"([^"]*)")?/';
        preg_match_all($pattern, $attr_string, $matches, PREG_SET_ORDER);

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
        $str = ' ';
        foreach ($attr as $key => $values) {
            if ($values === true) {
                $str .= $key.' ';
            } elseif (is_array($values)) {
                $str .= $key.'="'.implode(' ', $values).'" ';
            } else {
                $str .= $key.'="'.$values.'" ';
            }
        }
        return $str;
    }
}
