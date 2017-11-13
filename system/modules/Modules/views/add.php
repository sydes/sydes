<div class="card box">
    <div class="card-body">
        <?=Form::open($options);?>
        <div class="row">
            <div class="col-sm-5">
                <?=H::formGroup(t('module_upload_file'), H::fileInput('file'));?>
            </div>
            <div class="col-sm-2 text-center">
                <?=t('or');?>
            </div>
            <div class="col-sm-5">
                <?=H::formGroup(t('module_upload_url'), H::urlInput('url', ''));?>
            </div>
        </div>
        <div class="text-center">
            <?=H::submitButton(t('upload'), ['button' => 'primary']);?>
            <label style="margin-left: 15px;"><?=H::checkbox('install'),' ',t('and_install');?></label>
        </div>
        <?=Form::close();?>
    </div>
</div>
