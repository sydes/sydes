<?php

namespace Sydes\View;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ViewServiceProvider implements ServiceProviderInterface
{
    public function register(Container $c)
    {
        $this->registerFactory($c);
        $this->registerEngineResolver($c);
    }

    public function registerFactory(Container $c)
    {
        $c['view'] = function ($c) {
            return new Factory($c['view.engine.resolver'], $c['event']);
        };
    }

    public function registerEngineResolver(Container $c)
    {
        $c['view.engine.resolver'] = function () {
            $resolver = new Engines\EngineResolver;

            $resolver->register('file', function () {
                return new Engines\FileEngine;
            });

            $resolver->register('php', function () {
                return new Engines\PhpEngine;
            });

            return $resolver;
        };
    }
}
