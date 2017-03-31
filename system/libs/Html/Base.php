<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace App\Html;

class Base
{
    protected static $extenders = [];

    public static $voidElements = [
        'area' => 1,
        'base' => 1,
        'br' => 1,
        'col' => 1,
        'command' => 1,
        'embed' => 1,
        'hr' => 1,
        'img' => 1,
        'input' => 1,
        'keygen' => 1,
        'link' => 1,
        'meta' => 1,
        'param' => 1,
        'source' => 1,
        'track' => 1,
        'wbr' => 1,
    ];

    public static $dataAttributes = ['data', 'data-ng', 'ng'];

    /**
     * Extend class with new html generators
     *
     * @param string   $name
     * @param callable $closure
     */
    public static function extend($name, callable $closure)
    {
        static::$extenders[$name] = $closure;
    }

    /**
     * Get the extender by name
     *
     * @param string $name
     * @param array  $arguments
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function __callStatic($name, array $arguments)
    {
        if (!array_key_exists($name, static::$extenders)) {
            throw new \InvalidArgumentException('Undefined HTML extender "'.$name.'"');
        }

        return call_user_func_array(static::$extenders[$name], $arguments);
    }

    /**
     * @param string $content
     * @param bool   $doubleEncode
     * @return string
     */
    public static function encode($content, $doubleEncode = true)
    {
        return htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', $doubleEncode);
    }

    /**
     * @param string $content
     * @return string
     */
    public static function decode($content)
    {
        return htmlspecialchars_decode($content, ENT_QUOTES);
    }

    /**
     * Parse string with attributes to array
     *
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
     * Renders the HTML tag attributes from array
     *
     * @param array $attr
     * @return string
     */
    public static function attr(array $attr)
    {
        $str = '';
        foreach ($attr as $key => $value) {
            if (empty($value)) {
                continue;
            }

            if ($value === true) {
                $str .= " $key";
            } elseif (in_array($key, static::$dataAttributes)) {
                foreach ($value as $k => $v) {
                    if (is_array($v)) {
                        $str .= " $key-$k=\"".json_encode($v,
                                JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG).'"';
                    } else {
                        $str .= " $key-$k=\"".static::encode($v).'"';
                    }
                }
            } elseif (is_array($value)) {
                $str .= " $key=\"".implode(' ', $value).'"';
            } else {
                $str .= " $key=\"".static::encode($value).'"';
            }
        }

        return $str;
    }

    /**
     * @param array        $attr
     * @param array|string $classes
     * @return array
     */
    public static function attrAddClass($attr, $classes)
    {
        if (is_string($classes)) {
            $classes = explode(' ', $classes);
        }

        if (isset($attr['class'])) {
            foreach ($classes as $class) {
                if (!empty($class) && !in_array($class, $attr['class'])) {
                    $attr['class'][] = $class;
                }
            }
        } else {
            $attr['class'] = $classes;
        }

        return $attr;
    }

    /**
     * @param array        $attr
     * @param array|string $classes
     * @return array
     */
    public static function attrRemoveClass($attr, $classes)
    {
        if (is_string($classes)) {
            $classes = explode(' ', $classes);
        }

        if (isset($attr['class'])) {
            $classes = array_diff($attr['class'], $classes);
            if (empty($classes)) {
                unset($attr['class']);
            } else {
                $attr['class'] = $classes;
            }
        }

        return $attr;
    }

    /**
     * @param string $name
     * @param string $content
     * @param array  $attr
     * @return string
     */
    public static function tag($name, $content = '', $attr = [])
    {
        if (!$name) {
            return $content;
        }

        $html = "<$name".static::attr($attr).'>';

        return isset(static::$voidElements[$name]) ? $html : "$html$content</$name>";
    }

    /**
     * @param string $name
     * @param array  $attr
     * @return string
     */
    public static function beginTag($name, $attr = [])
    {
        if (!$name) {
            return '';
        }

        return "<$name".static::attr($attr).'>';
    }

