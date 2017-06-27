<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Entity\Models;

class FormBuilder
{
    private static $data = [];
    private static $fields = [];

    /**
     * Open up a new HTML form.
     *
     * @param array $options
     * @return string
     */
    public static function open(array $options = [])
    {
        $append = '';
        $attr = ['method' => 'get'];
        $method = strtolower(ifsetor($options['method'], 'post'));

        if ($method != 'get') {
            $attr['method'] = 'post';

            if ($method != 'post') {
                $append .= method_field($method);
            }
        }

        $attr['action'] = $options['url'];
        $attr['accept-charset'] = 'UTF-8';

        if (isset($options['files']) && $options['files']) {
            $attr['enctype'] = 'multipart/form-data';
        }

        if (isset($options['form'])) {
            $attr['id'] = 'form-'.$options['form'];
        }

        return \H::beginTag('form', $attr).$append;
    }

    /**
     * Close the current form.
     *
     * @return string
     */
    public static function close()
    {
        self::$data = [];

        return '</form>';
    }

    /**
     * @param array $data
     * @param array $options
     * @return string
     */
    public static function fromArray(array $data, array $options = [])
    {
        self::$data = $data;
        self::$fields = app('form.fields');

        return self::open($options);
    }

    /**
     * @param string $fieldType
     * @param string $name
     * @param string $value
     * @param array  $settings
     * @return string
     */
    public static function field($fieldType, $name, $value = null, $settings = [])
    {
        if (!isset(self::$fields[$fieldType])) {
            throw new \InvalidArgumentException(t('field_not_exists', ['name' => $fieldType]));
        }

        if (is_null($value) && isset(self::$data[$name])) {
            $value = self::$data[$name];
        }

        /** @var FieldInterface $field */
        $field = new self::$fields[$fieldType]($name, $value, $settings);

        return $field->formInput();
    }

    /**
     * @param array $fields
     * @param array $data
     * @param array $options
     * @return string
     */
    public static function auto(array $fields, array $data, array $options = [])
    {
        $form = self::fromArray($data, $options);

        if (isset($options['formatter']) && is_callable($options['formatter'])) {
            $formatter = $options['formatter'];
        } else {
            $formatter = function ($params) {
                return '<div class="form-group row">
<label class="col-3 col-form-label">'.$params['label'].'</label>
<div class="col-9">'.$params['input'].'</div></div>';
            };
        }

        foreach ($fields as $name => $field) {
            $settings = ifsetor($field['settings'], []);
            $form .= $formatter([
                'label' => $field['label'],
                'input' => self::field($field['type'], $name, null, $settings)
            ]);
        }

        $form .= self::close();

        return $form;
    }
}
