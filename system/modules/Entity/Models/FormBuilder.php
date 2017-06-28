<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Entity\Models;

class FormBuilder
{
    /** @var EntityInterface */
    private static $model;

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

        return '</form>';
    }

    /**
     * @param EntityInterface $model
     * @param array $options
     * @return string
     */
    public static function model(EntityInterface $model, array $options = [])
    {
        self::$model = $model;

        return self::open($options);
    }

    /**
     * @param EntityInterface $model
     * @param array $options
     * @return string
     */
    public static function auto(EntityInterface $model, array $options = [])
    {
        $form = self::model($model, $options);

        if (isset($options['formatter']) && is_callable($options['formatter'])) {
            $wrapper = $options['formatter'];
        } else {
            $wrapper = function (FieldInterface $field) {
                $help = $field->getSettings('helpText') ?
                    \H::tag('small', $field->getSettings('helpText'), ['class'=>'form-text text-muted']) : '';

                return '<div class="form-group row">'.
                    '<label class="col-3 col-form-label">'.t($field->getSettings('label')).'</label>'.
                    '<div class="col-9">'.$field->input().$help.'</div></div>';
            };
        }

        foreach (self::$model->allFields() as $name => $field) {
            $form .= $field->formInput($wrapper);
        }

        $form .= \H::submitButton(t(ifsetor($options['btn_text'], 'save')), ['button' => 'primary']);
        $form .= self::close();

        return $form;
    }

    /**
     * @param string $name
     * @return string
     */
    public static function input($name)
    {
        return self::$model->field($name)->formInput();
    }
}
