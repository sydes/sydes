<?php
$r->get('/auth/login', 'Auth@loginForm');
$r->post('/auth/login', 'Auth@login');
$r->post('/auth/logout', 'Auth@logout');

$r->get('/password/reset', 'Auth/Password@showForm');
$r->post('/password/email', 'Auth/Password@sendMail');
$r->get('/password/reset/{token}', 'Auth/Password@showResetForm');
$r->post('/password/reset', 'Auth/Password@reset');
