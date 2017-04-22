<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Sydes;

use Pimple\Container as PimpleContainer;

class Container extends PimpleContainer
{
    /** @var array */
    private $defaultSettings = [
        'cacheRouter'   => true,
        'debugLevel' => 0,
        'checkUpdates'  => true,
    ];

    /** @var \Pimple\Container */
    protected static $container = null;

    /**
     * Create new container
     *
     * @param array $values The parameters or objects.
     */
    public function __construct(array $values = [])
    {
        parent::__construct($values);

        $userSettings = ifsetor($values['settings'], []);
        $this->registerDefaults($userSettings);
    }

    /**
     * @return \Pimple\Container
     */
    public static function getContainer()
    {
        return static::$container;
    }

    public static function setContainer($container)
    {
        self::$container = $container;
    }

    /**
     * This function registers the default services and handlers that SyDES needs to work.
     *
     * @param $settings
     * @return void
     */
    private function registerDefaults($settings)
    {
        $this['settings'] = array_merge($this->defaultSettings, $settings);
        $this['section'] = 'base';

        (new DefaultServicesProvider)->register($this);
        (new ExceptionHandlersProvider)->register($this);
    }
}