    /**
     * @param string $name
     * @return string
     */
    public static function endTag($name)
    {
        if (!$name) {
            return '';
        }

        return "</$name>";
    }

    /**
     * @param string $action
     * @param string $method
     * @param string $content
     * @param array  $attr
     * @return string
     */
    public static function form($action = '', $method = 'post', $content, array $attr = [])
    {
        $attr['action'] = $action;
        $attr['method'] = $method;

        return static::tag('form', $content, $attr);
    }

    /**
     * @param string      $text
     * @param string|bool $url
     * @param array       $attr
     * @return string
     */
    public static function a($text, $url = false, array $attr = [])
    {
        $attr['href'] = $url;

        return static::tag('a', $text, $attr);
    }

    /**
     * @param string $src
     * @param array  $attr
     * @return string
     */
    public static function img($src, $attr = [])
    {
        $attr['src'] = $src;

        if (isset($attr['srcset']) && is_array($attr['srcset'])) {
            $srcset = [];
            foreach ($attr['srcset'] as $descriptor => $url) {
                $srcset[] = $url.' '.$descriptor;
            }
            $attr['srcset'] = implode(',', $srcset);
        }

        if (!isset($attr['alt'])) {
            $attr['alt'] = '';
        }

        return static::tag('img', '', $attr);
    }

    /**
     * @param string $content
     * @param null   $for
     * @param array  $attr
     * @return string
     */
    public static function label($content, $for = null, $attr = [])
    {
        $attr['for'] = $for;
        return static::tag('label', $content, $attr);
    }

    /**
     * @param string $label
     * @param array  $attr
     * @return string
     */
    public static function button($label = 'Button', array $attr = [])
    {
        if (!isset($attr['type'])) {
            $attr['type'] = 'button';
        }

        return static::tag('button', $label, $attr);
    }

