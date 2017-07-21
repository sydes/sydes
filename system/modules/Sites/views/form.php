<div class="card box">
    <div class="card-block">
        <?=Form::fromArray($site, $options);?>

        <div class="row">
            <div class="col-sm-6">
                <?=Form::input('name', 'Text', [
                    'required' => true,
                    'label' => 'site_name',
                ]);?>
            </div>
            <div class="col-sm-6">
                <?=Form::input('theme', 'List', [
                    'label' => 'site_theme',
                    'items' => $themes,
                ]);?>
            </div>
            <div class="col-sm-6">
                <?=Form::input('domains', 'TextList', [
                    'required' => true,
                    'label' => 'site_domains',
                    'helpText' => 'one_per_line',
                ]);?>
            </div>
            <div class="col-sm-6">
                <?=Form::input('onlyMainDomain', 'YesNo', [
                    'label' => 'site_use_only_main_domain',
                ]);?>
            </div>
            <div class="col-sm-6">
                <?=Form::input('locales', 'TextList', [
                    'required' => true,
                    'label' => 'site_locales',
                    'helpText' => 'one_per_line',
                ]);?>
            </div>
            <div class="col-sm-6">
                <?=Form::input('localeIn', 'List', [
                    'label' => 'site_locale_in',
                    'items' => [
                        'url' => t('in_url'),
                        'domain' => t('in_domain'),
                    ]
                ]);?>
                <?=Form::input('host2locale', 'TextList', [
                    'label' => 'domain_to_locale',
                    'helpText' => 'domain_to_locale_hint',
                ]);?>
            </div>
            <div class="col-sm-6">
                <?=Form::input('work', 'YesNo', [
                    'label' => 'site_works',
                ]);?>
            </div>
        </div>

        <?=Form::close();?>
    </div>
</div>
