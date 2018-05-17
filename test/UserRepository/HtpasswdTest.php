<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\Expressive\Authentication\UserRepository;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Authentication\UserRepositoryInterface;
use Zend\Expressive\Authentication\UserRepository\Htpasswd;

class HtpasswdTest extends TestCase
{
    protected function setUp()
    {
        $user = $this->prophesize(UserInterface::class);
        $user->setIdentity(Argument::type('string'))->will(function ($args) use ($user) {
            $user->getIdentity()->willReturn($args[0]);
        });
        $user->setRoles(Argument::type('array'))->will(function ($args) use ($user) {
            $user->getRoles()->willReturn($args[0]);
        });
        $this->user = $user;
    }
    /**
     * @expectedException Zend\Expressive\Authentication\Exception\InvalidConfigException
     */
    public function testConstructorWithNoFile()
    {
        $htpasswd = new Htpasswd('foo', $this->user->reveal());
    }

    public function testConstructor()
    {
        $htpasswd = new Htpasswd(
            __DIR__ . '/../TestAssets/htpasswd',
            $this->user->reveal()
        );
        $this->assertInstanceOf(UserRepositoryInterface::class, $htpasswd);
    }

    public function testAuthenticate()
    {
        $htpasswd = new Htpasswd(
            __DIR__ . '/../TestAssets/htpasswd',
            $this->user->reveal()
        );

        $user = $htpasswd->authenticate('test', 'password');
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertEquals('test', $user->getIdentity());
    }

    public function testAuthenticateInvalidUser()
    {
        $htpasswd = new Htpasswd(
            __DIR__ . '/../TestAssets/htpasswd',
            $this->user->reveal()
        );
        $this->assertNull($htpasswd->authenticate('test', 'foo'));
    }

    public function testAuthenticateWithoutPassword()
    {
        $htpasswd = new Htpasswd(
            __DIR__ . '/../TestAssets/htpasswd',
            $this->user->reveal()
        );
        $this->assertNull($htpasswd->authenticate('test', null));
    }

    /**
     * @expectedException Zend\Expressive\Authentication\Exception\RuntimeException
     */
    public function testAuthenticateWithInsecureHash()
    {
        $htpasswd = new Htpasswd(
            __DIR__ . '/../TestAssets/htpasswd_insecure',
            $this->user->reveal()
        );
        $htpasswd->authenticate('test', 'password');
    }
}
