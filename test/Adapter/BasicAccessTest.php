<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */
namespace ZendTest\Expressive\Authentication\Adapter;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Expressive\Authentication\Adapter\BasicAccess;
use Zend\Expressive\Authentication\AuthenticationInterface;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Authentication\UserRegisterInterface;

class BasicAccessTest extends TestCase
{
    protected function setUp()
    {
        $this->request = $this->prophesize(ServerRequestInterface::class);
        $this->userRegister = $this->prophesize(UserRegisterInterface::class);
        $this->authenticatedUser = $this->prophesize(UserInterface::class);
    }

    public function testConstructor()
    {
        $basicAccess = new BasicAccess($this->userRegister->reveal(), 'test');
        $this->assertInstanceOf(AuthenticationInterface::class, $basicAccess);
    }

    public function testIsAuthenticatedWithoutHeader()
    {
        $this->request->getHeader('Authorization')
                      ->willReturn([]);

        $basicAccess = new BasicAccess($this->userRegister->reveal(), 'test');
        $this->assertNull($basicAccess->authenticate($this->request->reveal()));
    }

    public function testIsAuthenticatedWithoutBasic()
    {
        $this->request->getHeader('Authorization')
                      ->willReturn(['foo']);

        $basicAccess = new BasicAccess($this->userRegister->reveal(), 'test');
        $this->assertNull($basicAccess->authenticate($this->request->reveal()));
    }

    public function testIsAuthenticatedWithValidCredential()
    {
        $this->request->getHeader('Authorization')
                      ->willReturn(['Basic QWxhZGRpbjpPcGVuU2VzYW1l']);
        $this->request->withAttribute(UserInterface::class, Argument::type(UserInterface::class))
                      ->willReturn($this->request->reveal());

        $this->authenticatedUser->getUsername()->willReturn('Aladdin');
        $this->userRegister->authenticate('Aladdin', 'OpenSesame')
                           ->willReturn($this->authenticatedUser->reveal());

        $basicAccess = new BasicAccess($this->userRegister->reveal(), 'test');
        $user = $basicAccess->authenticate($this->request->reveal());
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertEquals('Aladdin', $user->getUsername());
    }

    public function testIsAuthenticatedWithNoCredential()
    {
        $this->request->getHeader('Authorization')
                      ->willReturn(['Basic QWxhZGRpbjpPcGVuU2VzYW1l']);

        $this->userRegister->authenticate('Aladdin', 'OpenSesame')
                           ->willReturn(null);

        $basicAccess = new BasicAccess($this->userRegister->reveal(), 'test');
        $this->assertNull($basicAccess->authenticate($this->request->reveal()));
    }

    public function testGetUnauthenticatedResponse()
    {
        $basicAccess = new BasicAccess($this->userRegister->reveal(), 'test');
        $response = $basicAccess->unauthorizedResponse($this->request->reveal());

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertEquals(['Basic realm="test"'], $response->getHeader('WWW-Authenticate'));
    }
}
