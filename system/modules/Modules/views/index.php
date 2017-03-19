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
                <td><?=t('module_'.snake_case($name, '-'));?></td>
                <td><?=$module->get('version', ' ');?></td>
                <td><?=$module->get('description');?></td>
                <td class="text-right">

                    <?php if ($groupName == 'not_installed') { ?>
                        <button type="button" class="btn btn-secondary btn-sm"><?=t('install_module');?></button>
                    <?php } elseif ($groupName == 'default') {
                        if ( $module->has('settings')) { ?>
                            <a href="/admin/<?=snake_case($name, '-');?>/settings" class="btn btn-secondary btn-sm"><?=t('configure_module');?></a>
                        <?php }
                    } else { ?>
                    <div class="btn-group">
                        <a href="/admin/<?=snake_case($name, '-');?>/settings" class="btn btn-secondary btn-sm"><?=t('configure_module');?></a>
                        <button type="button" class="btn btn-secondary btn-sm dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#"><?=t('uninstall_module');?></a>
                        </div>
                    </div>
                    <?php } ?>

                </td>
            </tr>

    <?php } ?>
        </tbody>
    </table>
</div>
<?php } ?>
