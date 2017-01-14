<?php return [
    ['GET', '/page/{id:[0-9]+}', 'Test@page'],
    ['GET', '/notfound', 'Test@notFound'],
    ['GET', '/forbidden', 'Test@forbidden'],
    ['GET', '/ajax', 'Test@ajax'],
    ['GET', '/string.txt', 'Test@textString'],
    ['GET', '/export', 'Test@export'],
    ['GET', '/html', 'Test@html'],
    ['GET', '/nool', 'Test@nool'],
    ['GET', '/moved', 'Test@moved'],
    ['GET', '/update', 'Test@notifyAfterRedirect'],
    ['GET', '/store', 'Test@alertAfterRedirect'],
    ['GET', '/ajaxupdate', 'Test@ajaxNotify'],
    ['GET', '/ajaxstore', 'Test@ajaxAlert'],
    ['GET', '/refresh', 'Test@ajaxRefresh'],
    ['GET', '/refresh2', 'Test@refreshAndNotify'],
    ['GET', '/random', 'Test@random'],
    ['GET', '/', 'Test@index'],
    ['GET', '/admin', 'Test@adminMain'],
    ['GET', '/admin/pages', 'Test@adminPages'],
    ['GET', '/case', 'TestCase@index'],

    ['GET', '/sample', 'Sample@index'],

    ['GET', '/login', 'User@loginForm'],
    ['POST', '/login', 'User@login'],
    ['POST', '/logout', 'User@logout'], // TODO вернуть на пост, создать форму, как в ларавельке
    ['GET', '/install', 'Util@signUpForm'],
    ['POST', '/install', 'Util@signUp'],
    ['GET', '/admin/sites/add', 'Site@addForm'],
    ['POST', '/admin/sites/add', 'Site@add'],

    ['GET', '/robots.txt', 'Util@robots'],
    ['GET', '/sitemap.xml', 'Util@sitemap'],
];

// TODO оставить только базовые
