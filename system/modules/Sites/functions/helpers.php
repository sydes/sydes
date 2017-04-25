<?php

function siteRenderStatus($status)
{
    if ($status) {
        $text = 'works';
        $class = 'success';
    } else {
        $text = 'not_works';
        $class = 'default';
    }

    return '<span class="badge badge-'.$class.'">'.t($text).'</span>';
}
