<?php
$all = [
    'installed' => $installed,
    'not_installed' => $uploaded,
    'default' => $default,
];
?>

<?php foreach ($all as $groupName => $group) {
    if (empty($group)) continue;
?>
<div class="card box">
    <h5><?=t('modules_'.$groupName);?></h5>
    <table class="table table-hover table-sm">
        <tbody>
    <?php foreach ($group as $name => $module) { ?>
            <tr>
                <td><?=$translated[$name]['name'] ?: $module->get('name');?></td>
                <td><?=$module->get('version', ' ');?></td>
                <td><?=$translated[$name]['description'] ?: $module->get('description');?></td>
                <td class="text-right">

                    <?php if ($groupName == 'not_installed') { ?>
                        <button type="button" class="btn btn-secondary btn-sm"><?=t('install');?></button>
                    <?php } elseif ($groupName == 'default') {
                        if ( $module->has('settings')) {
                            echo H::a(t('settings'), '/admin/'.$name.'/settings', ['class' => 'btn btn-secondary btn-sm']);
                        }
                    } else {
                        echo H::dropdown([[
                                'label' => t('settings'),
                                'url' => '/admin/'.$name.'/settings',
                                'attr' => ['size' => 'sm'],
                            ], [
                                'label' => t('uninstall'),
                                'url' => 'admin/modules/'.$name.'uninstall',
                            ]]);
                         } ?>

                </td>
            </tr>

    <?php } ?>
        </tbody>
    </table>
</div>
<?php } ?>
