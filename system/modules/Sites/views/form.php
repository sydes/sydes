<div class="card box">
    <div class="card-block">
        <?=Form::fromArray($site, $options);?>

        <div class="row">
            <div class="col-sm-6">
                <?=H::formGroup(t('site_name'), Form::field('Text', 'name', null, ['required' => true]));?>
            </div>
            <div class="col-sm-6">
                <?=H::formGroup(t('site_theme'), Form::field('List', 'theme', null, ['items' => $themes]));?>
            </div>
            <div class="col-sm-6">
                <?=H::formGroup(t('site_domains'),
                    Form::field('TextList', 'domains', null, ['required' => true]),
                    t('one_per_line'));?>
            </div>
            <div class="col-sm-6">
                <?=H::formGroup(t('site_use_only_main_domain'), Form::field('YesNo', 'onlyMainDomain'));?>
            </div>
            <div class="col-sm-6">
                <?=H::formGroup(t('site_locales'),
                    Form::field('TextList', 'locales', null, ['required' => true]),
                    t('one_per_line'));?>
            </div>
            <div class="col-sm-6">
                <?=H::formGroup(t('site_locale_in'), Form::field('List', 'localeIn', null, ['items' => [
                    'url' => t('in_url'),
                    'domain' => t('in_domain'),
                ]]));?>
                <?=H::formGroup(t('domain_to_locale'), Form::field('TextList', 'host2locale'), t('domain_to_locale_hint'));?>
            </div>
            <div class="col-sm-6">
                <?=H::formGroup(t('site_works'), Form::field('YesNo', 'work'));?>
            </div>
        </div>

        <?=Form::close();?>
    </div>
</div>
