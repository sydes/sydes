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

<?=Form::close();?>
