<div class="card box">
    <div class="card-block">
        <?=Form::fromArray($data, $options);?>

        <div class="row">
            <div class="col-sm-6">
                <?=Form::input('timeZone', 'Text', [
                    'required' => true,
                    'label' => 'app_time_zone',
                ]);?>
            </div>
            <div class="col-sm-6">

            </div>
            <div class="col-sm-6">
                <?=Form::input('dateFormat', 'Text', [
                    'required' => true,
                    'label' => 'app_date_format',
                ]);?>
            </div>
            <div class="col-sm-6">
                <?=Form::input('timeFormat', 'Text', [
                    'required' => true,
                    'label' => 'app_time_format',
                ]);?>
            </div>
            <div class="col-sm-6">
                <?=Form::input('locale', 'Text', [
                    'required' => true,
                    'label' => 'app_locale',
                ]);?>
            </div>
        </div>

        <?=Form::close();?>
    </div>
</div>


