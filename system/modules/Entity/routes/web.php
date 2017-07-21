<?php
$r->get('/admin/entity', 'Entity@index');
$r->post('/admin/entity/table-settings/{key}', 'Entity@storeTableSettings');
