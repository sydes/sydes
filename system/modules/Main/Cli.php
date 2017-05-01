<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Main;

use Sydes\Console\Commands;
use Sydes\Console\Output;

class Cli
{
    public function __construct(Commands $commands, Output $you)
    {
        $commands->add('help', function () use ($commands, $you) {
            $you->say('Welcome to SyDES CLI')
                ->say('')
                ->say('Usage:')
                ->say('  command [arguments] [options]')
                ->say('')
                ->say('Options:')
                ->say('  -h, --help  Display description about command')
                ->say('')
                ->say('Available commands:')
                ->say('  '.implode("\n  ", $commands->getCommands()));
        });

        $commands->add('goto-site id', function ($id) use ($you) {
            if (!is_int($id) || !file_exists(DIR_SITE.'/'.$id)) {
                $you->say("Can't find site with id ".$id);
                return;
            }

            $_SESSION['site'] = $id;

            $you->say('You in site '.$id);
        }, ['Select site to work with', [
            'id' => 'Site ID',
        ]]);

        $commands->add('install [extension] [name] [-d|--demo]',
            function ($extension, $name, $demo = false) use ($you) {
                if ($extension && $name) {
                    if ($extension == 'module') {
                        model('Modules')->install($name);
                    } else {
                        model('Themes')->activate($name);
                    }

                    $you->say('You installed '.$extension.' '.$name);
                } else {
                    if (model('Settings/App')->isCreated()) {
                        $you->say('Site already installed');
                        return;
                    }

                    $params = [
                        'email' => 'admin@domain.tld',
                        'username' => 'demo',
                        'password' => 'demo',
                        'mastercode' => 'demo',
                        'siteName' => 'Demo site',
                        'locale' => 'en',
                        'domain' => '',
                        'timeZone' => 0,
                    ];

                    if ($demo) {

                        $you->say("You can enter with these credentials:
Username: demo\nPassword: demo\nDeveloper password: demo\nBut we need some info...");

                    } else {

                        $you->say('Ok! Just answer the following questions')
                            ->say('First, create your account');
                        $params['email'] = $you->ask('Email');
                        $you->rewind();
                        $params['username'] = $you->ask('Username');
                        $you->rewind();
                        $params['password'] = $you->ask('Password');
                        $you->rewind();
                        $params['mastercode'] = $you->ask('Developer password');
                        $you->rewind();
                        $you->say('Good!');
                        $you->say("Now we'll create your site");
                        $params['siteName'] = mb_convert_encoding($you->ask('Site name'), 'UTF-8', 'cp866');
                        $you->rewind();
                        $params['locale'] = $you->ask('Locale (en)');
                        $you->rewind();

                    }

                    $params['domain'] = $you->ask('Domain (test.com)');
                    $you->rewind();

                    $installer = model('Main/Installer');
                    $installer->step1();
                    $installer->step2($params['locale']);
                    $installer->step3($params);

                    $what = $demo ? 'Demo site' : 'Site';
                    $you->say($what.' installed')
                        ->say('Have a nice day!');
                }
            }, ['Install site or provide type and name of extension to install it', [
                'extension' => "Optional, 'module' or 'theme'",
                'name' => 'Optional, name of extension',
                '-d, --demo' => 'Use to install demo site',
            ]]);

        $commands->add('update [extension] [name]', function ($extension = null, $name = null) use ($you) {
            if (model('Updater')->up($extension, $name)) {
                if ($extension && $name) {
                    $you->say('You updated '.$extension.' '.$name);
                } else {
                    $you->say('SyDES updated');
                }
            }
        }, ['Update site or provide type and name of extension to update it', [
            'extension' => "Optional, 'module' or 'theme'",
            'name' => 'Optional, name of extension',
        ]]);

        $commands->add('uninstall [extension] [name] [-d]',
            function ($extension = false, $name = false, $d = false) use ($commands, $you) {
                if ($extension && $name) {
                    if ($extension == 'module') {
                        model('Modules')->uninstall($name);
                        $you->say('You uninstalled module '.$name);
                    } else {
                        $you->say('You can\'t uninstall theme');
                    }

                    if ($d) {
                        $commands->run('delete', [$extension, $name]);
                    }
                } else {
                    model('Main/Installer')->uninstall();
                    $you->say('SyDES uninstalled');
                }
            }, ['Uninstall site or provide type and name of extension to uninstall it', [
                'extension' => "Optional, 'module' or 'theme'",
                'name' => 'Optional, name of extension',
                '-d' => 'Delete extension after uninstalling',
            ]]);

        $commands->add('download extension name [-i]', function ($extension, $name, $i = false) use ($commands, $you) {

            $you->say('Downloading...');

            $you->say('You downloaded '.$extension.' '.$name);

            if ($i) {
                $commands->run('install', [$extension, $name]);
            }
        }, ['Download extension by type and name', [
            'extension' => "'module' or 'theme'",
            'name' => 'name of extension',
            '-i' => 'Install extension after downloading',
        ]]);

        $commands->add('delete extension name', function ($extension, $name) use ($you) {

            $you->say('You deleted '.$extension.' '.$name);
        }, ['Delete extension by type and name', [
            'extension' => "'module' or 'theme'",
            'name' => 'name of extension',
        ]]);

        $commands->add('make extension name', function ($extension, $name) use ($you) {

            $you->say('You created base files for '.$extension.' '.$name);
        }, ['Make base files for extension', [
            'extension' => "'module', 'plugin', 'iblock', or 'theme'",
            'name' => 'name of extension',
        ]]);
    }
}
