<div><?=t('password_reset_tip');?></div>
<br>
<div><input class="input" type="password" name="password" placeholder="<?=t('password');?>" required></div>
<div><input class="input" type="password" name="password2" placeholder="<?=t('password_confirmation');?>" required></div>
<input type="hidden" name="token" value="<?=$token;?>">
<div><button type="submit"><?=t('save');?></button></div>
