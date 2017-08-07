<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Main;

class Controller
{
    public function installer()
    {
        if (model('Settings/App')->isCreated()) {
            return text('Site already installed');
        }

        $r = app('request');
        $num = $r->input('step', 1);
        $installer = model('Main/Installer');

        $stepData = [];
        if ($num == 1) {
            $installed = str_replace([app('dir.l10n').'/locales/', '.php'], '', glob(app('dir.l10n').'/locales/*.php'));

            if (empty($installed)) {
                $data = app('api')->getTranslations('Main');
                if (!$data) {
                    return text('Api server down. Please, download language package manually and unzip into /app/l10n');
                }

                $all = app('api')->getLocales();
                foreach ($data as $d) {
                    $stepData['locales'][$d] = $all[$d];
                }
            } else {
                foreach ($installed as $key) {
                    $className = 'Locales\\'.$key;
                    $class = new $className;
                    $stepData['locales'][$class->getisoCode()] = $class->getNativeName();
                }
            }

            $installer->step1();
        } elseif ($num == 2) {
            $stepData['locale'] = $r->input('locale');

            $installer->step2($stepData['locale']);

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
        return 'redirect '.$url;
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
