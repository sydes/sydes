<?php
$r->get('/admin/modules', 'Modules@index');
$r->post('/admin/module/{name:[a-z-]+}', 'Modules@installModule');
$r->delete('/admin/module/{name:[a-z-]+}', 'Modules@uninstallModule');
$r->delete('/admin/module/{name:[a-z-]+}/delete', 'Modules@deleteModule');
$r->get('/admin/modules/add', 'Modules@add');
$r->post('/admin/modules/add', 'Modules@upload');
$r->get('/admin/modules/updates', 'Modules@updates');
