<div id="toolbar">

    <a href="/admin"><?=t('admin');?></a>

    <a href="/admin/cache/clear-all&return=<?=$request_uri;?>"><?=t('clear_cache');?></a>

    <span class="divider"></span>

    <?php foreach ($menu as $item) { ?>
        <?php if ($item['link']) { ?>
            <a href="<?=$item['link'];?>"><?=$item['title'];?></a>
        <?php } else { ?>
            <div class="btn-group">
                <button type="button" class="btn btn-sm dropdown-toggle" data-toggle="dropdown"><?=$item['title'] ?>
                    <span class="caret"></span></button>
                <ul class="dropdown-menu dropdown-menu">
                    <?php foreach ($item['children'] as $child) { ?>
                        <li><?=$child;?></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
    <?php } ?>

    <a href="/logout" class="pull-right"><?=t('exit');?></a>
</div>

<script>
    $(document).ready(function () {
        $('.block-edit').on('click', function () {
            if ($(this).data('module') == 'iblock') {
                location.href = '/admin/iblocks/edit&id='+$(this).data('item')
            } else if ($(this).data('module') == 'config') {
                location.href = '/admin/config#'+$(this).data('item')
            }
        });
        $('.block-template').on('click', function () {
            location.href = '/admin?route=templates/file/edit&tpl=<?=$theme;?>&file=iblock/' +
                $(this).data('item')+'/'+$(this).data('template')+'.php'
        });
        $('body').addClass('with-toolbar');
    })
</script>
