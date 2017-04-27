<!DOCTYPE html>
<html>
<head>
    <title><?=$num == 1 ? 'Installer - Step 1' : t('installer_step_'.$num);?> - SyDES</title>
    <style>
        html, body, form{height:100%;}
        body{background:#fbfbfb;margin:0;padding:0;text-align:center;font:normal 16px/21px Arial;color:#fff;}
        form{margin:0 auto;width:320px;padding:0 20px;background:#2C313A;overflow:hidden;}
        .text{margin:250px 0 20px;font-size:30px;}
        .input{width:100%;padding:10px;border:none;box-sizing:border-box;}
        button{min-width:150px;padding:10px;background:#EA4848;border:none;font-size:16px;color:#fff;}
        button:hover{background:#F36767}
        .two{display:inline-block;width:50%;text-align:left;}
        .two.last{text-align:right;}
        .red{color:#EA4848;}
        ul{text-align:left;}
        label{cursor:pointer;}
        .group{margin-bottom:15px;}
    </style>
</head>
<body>

<form action="?step=<?=$num+1;?>" method="post">
    <?=$step;?>
    <?=csrf_field();?>
    <div class="two">&nbsp;</div><div class="two last"><button type="submit"><?=t('next');?></button></div>
</form>

</body>
</html>
