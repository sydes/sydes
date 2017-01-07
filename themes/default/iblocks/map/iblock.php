<?php
if (!$this->config('map_show')) {
    return;
}
if ($this->config('map_type')) {
    $args['template'] = $this->config('map_type');
}
