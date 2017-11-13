<?=Form::fromArray($data, $options);?>

    <div class="card box">
        <div class="card-body">
            <h4 class="card-title"><?=t('date_and_time');?></h4>
            <div class="row">
                <div class="col-sm-6">
                    <?=Form::input('timeZone', 'List', [
                        'required' => true,
                        'label' => 'admin_time_zone',
                        'choices' => $timeZones,
                    ]);?>
                </div>
                <div class="col-sm-6">

                </div>
                <div class="col-sm-6">
                    <?=Form::input('dateFormat', 'Text', [
                        'required' => true,
                        'label' => 'admin_date_format',
                        'attr' => [
                            'suffix' => date($data['dateFormat'])
                        ],
                    ]);?>
                </div>
                <div class="col-sm-6">
                    <?=Form::input('timeFormat', 'Text', [
                        'required' => true,
                        'label' => 'admin_time_format',
                        'attr' => [
                                'suffix' => date($data['timeFormat'])
                        ],
                    ]);?>
                </div>

            </div>
        </div>
    </div>

    <div class="card box">
        <div class="card-body">
            <h4 class="card-title"><?=t('language');?></h4>
            <div class="row">
                <div class="col-sm-6">
                    <?=Form::input('adminLanguage', 'List', [
                        'label' => 'admin_language',
                        'choices' => $translations,
                    ]);?>
                </div>
            </div>
        </div>
    </div>
<?=Form::close();?>



