<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */
namespace ZendTest\Expressive\Authentication\UserRepository;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Authentication\UserRepository\Htpasswd;
use Zend\Expressive\Authentication\UserRepository\HtpasswdFactory;


class HtpasswdFactoryTest extends TestCase
{
    protected function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->factory = new HtpasswdFactory();
    }

    /**
     * @expectedException Zend\Expressive\Authentication\Exception\InvalidConfigException
     */
    public function testInvokeWithEmptyConfig()
    {
        $this->container->get('config')->willReturn([]);
        $htpasswd = ($this->factory)($this->container->reveal());
    }

    /**
     * @expectedException Zend\Expressive\Authentication\Exception\InvalidConfigException
     */
    public function testInvokeWithInvalidConfig()
    {
        $this->container->get('config')->willReturn([
            'user_repository' => [
                'htpasswd' => 'foo'
            ]
        ]);
        $htpasswd = ($this->factory)($this->container->reveal());
    }

    public function testInvokeWithValidConfig()
    {
        $this->container->get('config')->willReturn([
            'user_repository' => [
                'htpasswd' => __DIR__ . '/../TestAssets/htpasswd'
            ]
        ]);
        $htpasswd = ($this->factory)($this->container->reveal());
        $this->assertInstanceOf(Htpasswd::class, $htpasswd);
    }
}
