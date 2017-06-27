<?php
$r->resource('sites', 'Sites');
$r->get('/admin/sites/go/{id:\d+}', 'Sites@go');
