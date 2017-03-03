<div class="current-theme">
    <img class="screenshot" src="<?=$current['screenshot'];?>" alt="<?=$current['name'];?>">
    <div class="data">
        <div class="h3"><?=$current['name'];?> <small>v<?=$current['version'];?></small></div>
        <div>
            <?php if (empty($current['authors'])) {
                echo 'Unknown';
            } else {
                foreach ($current['authors'] as $author) {
                    echo H::link($author['homepage'], $author['name'], ['target' => '_blank']);
                }
            }?>
        </div>
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
                <div class="card-block">
                    <h5 class="card-title"><?=$item['name'];?> <small>v<?=$item['version'];?></small></h5>
                    <div class="authors"><?=t('by')?>
                        <?php if (empty($item['authors'])) {
                            echo 'Unknown';
                        } else {
                            foreach ($item['authors'] as $author) {
                                echo H::link($author['homepage'], $author['name'], ['target' => '_blank']);
                            }
                        }?>
                    </div>
                    <div>
                        <form action="/admin/theme/<?=$themeName;?>" method="post" class="pull-right">
                            <?=method_field('delete');?>
                            <?=csrf_field();?>
                            <button type="submit" class="btn btn-outline-danger btn-sm"><?=t('delete');?></button>
                        </form>
                        <form action="/admin/theme/<?=$themeName;?>/activate" method="post">
                            <?=csrf_field();?>
                            <button type="submit" class="btn btn-success btn-sm"><?=t('activate');?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php }?>
</div>
