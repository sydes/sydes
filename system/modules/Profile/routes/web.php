<?php
$r->get('/admin/profile', 'Profile@edit');
$r->put('/admin/profile', 'Profile@update');
$r->put('/admin/profile/pass', 'Profile@updatePassword');
