<?=Form::fromArray($data, $options);?>
<div class="card box">
    <div class="card-block">
        <div class="row">
            <div class="col-sm-6">
                <?=H::formGroup(t('app_time_zone'), Form::field('Text', 'timeZone', null, ['required' => true]));?>
            </div>
            <div class="col-sm-6">

            </div>
            <div class="col-sm-6">
                <?=H::formGroup(t('app_date_format'), Form::field('Text', 'dateFormat', null, ['required' => true]));?>
            </div>
            <div class="col-sm-6">
                <?=H::formGroup(t('app_time_format'), Form::field('Text', 'timeFormat', null, ['required' => true]));?>
            </div>
            <div class="col-sm-6">
                <?=H::formGroup(t('app_locale'), Form::field('Text', 'locale', null, ['required' => true]));?>
            </div>
        </div>
    </div>
</div>

<div class="card box">
    <div class="card-block">
        <h4 class="card-title"><?=t('mailer_settings');?></h4>

        <div class="row">
            <div class="col-sm-6">
                <?=H::formGroup(t('mailer_default_from'), Form::field('Text', 'mailer_defaultFrom', null, ['required' => true]));?>
            </div>
            <div class="col-sm-6">
                <?=H::formGroup(t('mailer_default_to'), Form::field('Text', 'mailer_defaultTo', null, ['required' => true]));?>
            </div>
            <div class="col-sm-6">
                <?=H::formGroup(t('mailer_smtp'), Form::field('YesNo', 'mailer_useSmtp'));?>
            </div>
        </div>

        <div class="row on-smtp">
            <div class="col-sm-6">
                <?=H::formGroup(t('mailer_smtp_host'), Form::field('Text', 'mailer_smtpHost'));?>
            </div>
            <div class="col-sm-6">
                <?=H::formGroup(t('mailer_smtp_port'), Form::field('Text', 'mailer_smtpPort'));?>
            </div>
            <div class="col-sm-6">
                <?=H::formGroup(t('mailer_smtp_user'), Form::field('Text', 'mailer_smtpUser'));?>
            </div>
            <div class="col-sm-6">
                <?=H::formGroup(t('mailer_smtp_password'), Form::field('Text', 'mailer_smtpPassword'));?>
            </div>
        </div>

        <?=H::formGroup(t('mailer_send_also'), Form::field('Text', 'mailer_sendAlso'));?>
    </div>
</div>
<?=Form::close();?>
