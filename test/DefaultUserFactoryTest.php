<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\Expressive\Authentication;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Authentication\DefaultUser;
use Zend\Expressive\Authentication\DefaultUserFactory;
use Zend\Expressive\Authentication\UserInterface;

class DefaultUserFactoryTest extends TestCase
{
    public function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
    }

    public function testConstructor()
    {
        $factory = new DefaultUserFactory();
        $this->assertInstanceOf(DefaultUserFactory::class, $factory);
    }

    public function testInvoke()
    {
        $factory = new DefaultUserFactory();
        $userFactory = $factory($this->container->reveal());
        $defaultUser = $userFactory('foo');
        $this->assertInstanceOf(DefaultUser::class, $defaultUser);
    }
}
