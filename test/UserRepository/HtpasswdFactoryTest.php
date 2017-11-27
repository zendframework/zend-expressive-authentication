<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */
namespace ZendTest\Expressive\Authentication\UserRepository;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Authentication\Exception\InvalidConfigException;
use Zend\Expressive\Authentication\UserRepository\Htpasswd;
use Zend\Expressive\Authentication\UserRepository\HtpasswdFactory;

class HtpasswdFactoryTest extends TestCase
{
    protected function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->factory = new HtpasswdFactory();
    }

    public function testInvokeWithMissingConfig()
    {
        $this->container->has('config')->willReturn(false);
        $this->container->get('config')->shouldNotBeCalled();

        $this->expectException(InvalidConfigException::class);
        $htpasswd = ($this->factory)($this->container->reveal());
    }

    public function testInvokeWithEmptyConfig()
    {
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn([]);

        $this->expectException(InvalidConfigException::class);
        $htpasswd = ($this->factory)($this->container->reveal());
    }

    public function testInvokeWithInvalidConfig()
    {
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn([
            'authentication' => [
                'htpasswd' => 'foo'
            ]
        ]);

        $this->expectException(InvalidConfigException::class);
        $htpasswd = ($this->factory)($this->container->reveal());
    }

    public function testInvokeWithValidConfig()
    {
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn([
            'authentication' => [
                'htpasswd' => __DIR__ . '/../TestAssets/htpasswd'
            ]
        ]);
        $htpasswd = ($this->factory)($this->container->reveal());
        $this->assertInstanceOf(Htpasswd::class, $htpasswd);
    }
}
