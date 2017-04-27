<!DOCTYPE html>
<html>
<head>
    <title><?=$title;?> SyDES</title>
    <style>
        html, body, form{height:100%;}
        body{background:#fbfbfb;margin:0;padding:0;text-align:center;font:normal 14px/20px Arial;color:#fff;}
        form{margin:0 auto;width:320px;padding:0 20px;background:#2C313A;overflow:hidden;}
        .text{margin:250px 0 20px;font-size:30px;}
        .input{width:100%;padding:10px;margin-bottom:10px;border:none;box-sizing:border-box;}
        button{min-width:150px;padding:10px;background:#EA4848;cursor:pointer;border:none;font-size:16px;color:#fff;}
        button:hover{background:#F36767}
        .two{display:inline-block;width:50%;text-align:left;}
        .two.last{text-align:right;}
        .red{color:#EA4848;}
        ul{text-align:left;}
        label{cursor:pointer;}
    </style>
</head>
<body>
<!-- you shall not pass -->
<form action="" method="post">
    <div class="text">S<span class="red">y</span>DES</div>
    <?php if (!empty($errors)) { ?>
        <?=$errors;?>
        <div><a href=".">Refresh page</a></div>
    <?php } else { ?>
        <div><input class="input" type="text" name="username" placeholder="<?=t('username');?>" required></div>
        <div><input class="input" type="password" name="password" placeholder="<?=t('password');?>" required></div>

        <div class="two">
            <?php if ($autoLogin){?>
                <label><input type="checkbox" name="remember"> <?=t('remember_me')?></label>
            <?php } ?>
        </div><div class="two last"><button type="submit"><?=t('login');?></button></div>

    <?php } ?>
    <?=csrf_field();?>
</form>

</body>
</html>
