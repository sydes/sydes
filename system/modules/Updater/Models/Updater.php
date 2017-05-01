<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Updater\Models;

class Updater
{
    /**
     * @param string $type
     * @param string $name
     * @return bool
     */
    public function up($type, $name)
    {
        if ($this->haveUpdate($type, $name)) {
            $this->update($type, 'http://'.$name);
        }

        return true;
    }

    /**
     * @param string $type
     * @param string $name
     * @return bool
     */
    public function haveUpdate($type, $name)
    {
        return true;
    }

    protected function update($type, $url)
    {
        /*
         * скачать архив и его хеши
         * распаковать в папку temp
         * проверить соответсвие файлов хешам
         * если ядро, то
         *   сделать бекап и удалить system и vendor
         * если модуль, то
         *   сделать бекап и удалить папку модуля
         * иначе
         *   удалить все из папки, но не custom файлы
         * перенести из временной папки новую версию
         * если ядро или модуль, то
         *   запустить миграцию базы данных для каждого сайта
         *
         * throw Not updated. There is error:
         * */
    }
}
