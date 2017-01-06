<!DOCTYPE html>
<html lang="<?=$lang;?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?=$head;?>
    <link href="/system/assets/css/structure.css" rel="stylesheet" media="screen">
    <link href="/system/assets/css/skin.<?=$skin;?>.css" rel="stylesheet" media="screen" id="skin">

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <link rel="icon" href="/favicon.png">
</head>
<body>
    <div class="admin-bar">
        <div class="admin-bar-left">
            <a href="<?=$site_url;?>"><?=$site_name;?></a>
        </div>

        <div class="admin-bar-right">
            <div class="admin-bar-block">
                <?=H::dropdown('Контент: RU', [
                    ['title' => 'Контент: ru', 'link' => '/admin/pages?lang=ru'],
                    ['title' => 'Контент: en', 'link' => '/admin/pages?lang=en'],
                ], 'right');?>
            </div>

            <div class="admin-bar-block">
                <?=H::dropdown(app('user')->username, [
                    ['title' => t('profile'), 'link' => '/admin/profile'],
                    ['html' => '<a href="/logout" onclick="event.preventDefault();'.
                        'document.getElementById(\'logout-form\').submit();">'.t('logout').'</a>'.
                        '<form id="logout-form" action="/logout" method="POST" style="display: none;">'.
                        csrf_field().'</form>'],
                ], 'right');?>
            </div>
        </div>
    </div>

    <div class="menu-wrapper">
        <?=$menu;?>
    </div>

    <div id="main" class="container-fluid">
        <div id="alerts"></div>

        <div id="workarea" class="row">
            <?php if ($form_url){ ?><form name="main-form" method="post" enctype="multipart/form-data" action="<?=$form_url;?>"><?php } ?>
                <?php if ($sidebar_left) { ?>
                    <div class="col-sm-3 col-lg-2 sidebar-left"><?=$sidebar_left;?></div><?php } ?>
                <?php if ($content) { ?>
                    <div class="col-sm-<?=$col_sm;?> col-lg-<?=$col_lg;?> content"><?=$content;?></div><?php } ?>
                <?php if ($sidebar_right) { ?>
                    <div class="col-sm-3 col-lg-2 sidebar-right"><?=$sidebar_right;?></div><?php } ?>
            <?php if ($form_url){ ?></form><?php } ?>
        </div>

        <div id="footer" class="row">
            <div class="col-sm-3">
                <?=$footer_left;?>
            </div>
            <div class="col-sm-6 text-center">
                <?=$footer_center;?>
            </div>
            <div class="col-sm-3 text-right">
                <a href="http://sydes.ru" data-toggle="tooltip"
                   title="<?=t('tip_license');?>">SyDES <?=VERSION;?></a>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content"></div>
        </div>
    </div>

    <?=$footer;?>
    <script src="/system/assets/js/utils.min.js"></script>
    <script src="/system/assets/js/main.js"></script>
</body>
</html>
