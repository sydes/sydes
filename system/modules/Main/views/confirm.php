<form action="" method="post" class="confirmation card">
    <p class="text-center"><?=$message;?></p>
    <div>
        <button type="submit" class="btn btn-danger"><?=t('yes');?></button>
        <a href="<?=$return_url;?>" class="btn btn-success pull-right"><?=t('no');?></a>
    </div>
    <input type="hidden" name="confirmed" value="1">
</form>
