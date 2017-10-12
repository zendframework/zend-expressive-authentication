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
use Zend\Authentication\AuthenticationService;
use Zend\Expressive\Authentication\Adapter\ZendAuthentication;
use Zend\Expressive\Authentication\Adapter\ZendAuthenticationFactory;

class ZendAuthenticationFactoryTest extends TestCase
{
    protected function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->factory = new ZendAuthenticationFactory();
        $this->authService = $this->prophesize(AuthenticationService::class);
        $this->responsePrototype = $this->prophesize(ResponseInterface::class);
    }

    /**
     * @expectedException Zend\Expressive\Authentication\Exception\InvalidConfigException
     */
    public function testInvokeWithEmptyContainer()
    {
        $zendAuthentication = ($this->factory)($this->container->reveal());
    }

    /**
     * @expectedException Zend\Expressive\Authentication\Exception\InvalidConfigException
     */
    public function testInvokeWithContainerEmptyConfig()
    {
        $this->container->has(AuthenticationService::class)
                        ->willReturn(true);
        $this->container->get(AuthenticationService::class)
                        ->willReturn($this->authService->reveal());
        $this->container->has(ResponseInterface::class)
                        ->willReturn(true);
        $this->container->get(ResponseInterface::class)
                        ->willReturn($this->responsePrototype->reveal());
        $this->container->get('config')
                        ->willReturn([]);

        $zendAuthentication = ($this->factory)($this->container->reveal());
    }

    public function testInvokeWithContainerAndConfig()
    {
        $this->container->has(AuthenticationService::class)
                        ->willReturn(true);
        $this->container->get(AuthenticationService::class)
                        ->willReturn($this->authService->reveal());
        $this->container->has(ResponseInterface::class)
                        ->willReturn(true);
        $this->container->get(ResponseInterface::class)
                        ->willReturn($this->responsePrototype->reveal());
        $this->container->get('config')
                        ->willReturn([
                            'authentication' => ['redirect' => '/login']
                        ]);

        $zendAuthentication = ($this->factory)($this->container->reveal());
        $this->assertInstanceOf(ZendAuthentication::class, $zendAuthentication);
    }
}
