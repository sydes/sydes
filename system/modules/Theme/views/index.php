<div class="themes">
<?php foreach ($themes as $themeName => $item) { ?>
    <div class="item">
        <a href="/admin/theme/<?=$themeName;?>">
            <img src="<?=$item['screenshot'];?>" alt="<?=$item['name'];?>">
        </a>
        <div class="h4"><?=$item['name'];?> <small>v<?=$item['version'];?></small></div>
        <div class="authors">By
        <?php foreach ($item['authors'] as $author) { ?>
            <a href="<?=$author['homepage'];?>" target="_blank"><?=$author['name'];?></a>
        <?php } ?>
        </div>
        <div>
            <a href="#" class="btn btn-default btn-xs"><?=t('activate');?></a>
            <a href="#" class="btn btn-danger btn-xs pull-right"><?=t('delete');?></a>
        </div>
    </div>
<?php } ?>
</div>
