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
    <div class="card-body">
    <h5><?=t('modules_'.$groupName);?></h5>
    <table class="table table-hover table-sm">
        <tbody>
    <?php foreach ($group as $name => $module) { ?>
            <tr>
                <td><?=$translated[$name]['name'] ?: $module->get('name');?></td>
                <td><?=$module->get('version', ' ');?></td>
                <td><?=$translated[$name]['description'] ?: $module->get('description');?></td>
                <td class="text-right">

                    <?php if ($groupName == 'not_installed') {
                        echo H::dropdown([[
                            'label' => t('install'),
                            'url' => '/admin/module/'.$name,
                            'attr' => ['size' => 'sm', 'data-method' => 'post'],
                        ], [
                            'label' => t('delete_module'),
                            'url' => '/admin/module/'.$name.'/delete',
                            'attr' => ['data-method' => 'delete']
                        ]]);

                    } elseif ($groupName == 'default') {
                        if ($module->has('settings')) {
                            echo H::a(t('settings'), '/admin/'.$name.'/settings', [
                                'button' => 'secondary',
                                'size' => 'sm',
                            ]);
                        }
                    } else {
                        if ($module->has('settings')) {
                            echo H::dropdown([[
                                    'label' => t('settings'),
                                    'url' => '/admin/'.$name.'/settings',
                                    'attr' => ['size' => 'sm'],
                                ], [
                                    'label' => t('uninstall'),
                                    'url' => '/admin/module/'.$name,
                                    'attr' => ['data-method' => 'delete']
                                ]]);
                        } else {
                            echo H::a(t('uninstall'), '/admin/module/'.$name, [
                                'button' => 'secondary',
                                'size' => 'sm',
                                'data-method' => 'delete',
                            ]);
                        }
                    } ?>

                </td>
            </tr>
    <?php } ?>
        </tbody>
    </table>
    </div>
</div>
<?php } ?>
