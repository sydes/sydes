<div class="card box">
    <div class="card-block">
        <?=Form::open(['method' => 'put', 'url' => '/admin/profile', 'form' => 'main']);?>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label><?=t('new_username');?></label>
                    <input type="text" name="newusername" class="form-control">
                </div>
                <div class="form-group">
                    <label><?=t('new_email');?></label>
                    <input type="email" name="newemail" class="form-control">
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label><?=t('new_password');?></label>
                    <input type="text" name="newpassword" class="form-control">
                </div>
                <div class="form-group">
                    <label><?=t('new_mastercode');?></label>
                    <input type="text" name="newmastercode" class="form-control">
                </div>
                <div class="form-group">
                    <label><?=t('enable_autologin');?></label>
                    <?=H::yesNo('autoLogin', $autoLogin);?>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-6 offset-sm-3 form-group">
                <label><?=t('mastercode');?></label>
                <input type="password" name="mastercode" class="form-control" required>
            </div>
        </div>
        <?=Form::close();?>
    </div>
</div>
