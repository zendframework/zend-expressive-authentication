<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */
namespace ZendTest\Expressive\Authentication;

use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Authentication\AuthenticationInterface;
use Zend\Expressive\Authentication\AuthenticationMiddleware;
use Zend\Expressive\Authentication\UserInterface;

class AuthenticationMiddlewareTest extends TestCase
{
    protected $authentication;
    protected $request;

    public function setUp()
    {
        $this->authentication = $this->prophesize(AuthenticationInterface::class);
        $this->request = $this->prophesize(ServerRequestInterface::class);
        $this->authenticatedUser = $this->prophesize(UserInterface::class);
        $this->handler = $this->prophesize(RequestHandlerInterface::class);
    }

    public function testConstructor()
    {
        $middleware = new AuthenticationMiddleware($this->authentication->reveal());
        $this->assertInstanceOf(AuthenticationMiddleware::class, $middleware);
        $this->assertInstanceOf(MiddlewareInterface::class, $middleware);
    }

    public function testProcessWithNoAuthenticatedUser()
    {
        $response = $this->prophesize(ResponseInterface::class);

        $this->authentication->authenticate($this->request->reveal())
                             ->willReturn(null);
        $this->authentication->unauthorizedResponse($this->request->reveal())
                             ->willReturn($response->reveal());

        $middleware = new AuthenticationMiddleware($this->authentication->reveal());
        $result = $middleware->process($this->request->reveal(), $this->handler->reveal());

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertEquals($response->reveal(), $result);
        $this->authentication->unauthorizedResponse($this->request->reveal())->shouldBeCalled();
    }

    public function testProcessWithAuthenticatedUser()
    {
        $response = $this->prophesize(ResponseInterface::class);

        $this->request->withAttribute(UserInterface::class, $this->authenticatedUser->reveal())
                      ->willReturn($this->request->reveal());
        $this->authentication->authenticate($this->request->reveal())
                             ->willReturn($this->authenticatedUser->reveal());
        $this->handler->handle($this->request->reveal())
                      ->willReturn($response->reveal());

        $middleware = new AuthenticationMiddleware($this->authentication->reveal());
        $result = $middleware->process($this->request->reveal(), $this->handler->reveal());

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertEquals($response->reveal(), $result);
        $this->handler->handle($this->request->reveal())->shouldBeCalled();
    }
}
