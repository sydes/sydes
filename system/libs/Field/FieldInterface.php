<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App\Field;

interface FieldInterface
{
    public function __construct($value);

    public function getView($params);

    public function getInput($params);

    public function getValue();

    public function validate($params);

    public function serialize();
}
