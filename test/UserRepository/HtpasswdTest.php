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
    const EXAMPLE_IDENTITY = 'test';

    protected function setUp()
    {
        $this->user = $this->prophesize(UserInterface::class);
        $this->user->getIdentity()->willReturn(self::EXAMPLE_IDENTITY);
    }
    /**
     * @expectedException Zend\Expressive\Authentication\Exception\InvalidConfigException
     */
    public function testConstructorWithNoFile()
    {
        $htpasswd = new Htpasswd(
            'foo',
            function () {
                return $this->user->reveal();
            }
        );
    }

    public function testConstructor()
    {
        $htpasswd = new Htpasswd(
            __DIR__ . '/../TestAssets/htpasswd',
            function () {
                return $this->user->reveal();
            }
        );
        $this->assertInstanceOf(UserRepositoryInterface::class, $htpasswd);
    }

    public function testAuthenticate()
    {
        $htpasswd = new Htpasswd(
            __DIR__ . '/../TestAssets/htpasswd',
            function () {
                return $this->user->reveal();
            }
        );

        $user = $htpasswd->authenticate(self::EXAMPLE_IDENTITY, 'password');
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertEquals(self::EXAMPLE_IDENTITY, $user->getIdentity());
    }

    public function testAuthenticateInvalidUser()
    {
        $htpasswd = new Htpasswd(
            __DIR__ . '/../TestAssets/htpasswd',
            function () {
                return $this->user->reveal();
            }
        );
        $this->assertNull($htpasswd->authenticate(self::EXAMPLE_IDENTITY, 'foo'));
    }

    public function testAuthenticateWithoutPassword()
    {
        $htpasswd = new Htpasswd(
            __DIR__ . '/../TestAssets/htpasswd',
            function () {
                return $this->user->reveal();
            }
        );
        $this->assertNull($htpasswd->authenticate(self::EXAMPLE_IDENTITY, null));
    }

    /**
     * @expectedException Zend\Expressive\Authentication\Exception\RuntimeException
     */
    public function testAuthenticateWithInsecureHash()
    {
        $htpasswd = new Htpasswd(
            __DIR__ . '/../TestAssets/htpasswd_insecure',
            function () {
                return $this->user->reveal();
            }
        );
        $htpasswd->authenticate(self::EXAMPLE_IDENTITY, 'password');
    }
}
