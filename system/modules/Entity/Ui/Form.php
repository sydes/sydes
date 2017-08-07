<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Entity\Ui;

use Sydes\Database\Entity\Field;
use Sydes\Database\Entity\Model;

class Form
{
    /** @var Model */
    private static $model;
    private static $data;
    private static $fields;

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
        self::$model = null;
        self::$data = null;

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
        if (self::$fields === null) {
            self::$fields = app('entity.fieldTypes');
        }

        return self::open($options);
    }

    /**
     * @param Model $model
     * @param array $options
     * @return string
     */
    public static function model(Model $model, array $options = [])
    {
        self::$model = $model;

        return self::open($options);
    }

    /**
     * @param Model $model
     * @param array $options
     * @return string
     */
    public static function auto(Model $model, array $options = [])
    {
        $form = self::model($model, $options);

        if (isset($options['formatter']) && is_callable($options['formatter'])) {
            $wrapper = $options['formatter'];
        } else {
            $wrapper = function (Field $field) {
                $help = $field->settings('helpText') ?
                    \H::tag('small', t($field->settings('helpText')), ['class' =>'form-text text-muted']) : '';

                return '<div class="form-group row">'.
                    '<label class="col-3 col-form-label">'.$field->label().'</label>'.
                    '<div class="col-9">'.$field->defaultInput().$help.'</div></div>';
            };
        }

        foreach (self::$model->getFields() as $name => $field) {
            if (!empty($field->label())) {
                $form .= $field->input($wrapper);
            }
        }

        if (isset($options['submit_button'])) {
            $form .= \H::submitButton(t($options['submit_button']), ['button' => 'primary']);
        }

        $form .= self::close();

        return $form;
    }

    /**
     * @param string $name
     * @param string $type field type name
     * @param array  $opts
     * @return string
     */
    public static function input($name, $type = 'Text', array $opts = [])
    {
        if (self::$model !== null) {
            return self::$model->field($name)->input();
        }

        if (!isset(self::$fields[$type])) {
            throw new \InvalidArgumentException(t('field_not_exists', ['name' => $type]));
        }

        $value = ifsetor(self::$data[$name], '');
        /** @var Field $field */
        $field = new self::$fields[$type]($name, $value, $opts);

        return $field->input();
    }
}
