<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\Expressive\Authentication;

use PHPUnit\Framework\TestCase;
use Zend\Expressive\Authentication\DefaultUser;
use Zend\Expressive\Authentication\UserInterface;

class DefaultUserTest extends TestCase
{
    public function testConstructor()
    {
        $user = new DefaultUser();
        $this->assertInstanceOf(UserInterface::class, $user);
    }

    public function testSetGetIdentity()
    {
        $user = new DefaultUser();
        $user->setIdentity('foo');
        $this->assertEquals('foo', $user->getIdentity());
    }

    public function testSetGetRoles()
    {
        $user = new DefaultUser();
        $user->setRoles(['foo', 'bar']);
        $this->assertEquals(['foo', 'bar'], $user->getRoles());
    }
}
