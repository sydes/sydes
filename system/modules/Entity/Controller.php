<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Entity;

use Sydes\Contracts\Http\Request;

class Controller
{

    public function install()
    {
        model('Entity/Fields')->find();
    }

    public function index()
    {
        $d = document([
            'title' => 'List of available Entities',
            'content' => 'All Entities',
        ]);

        return $d;
    }

    public function storeTableSettings($key, Request $req)
    {
        settings('entity-tables')->set($key, $req->input('select', []))->save();

        return back();
    }
}

/*
 * TODO валидатор должен вернуть текст конкретной ошибки в поле, или алертом, но пометить поле, где ошиблись
 * или не валидатор, в форма сама
 * дефолтное значение в settings а еще https://www.youtube.com/watch?v=HORfQdfPyvs и битрикс
 * Заполнять поля автоматом, если вернули с ошибкой old() в ларе
 */