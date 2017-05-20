<?php

namespace Sydes\View\Engines;

class FileEngine implements EngineInterface
{
    /**
     * {@inheritdoc}
     */
    public function get($path, array $data = [])
    {
        return file_get_contents($path);
    }
}
