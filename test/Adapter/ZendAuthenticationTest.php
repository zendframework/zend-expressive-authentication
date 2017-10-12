<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */
namespace ZendTest\Expressive\Authentication\Adapter;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Authentication\Adapter\AbstractAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use Zend\Expressive\Authentication\Adapter\ZendAuthentication;
use Zend\Expressive\Authentication\AuthenticationInterface;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Authentication\UserRepositoryInterface;

class ZendAuthenticationTest extends TestCase
{
    protected function setUp()
    {
        $this->request = $this->prophesize(ServerRequestInterface::class);
        $this->authService = $this->prophesize(AuthenticationService::class);
        $this->authenticatedUser = $this->prophesize(UserInterface::class);
        $this->responsePrototype = $this->prophesize(ResponseInterface::class);
    }

    public function testConstructor()
    {
        $zendAuthentication = new ZendAuthentication(
            $this->authService->reveal(),
            [],
            $this->responsePrototype->reveal()
        );
        $this->assertInstanceOf(AuthenticationInterface::class, $zendAuthentication);
    }

    public function testAuthenticateWithGetMethodAndIdentity()
    {
        $this->request->getMethod()->willReturn('GET');
        $this->authService->hasIdentity()->willReturn(true);
        $this->authService->getIdentity()->willReturn('foo');

        $zendAuthentication = new ZendAuthentication(
            $this->authService->reveal(),
            [],
            $this->responsePrototype->reveal()
        );
        $result = $zendAuthentication->authenticate($this->request->reveal());
        $this->assertInstanceOf(UserInterface::class, $result);
    }

    public function testAuthenticateWithGetMethodAndNoIdentity()
    {
        $this->request->getMethod()->willReturn('GET');
        $this->authService->hasIdentity()->willReturn(false);

        $zendAuthentication = new ZendAuthentication(
            $this->authService->reveal(),
            [],
            $this->responsePrototype->reveal()
        );
        $this->assertNull($zendAuthentication->authenticate($this->request->reveal()));
    }

    public function testAuthenticateWithPostMethodAndNoParams()
    {
        $this->request->getMethod()->willReturn('POST');
        $this->request->getParsedBody()->willReturn([]);

        $zendAuthentication = new ZendAuthentication(
            $this->authService->reveal(),
            [],
            $this->responsePrototype->reveal()
        );
        $this->assertNull($zendAuthentication->authenticate($this->request->reveal()));
    }

    public function testAuthenticateWithPostMethodAndNoValidCredential()
    {
        $this->request->getMethod()->willReturn('POST');
        $this->request->getParsedBody()->willReturn([
            'username' => 'foo',
            'password' => 'bar'
        ]);
        $adapter = $this->prophesize(AbstractAdapter::class);
        $adapter->setIdentity('foo')->willReturn(null);
        $adapter->setCredential('bar')->willReturn();

        $this->authService->getAdapter()
                          ->willReturn($adapter->reveal());
        $result = $this->prophesize(Result::class);
        $result->isValid()->willReturn(false);

        $this->authService->authenticate()
                          ->willReturn($result);

        $zendAuthentication = new ZendAuthentication(
            $this->authService->reveal(),
            [],
            $this->responsePrototype->reveal()
        );
        $this->assertNull($zendAuthentication->authenticate($this->request->reveal()));
    }

    public function testAuthenticateWithPostMethodAndValidCredential()
    {
        $this->request->getMethod()->willReturn('POST');
        $this->request->getParsedBody()->willReturn([
            'username' => 'foo',
            'password' => 'bar'
        ]);
        $adapter = $this->prophesize(AbstractAdapter::class);
        $adapter->setIdentity('foo')->willReturn(null);
        $adapter->setCredential('bar')->willReturn();

        $this->authService->getAdapter()
                          ->willReturn($adapter->reveal());
        $result = $this->prophesize(Result::class);
        $result->isValid()->willReturn(true);
        $result->getIdentity()->willReturn('foo');

        $this->authService->authenticate()
                          ->willReturn($result);

        $zendAuthentication = new ZendAuthentication(
            $this->authService->reveal(),
            [],
            $this->responsePrototype->reveal()
        );
        $result = $zendAuthentication->authenticate($this->request->reveal());
        $this->assertInstanceOf(UserInterface::class, $result);
    }
}
