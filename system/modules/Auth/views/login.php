<div class="text">S<span class="red">y</span>DES</div>
<?php if (!empty($errors)) { ?>
    <?=$errors;?>
    <div><a href=".">Refresh page</a></div>
<?php } else { ?>
    <div><input class="input" type="text" name="username" placeholder="<?=t('username');?>" required></div>
    <div><input class="input" type="password" name="password" placeholder="<?=t('password');?>" required></div>

    <div class="two">
        <?php if ($autoLogin){?>
            <label><input type="checkbox" name="remember"> <?=t('remember_me')?></label>
        <?php } ?>
    </div><div class="two last"><button type="submit"><?=t('login');?></button></div>

    <div class="forgot"><a href="/password/reset"><?=t('forgot_password');?></a></div>
<?php } ?>
