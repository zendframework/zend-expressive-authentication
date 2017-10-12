<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */
namespace ZendTest\Expressive\Authentication\Adapter;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Expressive\Authentication\Adapter\BasicAccess;
use Zend\Expressive\Authentication\Adapter\BasicAccessFactory;
use Zend\Expressive\Authentication\UserRepositoryInterface;

class BasicAccessFactoryTest extends TestCase
{
    protected function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->factory = new BasicAccessFactory();
        $this->userRegister = $this->prophesize(UserRepositoryInterface::class);
        $this->responsePrototype = $this->prophesize(ResponseInterface::class);
    }

    /**
     * @expectedException Zend\Expressive\Authentication\Exception\InvalidConfigException
     */
    public function testInvokeWithEmptyContainer()
    {
        $basicAccess = ($this->factory)($this->container->reveal());
    }

    /**
     * @expectedException Zend\Expressive\Authentication\Exception\InvalidConfigException
     */
    public function testInvokeWithContainerEmptyConfig()
    {
        $this->container->has(UserRepositoryInterface::class)
                        ->willReturn(true);
        $this->container->get(UserRepositoryInterface::class)
                        ->willReturn($this->userRegister->reveal());
        $this->container->has(ResponseInterface::class)
                        ->willReturn(true);
        $this->container->get(ResponseInterface::class)
                        ->willReturn($this->responsePrototype->reveal());
        $this->container->get('config')
                        ->willReturn([]);

        $basicAccess = ($this->factory)($this->container->reveal());
    }

    public function testInvokeWithContainerAndConfig()
    {
        $this->container->has(UserRepositoryInterface::class)
                        ->willReturn(true);
        $this->container->get(UserRepositoryInterface::class)
                        ->willReturn($this->userRegister->reveal());
        $this->container->has(ResponseInterface::class)
                        ->willReturn(true);
        $this->container->get(ResponseInterface::class)
                        ->willReturn($this->responsePrototype->reveal());
        $this->container->get('config')
                        ->willReturn([
                            'authentication' => ['realm' => 'My page']
                        ]);

        $basicAccess = ($this->factory)($this->container->reveal());
        $this->assertInstanceOf(BasicAccess::class, $basicAccess);
    }
}
