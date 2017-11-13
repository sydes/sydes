<div class="current-theme">
    <img class="screenshot" src="<?=$current['screenshot'];?>" alt="<?=$current['name'];?>">
    <div class="data">
        <div class="h3"><?=$current['name'];?> <small>v<?=$current['version'];?></small></div>
        <div><?=themeRenderAuthors($current['authors']);?></div>
        <p><?=$current['description'];?></p>
    </div>
</div>
<div class="clearfix"></div>

<div class="themes">
    <?php foreach ($themes as $themeName => $item) { ?>
        <div class="item">
            <div class="card">
                <a href="/admin/theme/<?=$themeName;?>">
                    <img class="card-img-top" src="<?=$item['screenshot'];?>" alt="<?=$item['name'];?>">
                </a>
                <div class="card-body">
                    <h5 class="card-title"><?=$item['name'];?> <small>v<?=$item['version'];?></small></h5>
                    <div class="authors"><?=themeRenderAuthors($item['authors'], 2);?></div>
                    <div>
                        <a href="/admin/theme/<?=$themeName;?>" data-method="delete"
                           class="btn btn-outline-danger btn-sm pull-right">
                            <?=t('delete');?>
                        </a>
                        <a href="/admin/theme/<?=$themeName;?>" data-method="post" class="btn btn-success btn-sm">
                            <?=t('activate');?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php }?>
</div>
