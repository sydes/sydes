<div class="text"><?=t('your_account');?></div>

<div class="group"><input class="input" type="text" name="username" placeholder="<?=t('username');?>" required></div>
<div class="group"><input class="input" type="password" name="password" placeholder="<?=t('password');?>" required></div>
<div class="group"><input class="input" type="email" name="email" placeholder="<?=t('email');?>" required></div>

<input type="hidden" name="time_zone">
<input type="hidden" name="locale" value="<?=$locale;?>">

<script>
    document.getElementsByName('time_zone')[0].value = (new Date()).getTimezoneOffset() / 60;
</script>
