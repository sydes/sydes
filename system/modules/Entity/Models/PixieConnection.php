<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

namespace Module\Entity\Models;

use Pixie\Connection;
use Sydes\Database;
use Viocon\Container;

class PixieConnection extends Connection
{
    public function __construct(Database $db)
    {
        $this->adapter = $db->getAttribute(\PDO::ATTR_DRIVER_NAME);
        $this->setPdoInstance($db->connection());
        $this->container = new Container();
        $this->eventHandler = $this->container->build('Pixie\EventHandler');
    }
}
