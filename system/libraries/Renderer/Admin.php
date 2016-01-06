<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace App\Renderer;

class Admin
{

    /**
     * @param \App\Document $document
     * @return string
     */
    public function render(\App\Document $document)
    {
        //TODO render doc or ajax response
        return json_encode($document);
    }

}
