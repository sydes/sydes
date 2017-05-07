<!DOCTYPE html>
<html>
<head>
    <title>Log in to SyDES</title>
    <style>
        html, body, form{height:100%;}
        body{background:#fbfbfb;margin:0;padding:0;text-align:center;font:normal 14px/20px Arial;color:#fff;}
        form{margin:0 auto;width:320px;padding:250px 20px 0;background:#2C313A;overflow:hidden;}
        .text{margin:0 0 20px;font-size:30px;}
        .input{width:100%;padding:10px;margin-bottom:10px;border:none;box-sizing:border-box;}
        button{min-width:150px;padding:10px;background:#EA4848;cursor:pointer;border:none;font-size:16px;color:#fff;}
        button:hover{background:#F36767}
        .two{display:inline-block;width:50%;text-align:left;}
        .two.last{text-align:right;}
        .red{color:#EA4848;}
        ul{text-align:left;}
        label{cursor:pointer;}
        .forgot{margin-top:30px;}
        a{color:#fff;text-decoration:none;}
    </style>
</head>
<body>
<!-- you shall not pass -->
<form action="<?=$url;?>" method="post">
    <?=$form;?>
    <?=csrf_field();?>
</form>

</body>
</html>
