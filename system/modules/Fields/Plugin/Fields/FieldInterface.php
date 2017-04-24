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

    public function fromString($value);

    public function toString();

    public function getValue();

    public function getSettings();

    public function getFormatters();

    public function validate();

    public function getField();

    public function getSettingsForm();

    public function render($formatter = null);
}
