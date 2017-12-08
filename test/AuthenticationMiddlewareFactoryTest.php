<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\Expressive\Authentication;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Authentication\AuthenticationInterface;
use Zend\Expressive\Authentication\AuthenticationMiddleware;
use Zend\Expressive\Authentication\AuthenticationMiddlewareFactory;

class AuthenticationMiddlewareFactoryTest extends TestCase
{
    protected $authentication;
    protected $request;

    public function setUp()
    {
        $this->authentication = $this->prophesize(AuthenticationInterface::class);
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->factory = new AuthenticationMiddlewareFactory();
    }

    /**
     * @expectedException Zend\Expressive\Authentication\Exception\InvalidConfigException
     */
    public function testInvokeWithNoAuthenticationService()
    {
        $middleware = ($this->factory)($this->container->reveal());
    }

    public function testInvokeWithAuthenticationService()
    {
        $this->container->has(AuthenticationInterface::class)
                        ->willReturn(true);
        $this->container->get(AuthenticationInterface::class)
                        ->willReturn($this->authentication->reveal());

        $middleware = ($this->factory)($this->container->reveal());
        $this->assertInstanceOf(AuthenticationMiddleware::class, $middleware);
    }
}
