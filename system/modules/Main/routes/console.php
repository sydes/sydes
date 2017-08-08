<?php
if (!$app) {
    return;
}

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

$app->command('site id', function ($id, OutputInterface $output) {
    if (!is_int($id) || !file_exists(app('dir.site').'/'.$id)) {
        $output->writeln("Can't find site with id ".$id);
        return;
    }

    $_SESSION['site'] = $id;

    $output->writeln('You in site '.$id);
})->descriptions('Select site to work with', [
    'id' => 'Site ID',
]);

$app->command('install [extension] [name] [-d|--demo]',
    function ($extension, $name, $demo, InputInterface $input, OutputInterface $output) {
        if ($extension && $name) {
            if ($extension == 'module') {
                model('Modules')->install($name);
            } else {
                model('Themes')->activate($name);
            }

            $output->writeln('You installed '.$extension.' '.$name);
        } else {
            if (model('Settings/App')->isCreated()) {
                $output->writeln('Site already installed');
                return;
            }

            /** @var QuestionHelper $helper */
            $helper = $this->getHelperSet()->get('question');
            $params = [
                'email'    => 'admin@domain.tld',
                'username' => 'demo',
                'password' => 'demo',
                'siteName' => 'Demo site',
                'locale'   => 'en',
                'domain'   => '',
                'timeZone' => 0,
            ];

            if ($demo) {

                $output->writeln("You can enter with these credentials:
Username: demo\nPassword: demo\nDeveloper password: demo\nBut we need some info...");

            } else {

                $output->writeln('Ok! Just answer the following questions');
                $output->writeln('First, create your account');

                $params['email'] = $helper->ask($input, $output, new Question('Email'));
                $params['username'] = $helper->ask($input, $output, new Question('Username'));
                $params['password'] = $helper->ask($input, $output, new Question('Password'));

                $output->writeln('Good!');
                $output->writeln("Now we'll create your site");
                $params['siteName'] = mb_convert_encoding(
                    $helper->ask($input, $output, new Question('Site name')), 'UTF-8', 'cp866'
                );
                $params['locale'] = $helper->ask($input, $output, new Question('Locale (en)'));

            }

            $params['domain'] = $helper->ask($input, $output, new Question('Domain (test.com)'));

            $installer = model('Main/Installer');
            $installer->step1();
            $installer->step2($params['locale']);
            $installer->step3($params);

            $what = $demo ? 'Demo site' : 'Site';
            $output->writeln($what.' installed');
            $output->writeln('Have a nice day!');
        }
    })->descriptions('Install site or provide type and name of extension to install it', [
    'extension'  => "Optional, 'module' or 'theme'",
    'name'       => 'Optional, name of extension',
    '--demo' => 'Use to install demo site',
]);

$app->command('update [extension] [name]', function ($extension = null, $name = null, OutputInterface $output) {
    if (model('Updater')->up($extension, $name)) {
        if ($extension && $name) {
            $output->writeln('You updated '.$extension.' '.$name);
        } else {
            $output->writeln('SyDES updated');
        }
    }
})->descriptions('Update site or provide type and name of extension to update it', [
    'extension' => "Optional, 'module' or 'theme'",
    'name'      => 'Optional, name of extension',
]);

$app->command('uninstall [extension] [name] [-d|--delete]',
    function ($extension = false, $name = false, $delete, OutputInterface $output) {
        if ($extension && $name) {
            if ($extension == 'module') {
                model('Modules')->uninstall($name);
                $output->writeln('You uninstalled module '.$name);
            } else {
                $output->writeln('You can\'t uninstall theme');
            }

            if ($delete) {
                $this->runCommand("delete {$extension} {$name}", $output);
            }
        } else {
            model('Main/Installer')->uninstall();
            $output->writeln('SyDES uninstalled');
        }
    })->descriptions('Uninstall site or provide type and name of extension to uninstall it', [
    'extension' => "Optional, 'module' or 'theme'",
    'name'      => 'Optional, name of extension',
    '--delete'  => 'Delete extension after uninstalling',
]);

$app->command('download extension name [-i|--install]', function ($extension, $name, $install, OutputInterface $output) {
    $output->writeln('Downloading...');
    $output->writeln('You downloaded '.$extension.' '.$name);

    if ($install) {
        $this->runCommand("install {$extension} {$name}", $output);
    }
})->descriptions('Download extension by type and name', [
    'extension' => 'module or theme',
    'name'      => 'name of extension',
    '--install' => 'Install extension after downloading',
]);

$app->command('delete extension name', function ($extension, $name, OutputInterface $output) {
    $output->writeln('You deleted '.$extension.' '.$name);
})->descriptions('Delete extension by type and name', [
    'extension' => "'module' or 'theme'",
    'name'      => 'name of extension',
]);

$app->command('make extension name', function ($extension, $name, OutputInterface $output) {
    $output->writeln('You created base files for '.$extension.' '.$name);
})->descriptions('Make base files for extension', [
    'extension' => 'module, plugin, iblock or theme',
    'name'      => 'name of extension',
]);
