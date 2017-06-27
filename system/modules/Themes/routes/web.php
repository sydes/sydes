<?php
use Sydes\router\Route;

$r->addGroup('/admin/theme', function (Route $r) {
    $r->get('s', 'Themes@index');
    $r->get('s/add', 'Themes@add');

    $r->get('/{name:[a-z-]+}', 'Themes@view');
    $r->post('/{name:[a-z-]+}', 'Themes@activate');
    $r->delete('/{name:[a-z-]+}', 'Themes@delete');

    $r->get('/layout/{name:[a-z-]+}', 'Themes/Layouts@edit');
    $r->post('/layout/{name:[a-z-]+}', 'Themes/Layouts@save');
});
