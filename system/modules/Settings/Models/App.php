<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Module\Settings\Models;

class App
{
    protected $storage;
    protected $config;

    public function __construct()
    {
        $this->storage = DIR_STORAGE.'/app.php';
        $this->config = file_exists($this->storage) ? include $this->storage : [];
    }

    /**
     * @return array
     */
    public function get()
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function save(array $config)
    {
        $this->config = $config;
        array2file($this->config, $this->storage);
    }

    /**
     * @return bool
     */
    public function isCreated()
    {
        return file_exists($this->storage);
    }

    /**
     * @param array $config
     * @return array
     */
    public function create(array $config)
    {
        $config = array_merge([
            'dateFormat' => 'd.m.Y',
            'timeFormat' => 'H:i',
            'mailer_useSmtp' => '0',
            'mailer_smtpHost' => '',
            'mailer_smtpPort' => '25',
            'mailer_smtpUser' => '',
            'mailer_smtpPassword' => '',
            'mailer_sendAlso' => '',
        ], $config);

        $this->save($config);

        return $config;
    }
}