<div class="card box">
    <div class="card-block">
        <?=Form::fromArray($data, ['method' => 'put', 'url' => '/admin/profile', 'form' => 'main']);?>
        <div class="row">
            <div class="col-sm-6">
                <?=H::formGroup(t('username'), Form::field('Text', 'username', null, ['required' => true]));?>
                <?=H::formGroup(t('email'), Form::field('Email', 'email', null, ['required' => true]));?>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <?=H::formGroup(t('enable_autologin'), Form::field('YesNo', 'autoLogin'));?>
                </div>
            </div>
        </div>
        <?=H::submitButton(t('save'), ['button' => 'primary']);?>
        <?=Form::close();?>
    </div>
</div>

<div class="card box">
    <div class="card-block">
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
