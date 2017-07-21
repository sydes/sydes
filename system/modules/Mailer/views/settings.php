<div class="card box">
    <div class="card-block">
        <h4 class="card-title"><?=t('mailer_settings');?></h4>

        <?=Form::fromArray($data, $options);?>
        <div class="row">
            <div class="col-sm-6">
                <?=Form::input('default_from', 'Text', [
                    'required' => true,
                    'label' => 'mailer_default_from',
                ]);?>
            </div>
            <div class="col-sm-6">
                <?=Form::input('default_to', 'Text', [
                    'required' => true,
                    'label' => 'mailer_default_to',
                ]);?>
            </div>
            <div class="col-sm-6">
                <?=Form::input('use_smtp', 'YesNo', [
                    'label' => 'mailer_smtp',
                ]);?>
            </div>
        </div>

        <div class="row on-smtp">
            <div class="col-sm-6">
                <?=Form::input('smtp_host', 'Text', [
                    'label' => 'mailer_smtp_host',
                ]);?>
            </div>
            <div class="col-sm-6">
                <?=Form::input('smtp_port', 'Text', [
                    'label' => 'mailer_smtp_port',
                ]);?>
            </div>
            <div class="col-sm-6">
                <?=Form::input('smtp_user', 'Text', [
                    'label' => 'mailer_smtp_user',
                ]);?>
            </div>
            <div class="col-sm-6">
                <?=Form::input('smtp_password', 'Text', [
                    'label' => 'mailer_smtp_password',
                ]);?>
            </div>
        </div>

        <?=Form::input('send_also', 'Text', [
            'label' => 'mailer_send_also',
        ]);?>

        <?=Form::close();?>
    </div>
</div>

