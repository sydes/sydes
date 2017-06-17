<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Fields\Plugin\Fields;

interface FieldInterface
{
    public function __construct($name, $value, $settings = []);

    public function set($value);

    public function get();

    public function setRaw($value);

    public function getRaw();

    public function getSettings($key = null);

    public function getFormatters();

    public function validate();

    public function getInput();

    public function getSettingsForm();

    public function render($formatter = null);
}
