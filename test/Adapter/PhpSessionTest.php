<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */
namespace ZendTest\Expressive\Authentication\Adapter;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Authentication\Adapter\PhpSession;
use Zend\Expressive\Authentication\AuthenticationInterface;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Authentication\UserRepositoryInterface;

class PhpSessionTest extends TestCase
{
    protected function setUp()
    {
        $this->request = $this->prophesize(ServerRequestInterface::class);
        $this->userRegister = $this->prophesize(UserRepositoryInterface::class);
        $this->authenticatedUser = $this->prophesize(UserInterface::class);
        $this->responsePrototype = $this->prophesize(ResponseInterface::class);
        // clean the PHP session for testing purpose
        session_abort();
    }

    public function testConstructor()
    {
        $phpSession = new PhpSession(
            $this->userRegister->reveal(),
            [],
            $this->responsePrototype->reveal()
        );
        $this->assertInstanceOf(AuthenticationInterface::class, $phpSession);
    }

    public function testAuthenticateWithEmptyCookieAndGetMethod()
    {
        $this->request->getCookieParams()->willReturn([]);
        $this->request->getMethod()->willReturn('GET');

        $phpSession = new PhpSession(
            $this->userRegister->reveal(),
            [],
            $this->responsePrototype->reveal()
        );
        $this->assertNull($phpSession->authenticate($this->request->reveal()));
    }

    public function testAuthenticateWithEmptyCookieAndPostMethodWithNoData()
    {
        $this->request->getCookieParams()->willReturn([]);
        $this->request->getMethod()->willReturn('POST');
        $this->request->getParsedBody()->willReturn([]);

        $phpSession = new PhpSession(
            $this->userRegister->reveal(),
            [],
            $this->responsePrototype->reveal()
        );
        $this->assertNull($phpSession->authenticate($this->request->reveal()));
    }

    public function testAuthenticateWithEmptyCookieAndPostMethodWithDataWithDefaultFields()
    {
        $this->request->getCookieParams()->willReturn([]);
        $this->request->getMethod()->willReturn('POST');
        $this->request->getParsedBody()->willReturn([
            'username' => 'foo',
            'password' => 'bar'
        ]);
        $this->userRegister->authenticate('foo', 'bar')
                           ->willReturn($this->authenticatedUser->reveal());

        $phpSession = new PhpSession(
            $this->userRegister->reveal(),
            [],
            $this->responsePrototype->reveal()
        );
        $result = $phpSession->authenticate($this->request->reveal());
        $this->assertInstanceOf(UserInterface::class, $result);
        $this->assertInstanceOf(UserInterface::class, $_SESSION[UserInterface::class]);
    }

    public function testAuthenticateWithEmptyCookieAndPostMethodWithDataWithCustomFields()
    {
        $this->request->getCookieParams()->willReturn([]);
        $this->request->getMethod()->willReturn('POST');
        $this->request->getParsedBody()->willReturn([
            'user' => 'foo',
            'pass' => 'bar'
        ]);
        $this->userRegister->authenticate('foo', 'bar')
                           ->willReturn($this->authenticatedUser->reveal());

       $phpSession = new PhpSession(
           $this->userRegister->reveal(),
           [
               'username' => 'user',
               'password' => 'pass'
           ],
           $this->responsePrototype->reveal()
       );
       $result = $phpSession->authenticate($this->request->reveal());
       $this->assertInstanceOf(UserInterface::class, $result);
       $this->assertInstanceOf(UserInterface::class, $_SESSION[UserInterface::class]);
   }

   public function testAuthenticationWithValidCookieAndUser()
   {
       $this->request->getCookieParams()->willReturn([
           AuthenticationInterface::class => '1234567890'
       ]);

       session_name(AuthenticationInterface::class);
       session_id('1234567890');
       session_start([
           'use_cookies' => false,
           'use_only_cookies' => true
       ]);
       $_SESSION[UserInterface::class] = $this->authenticatedUser->reveal();
       session_write_close();
       session_abort();

       $phpSession = new PhpSession(
           $this->userRegister->reveal(),
           [],
           $this->responsePrototype->reveal()
       );
       $result = $phpSession->authenticate($this->request->reveal());
       $this->assertInstanceOf(UserInterface::class, $result);
       $this->assertEquals($_SESSION[UserInterface::class], $result);
   }

   public function testAuthenticationWithValidCookieInvalidUser()
   {
       $this->request->getCookieParams()->willReturn([
           AuthenticationInterface::class => '1234567890'
       ]);

       session_name(AuthenticationInterface::class);
       session_id('1234567890');
       session_start([
           'use_cookies' => false,
           'use_only_cookies' => true
       ]);
       $_SESSION[UserInterface::class] = 'foo';
       session_write_close();
       session_abort();

       $phpSession = new PhpSession(
           $this->userRegister->reveal(),
           [],
           $this->responsePrototype->reveal()
       );
       $this->assertNull($phpSession->authenticate($this->request->reveal()));
   }

   public function testAuthenticationWithValidCookieNoUser()
   {
       $this->request->getCookieParams()->willReturn([
           AuthenticationInterface::class => '1234567890'
       ]);

       session_name(AuthenticationInterface::class);
       session_id('1234567890');
       session_start([
           'use_cookies' => false,
           'use_only_cookies' => true
       ]);
       $_SESSION['foo'] = 'bar';
       session_write_close();
       session_abort();

       $phpSession = new PhpSession(
           $this->userRegister->reveal(),
           [],
           $this->responsePrototype->reveal()
       );
       $this->assertNull($phpSession->authenticate($this->request->reveal()));
   }

   public function testUnauthorizedResponse()
   {
       $this->responsePrototype->getHeader('Location')
                               ->willReturn(['/login']);
       $this->responsePrototype->withHeader('Location', '/login')
                               ->willReturn($this->responsePrototype->reveal());
       $this->responsePrototype->withStatus(301)
                               ->willReturn($this->responsePrototype->reveal());

       $phpSession = new PhpSession(
           $this->userRegister->reveal(),
           [ 'redirect' => '/login' ],
           $this->responsePrototype->reveal()
       );

       $result = $phpSession->unauthorizedResponse($this->request->reveal());
       $this->assertInstanceOf(ResponseInterface::class, $result);
       $this->assertEquals(['/login'], $result->getHeader('Location'));
   }
}
