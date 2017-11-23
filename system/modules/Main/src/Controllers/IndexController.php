<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Main\Controllers;

use Sydes\Contracts\Http\Request;

class IndexController
{
    public function installer(Request $r)
    {
        if (model('Settings/App')->isCreated()) {
            return text('Site already installed');
        }

        $num = $r->input('step', 1);
        $installer = model('Main/Installer');

        $stepData = [];
        if ($num == 1) {
            $installer->step1();

            if (!$stepData['locales'] = model('Main/Translations')->getAvailable('Main')) {
                return text('Api server down. Please, download language package manually and unzip into /app/languages');
            }
        } elseif ($num == 2) {
            $stepData['locale'] = $r->input('locale');

            if ($stepData['locale'] != 'en') {
                $installer->step2($stepData['locale']);
            }

            app('translator')->init($stepData['locale']);
        } elseif ($num == 3) {
            $installer->step3($r->only('email', 'username', 'password', 'locale') + [
                'siteName' => 'Site Name',
                'domain' => $r->getUri()->getHost(),
                'timeZone' => $r->input('time_zone'),
            ]);

            return redirect('/admin/sites/1');
        }

        $dir = app('dir.system').'/modules/Main/views/installer';
        $step = render($dir.'/step'.$num.'.php', $stepData);

        return html(render($dir.'.php', compact('step', 'num')));
    }

    public function siteNotFound()
    {
        return 'siteNotFound';
    }

    public function error($code)
    {
        abort($code, 'Page not found');
    }

    public function redirect($url)
    {
        return redirect($url);
    }

    public function view($view)
    {
        return view($view);
    }

    public function robots()
    {
        return 'robots content';
    }

    public function sitemap()
    {
        return 'sitemap content';
    }
}
