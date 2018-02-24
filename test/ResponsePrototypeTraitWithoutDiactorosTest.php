<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\Expressive\Authentication;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use ReflectionMethod;
use ReflectionObject;
use Zend\Expressive\Authentication\Exception\InvalidConfigException;
use Zend\Expressive\Authentication\ResponsePrototypeTrait;

class ResponsePrototypeTraitWithoutDiactorosTest extends TestCase
{
    /** @var ContainerInterface */
    private $container;

    /** @var object */
    private $class;

    /** @var ReflectionMethod */
    private $method;

    /** @var array */
    private $autoloadFunctions = [];

    protected function setUp()
    {
        class_exists(InvalidConfigException::class);
        interface_exists(ResponseInterface::class);

        $this->container = new class () implements ContainerInterface
        {
            public function get($id)
            {
                return null;
            }

            public function has($id)
            {
                return false;
            }
        };

        $this->class = new class () {
            use ResponsePrototypeTrait;
        };

        $r = new ReflectionObject($this->class);
        $this->method = $r->getMethod('getResponsePrototype');
        $this->method->setAccessible(true);

        $this->autoloadFunctions = spl_autoload_functions();
        foreach ($this->autoloadFunctions as $autoloader) {
            spl_autoload_unregister($autoloader);
        }
    }

    private function reloadAutoloaders()
    {
        foreach ($this->autoloadFunctions as $autoloader) {
            spl_autoload_register($autoloader);
        }
    }

    public function testRaisesAnExceptionIfDiactorosIsNotLoaded()
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('zendframework/zend-diactoros');

        try {
            $this->method->invoke($this->class, $this->container);
        } finally {
            $this->reloadAutoloaders();
        }
    }
}
