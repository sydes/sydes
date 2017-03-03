<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Fileman;

use App\Cmf;

class Controller
{
	public function install(Cmf $cmf)
    {
        $cmf->addMenuItem('tools', [
            'title' => 'module_fileman',
            'url' => '#',
        ], 10);
    }

	public function uninstall(Cmf $cmf)
    {
        $cmf->removeMenuItem('tools', '#');
    }
}
