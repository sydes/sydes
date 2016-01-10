<?php

class SitesController
{
    public function addForm()
    {
        $themes = glob(DIR_THEME.'/*');
        $themeNames = [];
        foreach ($themes as $theme) {
            $themeData = parse_json_file($theme.'/composer.json');
            $theme = str_replace(DIR_THEME.'/', '', $theme);
            $themeNames[$theme] = $themeData['extra']['name'];
        }

        $data = [];
        $data['content'] = view('sites/form', [
            'title'             => t('new_site'),
            'name'              => '',
            'locales'           => substr(app('request')->getPreferredLanguage(app('translator')->installedPackages), 0, 2),
            'domains'           => app('request')->server['HTTP_HOST'],
            'use_alias_as_path' => 0,
            'maintenance_mode'  => 0,
            'need_cache'        => 0,
            'template'          => HTML::select('theme', '', $themeNames, 'class="form-control"'),
            'site'              => 'new',
            'sites'             => str_replace(DIR_SITE, '', glob(DIR_SITE.'s*')),
        ]);
        $data['sidebar_right'] = HTML::saveButton().HTML::mastercodeInput();
        $data['sidebar_left'] = '';
        $data['form_url'] = 'admin/sites/add';
        $data['meta_title'] = t('site_creation');
        $data['breadcrumbs'] = [
            ['url' => '?route=config', 'title' => t('settings')],
            ['url' => '?route=config/sites', 'title' => t('site_list')],
            ['title' => t('site_creation')],
        ];

        $d = document($data);

        return $d;
    }

    public function add()
    {
        notify('Added');
        return redirect('admin/sites');
    }

}
