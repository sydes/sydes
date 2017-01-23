<?php return [
    ['GET', '/login', 'User@loginForm'],
    ['POST', '/login', 'User@login'],
    ['POST', '/logout', 'User@logout'],
    ['GET', '/install', 'Util@signUpForm'],
    ['POST', '/install', 'Util@signUp'],
    ['GET', '/admin/sites/add', 'Site@addForm'],
    ['POST', '/admin/sites/add', 'Site@add'],

    ['GET', '/robots.txt', 'Util@robots'],
    ['GET', '/sitemap.xml', 'Util@sitemap'],
];

// TODO оставить только базовые
