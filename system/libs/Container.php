<?php
/**
 * @link      https://github.com/sydes/sydes
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */
namespace Sydes;

use Pimple\Container as PimpleContainer;
use Psr\Container\ContainerInterface;
use Sydes\Exception\NotFoundException;

class Container extends PimpleContainer implements ContainerInterface
{
    /** @var \Pimple\Container */
    protected static $container = null;

    protected $namespaces;
    protected $edges = [];

    /**
     * Instantiate the container.
     *
     * @param array $values  The parameters of the object
     * @param array $options Associative array with optional "namespaces" key
     */
    public function __construct(array $values = [], array $options = [])
    {
        parent::__construct($values);

        $this->namespaces = ifsetor($options['namespaces'], []);
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
     * @param string $id
     * @param mixed  $data
     */
    public function set($id, $data)
    {
        $this[$id] = $data;
    }

    /**
     * {@inheritDoc}
     */
    public function get($id)
    {
        if (!isset($this[$id])) {
            throw new NotFoundException(sprintf('Identifier "%s" is not defined in container', $id));
        }

        return $this[$id];
    }

    /**
     * {@inheritDoc}
     */
    public function has($id)
    {
        return isset($this[$id]);
    }

    /**
     * @param array $namespaces
     */
    public function setNamespaces($namespaces)
    {
        $this->namespaces = $namespaces;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \RuntimeException if there is a circular dependency
     */
    public function offsetGet($id)
    {
        if (!isset($this[$id]) && $this->resolveClassName($id)) {
            return $this->make($id);
        }

        if (isset($this[$id])) {
            $this->checkDependencyCycle($id);
        }

        return parent::offsetGet($id);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($id)
    {
        parent::offsetUnset($id);

        $resolvedClass = $this->resolveClassName($id);
        foreach ([$id, $resolvedClass] as $key) {
            if (isset($this->edges[$key])) {
                unset($this->edges[$key]);
            }
        }
    }

    /**
     * @param callable $callable
     * @param array    $args
     * @return mixed
     */
    public function call(callable $callable, array $args = [])
    {
        $reflection = is_array($callable) ?
            new \ReflectionMethod($callable[0], $callable[1]) : new \ReflectionFunction($callable);

        $params = [];
        foreach ($reflection->getParameters() as $param) {
            if ($paramClassReflection = $param->getClass()) {
                $paramClass = $paramClassReflection->getName();
                $val = isset($this[$paramClass]) ? $this[$paramClass] : $this->make($paramClass);
            } elseif (array_key_exists($param->name, $args)) {
                $val = $args[$param->name];
            } elseif ($param->isOptional()) {
                $val = $param->getDefaultValue();
            } elseif (isset($this[$param->name])) {
                $val = $this[$param->name];
            } else {
                $val = null;
            }

            $params[$param->name] = $val;
        }

        return call_user_func_array($callable, $params);
    }

    /**
     * Manually create a new auto-resolved class instance and return it
     *
     * @param string $name Entry name or a class name
     * @param array  $args Optional parameters to use to build the entry.
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function make($name, array $args = [])
    {
        $origName = $name;
        if (!($name = $this->resolveClassName($name))) {
            throw new \InvalidArgumentException(t('error_class_not_found', ['class' => $origName]));
        }

        if (isset($this[$name])) {
            throw new \RuntimeException(t('error_class_not_found', ['class' => $name]));
        } elseif ($origName !== $name && isset($this[$origName])) {
            throw new \RuntimeException(t('error_class_not_found', ['class' => $origName]));
        }

        $self = $this;
        $this[$name] = function () use ($self, $name, $args) {
            return $self->instantiate($name, $args);
        };

        if ($origName !== $name) {
            $this[$origName] = function () use ($self, $name) {
                return $self[$name];
            };
        }

        return $this[$name];
    }

    /**
     * Create instance of class.
     *
     * @param string $className
     * @param array  $args
     *
     * @return object
     */
    public function instantiate($className, array $args = [])
    {
        $classReflection = new \ReflectionClass($className);
        if (!$classReflection->hasMethod('__construct')) {
            return new $className;
        }

        $this->edges[$className] = [];
        $args = $this->generateArgs($classReflection->getMethod('__construct'), $className, $args);

        return $classReflection->newInstanceArgs($args);
    }

    /**
     * Builds and returns signature
     *
     * @param \ReflectionMethod $methodReflection
     * @param string            $className
     * @param array             $args
     *
     * @return array
     */
    protected function generateArgs(\ReflectionMethod $methodReflection, $className, array $args = [])
    {
        $params = [];
        foreach ($methodReflection->getParameters() as $param) {
            if ($paramClassReflection = $param->getClass()) {
                $paramClass = $paramClassReflection->getName();

                $this->edges[$className][] = $paramClass;
                $this->checkDependencyCycle($paramClass);

                $val = isset($this[$paramClass]) ? $this[$paramClass] : $this->make($paramClass);
            } elseif (array_key_exists($param->name, $args)) {
                $val = $args[$param->name];
            } elseif ($param->isOptional()) {
                $val = $param->getDefaultValue();
            } elseif (isset($this[$param->name])) {
                $val = $this[$param->name];
            } else {
                $val = null;
            }

            $params[$param->name] = $val;
        }

        return $params;
    }

    /**
     * Checks if class exists
     *
     * @param string $name
     *
     * @return string|null
     */
    protected function resolveClassName($name)
    {
        if (class_exists($name)) {
            return $name;
        }

        foreach ($this->namespaces as $namespace) {
            $named = "$namespace\\$name";
            if (class_exists($named)) {
                return $named;
            }
        }

        return null;
    }

    /**
     * @param string $className
     *
     * @throws \RuntimeException if dependency cycle found
     */
    protected function checkDependencyCycle($className)
    {
        if ($cycle = $this->findCircularEdge($className)) {
            throw new \RuntimeException(t('error_found_circular_dependencies', [
                'cycle' => implode(' => ', $cycle).' => $className',
            ]));
        }
    }

    /**
     * Finds circular dependencies and returns them
     *
     * @param string $className
     * @param array  $circle
     *
     * @return array|null
     */
    protected function findCircularEdge($className, $circle = [])
    {
        if (!isset($this->edges[$className])) {
            return null;
        }

        if (in_array($className, $circle)) {
            return $circle;
        }

        $circle[] = $className;
        foreach ($this->edges[$className] as $edge) {
            if ($inner = $this->findCircularEdge($edge, $circle)) {
                return $inner;
            }
        }

        return null;
    }
}