    /**
     * @param string $label
     * @param array  $attr
     * @return string
     */
    public static function submitButton($label = 'Submit', $attr = [])
    {
        $attr['type'] = 'submit';

        return static::button($label, $attr);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $items List of items 'value' => 'title'
     * @param array  $attr
     * @return string
     */
    public static function select($name, $value, array $items, array $attr = [])
    {
        if (empty($items)) {
            $items[''] = ' -- ';
        }

        $attr['name'] = $name.(isset($attr['multiple']) ? '[]' : '');

        $html = '';
        foreach ($items as $val => $title) {
            $opt = [
                'selected' => $val == $value,
                'value' => $val,
            ];
            $html .= static::tag('option', $title, $opt);
        }

        return static::tag('select', $html, $attr);
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

        return static::tag('textarea', static::encode($value), $attr);
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
            $attr['value'] = (string)$value;
        }

        return static::tag('input', '', $attr);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $attr
     * @return string
     */
    public static function textInput($name, $value, array $attr = [])
    {
        return static::input('text', $name, $value, $attr);
    }

    /**
     * @param string $name
     * @param array  $attr
     * @return string
     */
    public static function passwordInput($name, array $attr = [])
    {
        return static::input('password', $name, '', $attr);
    }

    /**
     * @param string $name
     * @param array  $attr
     * @return string
     */
    public static function fileInput($name, array $attr = [])
    {
        return static::input('file', $name, '', $attr);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $attr
     * @return string
     */
    public static function hiddenInput($name, $value, array $attr = [])
    {
        return static::input('hidden', $name, $value, $attr);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $attr
     * @return string
     */
    public static function colorInput($name, $value, array $attr = [])
    {
        return static::input('color', $name, $value, $attr);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $attr
     * @return string
     */
    public static function dateInput($name, $value, array $attr = [])
    {
        return static::input('date', $name, $value, $attr);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $attr
     * @return string
     */
    public static function emailInput($name, $value, array $attr = [])
    {
        return static::input('email', $name, $value, $attr);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $attr
     * @return string
     */
    public static function numberInput($name, $value, array $attr = [])
    {
        return static::input('number', $name, $value, $attr);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $attr
     * @return string
     */
    public static function rangeInput($name, $value, array $attr = [])
    {
        return static::input('range', $name, $value, $attr);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $attr
     * @return string
     */
    public static function searchInput($name, $value, array $attr = [])
    {
        return static::input('search', $name, $value, $attr);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $attr
     * @return string
     */
    public static function telInput($name, $value, array $attr = [])
    {
        return static::input('tel', $name, $value, $attr);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $attr
     * @return string
     */
    public static function timeInput($name, $value, array $attr = [])
    {
        return static::input('time', $name, $value, $attr);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $attr
     * @return string
     */
    public static function urlInput($name, $value, array $attr = [])
    {
        return static::input('url', $name, $value, $attr);
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
     * @param string $name
     * @param bool   $checked
     * @param array  $attr
     * @return string
     */
    public static function radio($name, $checked = false, $attr = [])
    {
        return static::booleanInput('radio', $name, $checked, $attr);
    }

    /**
     * @param string $name
     * @param bool   $checked
     * @param array  $attr
     * @return string
     */
    public static function checkbox($name, $checked = false, $attr = [])
    {
        return static::booleanInput('checkbox', $name, $checked, $attr);
    }

    /**
     * @param string $type
     * @param string $name
     * @param bool   $checked
     * @param array  $attr
     * @return string
     */
    protected static function booleanInput($type, $name, $checked = false, $attr = [])
    {
        $attr['checked'] = (bool)$checked;
        $value = array_key_exists('value', $attr) ? $attr['value'] : '1';

        if (isset($attr['label'])) {
            $label = $attr['label'];
            $labelOptions = isset($attr['labelAttr']) ? $attr['labelAttr'] : [];
            unset($attr['label'], $attr['labelAttr']);
            $content = static::label(static::input($type, $name, $value, $attr).' '.$label, null, $labelOptions);

            return $content;
        } else {
            return static::input($type, $name, $value, $attr);
        }
    }

    /**
     * @param array $items
     * @param array $attr
     * @return string
     */
    public static function ul($items, $attr = [])
    {
        $tag = isset($attr['tag']) ? $attr['tag'] : 'ul';
        $formatter = isset($attr['item']) ? $attr['item'] : null;
        $itemAttr = isset($attr['itemAttr']) ? $attr['itemAttr'] : [];

        unset($attr['tag'], $attr['item'], $attr['itemAttr']);

        if (empty($items)) {
            return static::tag($tag, '', $attr);
        }

        $results = [];
        foreach ($items as $index => $item) {
            if ($formatter !== null) {
                $results[] = call_user_func($formatter, $item, $index);
            } else {
                $results[] = static::tag('li', $item, $itemAttr);
            }
        }

        return static::tag($tag, implode('', $results), $attr);
    }

    /**
     * @param array $items
     * @param array $attr
     * @return string
     */
    public static function ol($items, $attr = [])
    {
        $attr['tag'] = 'ol';

        return static::ul($items, $attr);
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
    public static function treeList(array $items, callable $formatter, array $attr = [], $max_level = 20)
    {
        $cur = current($items);
        $prev_level = $cur['level'];
        $html = '<ul'.static::attr($attr).'>';

        foreach ($items as $item) {
            if (isset($item['skip']) || $max_level < $item['level']) {
                continue;
            }

            if ($prev_level < $item['level']) {
                $html .= '<ul>';
            } else {
                $html .= str_repeat('</li></ul>', $prev_level - $item['level']);
                $html .= '</li>';
            }

            $attr = isset($item['attr']) ? static::attr($item['attr']) : '';
            $html .= '<li'.$attr.'>'.$formatter($item);
            $prev_level = $item['level'];
        }

        return $html.str_repeat('</li></ul>', $prev_level);
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
}
