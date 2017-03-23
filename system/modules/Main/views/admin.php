<!DOCTYPE html>
<html lang="<?=$lang;?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?=$head;?>

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <link rel="icon" href="/favicon.ico">
</head>
<body>
    <div class="menu-wrapper">
        <?=$menu;?>
    </div>

    <div class="page-wrapper">
        <?php if ($title || $header_actions) { ?>
        <div class="header row">
            <div class="col-sm-4">
                <h1><?=$title;?></h1>
            </div>
            <div class="col-sm-8 text-right">
                <?php foreach ($header_actions as $action) {
                    echo H::a($action['title'], $action['href'], ['class' => 'btn btn-'.$action['style']]), ' ';
                } ?>
            </div>
        </div>
        <?php } ?>

        <div id="alerts"></div>

        <?php if ($form_url) { ?>
            <form class="main row" name="main" method="post" enctype="multipart/form-data" action="<?=$form_url;?>">
        <?php } else { ?>
            <div class="main row">
        <?php } ?>
                <?php if ($sidebar_left) { ?>
                    <div class="col-sm-3 col-lg-2 sidebar-left"><?=$sidebar_left;?></div><?php } ?>
                <?php if ($content) { ?>
                    <div class="col-sm-<?=$col_sm;?> col-lg-<?=$col_lg;?> content"><?=$content;?></div><?php } ?>
                <?php if ($sidebar_right) { ?>
                    <div class="col-sm-3 col-lg-2 sidebar-right"><?=$sidebar_right;?></div><?php } ?>
        <?php if (!$form_url) { ?>
            </div>
        <?php } else { ?>
            </form>
        <?php } ?>

        <div class="footer">
            <div class="col-sm-3">
                <?=$footer_left;?>
            </div>
            <div class="col-sm-6 text-center">
                <?=$footer_center;?>
            </div>
            <div class="col-sm-3 text-right">
                <a href="http://sydes.ru" data-toggle="tooltip"
                   title="<?=t('tip_license');?>">SyDES <?=SYDES_VERSION;?></a>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content"></div>
        </div>
    </div>

    <?=$footer;?>
</body>
</html>
