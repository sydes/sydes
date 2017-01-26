<?php
if (!$this->data('map_show')) {
    return;
}
if ($this->data('map_type')) {
    $args['template'] = $this->data('map_type');
}
