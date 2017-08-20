<?php

namespace Sydes\Tests\Routing;

use PHPUnit\Framework\TestCase;
use Sydes\Routing\Route;

final class RoutingRouteTest extends TestCase
{
    public function testShortcuts()
    {
        $r = new DummyRouteCollector();

        $r->resource('test', 'Test');
        $r->settings('test', 'Test');
        $r->autoComplete('test', 'Test');
        $r->view('/view', 'module/view');
        $r->redirect('/from', '/to');

        $expected = [
            ['GET',    '/admin/test',          'Test@index'],
            ['GET',    '/admin/test/create',   'Test@create'],
            ['POST',   '/admin/test',          'Test@store'],
            ['GET',    '/admin/test/{id:\d+}', 'Test@edit'],
            ['PUT',    '/admin/test/{id:\d+}', 'Test@update'],
            ['DELETE', '/admin/test/{id:\d+}', 'Test@destroy'],

            ['GET', '/admin/test/settings', 'Test@settings'],
            ['PUT', '/admin/test/settings', 'Test@updateSettings'],

            ['GET', '/admin/test/suggest/{target}/{title}', 'Test@autoComplete'],

            ['GET', '/view', 'Main@view?view=module/view'],

            ['GET', '/from', 'Main@redirect?to=/to'],
        ];

        $this->assertSame($expected, $r->routes);
    }
}

class DummyRouteCollector extends Route
{
    public $routes = [];
    public function __construct(){}
    public function addRoute($method, $route, $handler)
    {
        $route = $this->currentGroupPrefix.$route;
        $this->routes[] = [$method, $route, $handler];
    }
}
