<div class="card box">
    <div class="card-body">
    <h5><?=t('sites');?></h5>
    <table class="table table-hover table-sm">
        <tbody>
    <?php foreach ($sites as $id => $item) { ?>
            <tr>
                <td><a href="/admin/sites/go/<?=$id;?>"><?=$item->get('name');?></a></td>
                <td><?=implode(', ',$item->get('domains'));?></td>
                <td><?=implode(', ',$item->get('locales'));?></td>
                <td><?=$item->get('theme');?></td>
                <td><?=siteRenderStatus($item->get('work'));?></td>
                <td class="text-right">

                    <?php echo H::dropdown([[
                        'label' => t('edit'),
                        'url' => '/admin/sites/'.$id,
                        'attr' => ['size' => 'sm'],
                    ], [
                        'label' => t('delete'),
                        'url' => '/admin/sites/'.$id,
                        'attr' => ['data-method' => 'delete']
                    ]]); ?>

                </td>
            </tr>
    <?php } ?>
        </tbody>
    </table>
    </div>
</div>
