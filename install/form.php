<!DOCTYPE html>
<html>
    <head>
        <title>SignUp in SyDES</title>
        <link href="/system/assets/css/signin.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <form action="" method="post">
            <div class="text">S<span class="red">y</span>DES</div>
            <?php if (!empty($result['errors'][0])) { ?>
                <?= $result['errors'][0]; ?>
            <?php } elseif (!empty($result['errors'][1])) { ?>
                <?= $result['errors'][1]; ?>
                <div><button type="submit">Refresh page</button></div>
            <?php } else { ?>
                <div><input type="text" name="username" placeholder="Username" required></div>
                <div><input type="password" name="password" placeholder="Password" required></div>
                <div><input type="text" name="mastercode" placeholder="Master code" required></div>
                <div><input type="email" name="email" placeholder="Email" required></div>
                <div><?= HTML::select('language', app('request')->getPreferredLanguage(array_keys($result['langs'])), $result['langs']); ?></div>
                <div class="two">&nbsp;</div>
                <div class="two last"><button type="submit">Create account</button></div>
                <input type="hidden" id="time_zone" name="time_zone">
            <?php } ?>
        </form>
        <script>
            document.getElementById('time_zone').value = (new Date()).getTimezoneOffset() / 60;
        </script>
    </body>
</html>