<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Fields;

use Sydes\Route;

class Controller
{
    public static function routes(Route $r)
    {
        $r->get('/admin/fields/settings', 'Fields@settings');
    }

    public function settings()
    {
        $d = document([
            'title' => 'List of available fields',
            'content' => 'here',
        ]);

        return $d;
    }
}

/*
 * TODO валидатор должен вернуть текст конкретной ошибки в поле, или алертом, но пометить поле, где ошиблись
 * или не валидатор, в форма сама
 * дефолтное значение в settings а еще https://www.youtube.com/watch?v=HORfQdfPyvs и битрикс
 * Заполнять поля автоматом, если вернули с ошибкой old() в ларе
 */