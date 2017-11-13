<div class="card box">
    <div class="card-body">
        <?=Form::fromArray($data, ['method' => 'put', 'url' => '/admin/profile', 'form' => 'main']);?>
        <div class="row">
            <div class="col-sm-6">
                <?=Form::input('username', 'Text', [
                    'required' => true,
                    'label' => 'username',
                ]);?>
                <?=Form::input('email', 'Email', [
                    'required' => true,
                    'label' => 'email',
                ]);?>
            </div>
            <div class="col-sm-6">
                <?=Form::input('autoLogin', 'YesNo', [
                    'label' => 'enable_autologin',
                ]);?>
            </div>
        </div>
        <?=H::submitButton(t('save'), ['button' => 'primary']);?>
        <?=Form::close();?>
    </div>
</div>

<div class="card box">
    <div class="card-body">
        <?=Form::open(['method' => 'put', 'url' => '/admin/profile/pass', 'form' => 'pass']);?>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label><?=t('password');?></label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label><?=t('password_confirmation');?></label>
                    <input type="password" name="password2" class="form-control" required>
                </div>
                <div class="form-group">
                    <label><?=t('current_password');?></label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
            </div>
        </div>
        <?=H::submitButton(t('save'), ['button' => 'primary']);?>
        <?=Form::close();?>
    </div>
</div>
