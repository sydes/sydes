<?php

namespace Sydes\Settings;

interface DriverInterface
{
    public function get($entity);
    public function set($entity, $data);
}
