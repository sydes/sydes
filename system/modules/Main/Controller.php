<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Main;

use Sydes\Route;

class Controller
{
    public static function routes(Route $r)
    {
        $r->get('/robots.txt', 'Main@robots');
        $r->get('/sitemap.xml', 'Main@sitemap');
    }

    public function installer()
    {
        if (file_exists(DIR_APP.'/config.php')) {
            return text('Site already installed');
        }

        $r = app('request');
        $step = $r->input('step', 1);
        $installer = model('Main/Installer');

        $stepData = [];
        if ($step == 1) {
            $installed = str_replace([DIR_L10N.'/locales/', '.php'], '', glob(DIR_L10N.'/locales/*.php'));

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
        } elseif ($step == 2) {
            $stepData['locale'] = $r->input('locale');

            $installer->step2($stepData['locale']);

            app('translator')->init($stepData['locale']);
        } elseif ($step == 3) {
            $installer->step3($r->only('email', 'username', 'password', 'mastercode', 'locale') + [
                'siteName' => 'Site Name',
                'domain' => $r->getUri()->getHost(),
                'timeZone' => $r->input('time_zone'),
            ]);

            app('user')->login($r->input('username'), $r->input('password'));

            return redirect('/admin/sites/1');
        }

        return $this->step($step, $stepData);
    }

    private function step($num, $data)
    {
        $dir = DIR_SYSTEM.'/modules/Main/views/installer';
        $step = render($dir.'/step'.$num.'.php', $data);

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
