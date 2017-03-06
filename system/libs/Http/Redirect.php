<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace App\Http;

use Zend\Diactoros\Response\RedirectResponse;

class Redirect extends RedirectResponse
{
    private $uri;

    public function __construct($uri, $status = 302, array $headers = [])
    {
        $this->uri = (string) $uri;

        parent::__construct($uri, $status, $headers);
    }

    public function getUri()
    {
        return $this->uri;
    }
}
