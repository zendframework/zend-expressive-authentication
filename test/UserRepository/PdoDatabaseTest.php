<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\Expressive\Authentication\UserRepository;

use PDO;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Zend\Expressive\Authentication\Exception\InvalidConfigException;
use Zend\Expressive\Authentication\Exception\RuntimeException;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Authentication\UserRepositoryInterface;
use Zend\Expressive\Authentication\UserRepository\PdoDatabase;

class PdoDatabaseTest extends TestCase
{
    protected function setUp()
    {
        $this->user = $this->prophesize(UserInterface::class);
    }

    public function testConstructor()
    {
        $pdoDatabase = new PdoDatabase(
            new PDO('sqlite::memory:'),
            [],
            function () {
                return $this->user->reveal();
            }
        );
        $this->assertInstanceOf(UserRepositoryInterface::class, $pdoDatabase);
    }

    public function getConfig()
    {
        return [
            'table' => 'user',
            'field' => [
                'identity' => 'username',
                'password' => 'password',
            ]
        ];
    }

    public function testAuthenticate()
    {
        $pdo = new PDO('sqlite:'. __DIR__ . '/../TestAssets/pdo.sqlite');
        $this->user->getIdentity()->willReturn('test');

        $pdoDatabase = new PdoDatabase(
            $pdo,
            $this->getConfig(),
            function () {
                return $this->user->reveal();
            }
        );

        $user = $pdoDatabase->authenticate('test', 'password');
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertEquals('test', $user->getIdentity());
    }

    public function testAuthenticationError()
    {
        $pdo = new PDO('sqlite:'. __DIR__ . '/../TestAssets/pdo.sqlite');
        $config = $this->getConfig();
        $config['field']['identity'] = 'foo'; // mistake in the configuration

        $pdoDatabase = new PdoDatabase(
            $pdo,
            $config,
            function () {
                return $this->user->reveal();
            }
        );
        $this->expectException(RuntimeException::class);
        $user = $pdoDatabase->authenticate('test', 'password');
    }

    public function testAuthenticateInvalidUserPassword()
    {
        $pdo = new PDO('sqlite:'. __DIR__ . '/../TestAssets/pdo.sqlite');
        $pdoDatabase = new PdoDatabase(
            $pdo,
            $this->getConfig(),
            function () {
                return $this->user->reveal();
            }
        );

        $user = $pdoDatabase->authenticate('test', 'foo');
        $this->assertNull($user);
    }

    public function testAuthenticateInvalidUsername()
    {
        $pdo = new PDO('sqlite:'. __DIR__ . '/../TestAssets/pdo.sqlite');
        $pdoDatabase = new PdoDatabase(
            $pdo,
            $this->getConfig(),
            function () {
                return $this->user->reveal();
            }
        );

        $user = $pdoDatabase->authenticate('invalidusername', 'password');
        $this->assertNull($user);
    }

    public function testAuthenticateWithRole()
    {
        $pdo = new PDO('sqlite:'. __DIR__ . '/../TestAssets/pdo_role.sqlite');
        $config = $this->getConfig();
        $config['sql_get_roles'] = 'SELECT role FROM user WHERE username = :identity';

        $this->user->getIdentity()->willReturn('test');
        $this->user->getRoles()->willReturn(['admin']);

        $pdoDatabase = new PdoDatabase(
            $pdo,
            $config,
            function () {
                return $this->user->reveal();
            }
        );

        $user = $pdoDatabase->authenticate('test', 'password');
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertEquals('test', $user->getIdentity());
        $this->assertEquals(['admin'], $user->getRoles());
    }

    public function testAuthenticateWithRoles()
    {
        $pdo = new PDO('sqlite:'. __DIR__ . '/../TestAssets/pdo_roles.sqlite');
        $config = $this->getConfig();
        $config['sql_get_roles'] = 'SELECT role FROM user_role WHERE username = :identity';

        $this->user->getIdentity()->willReturn('test');
        $this->user->getRoles()->willReturn(['user', 'admin']);

        $pdoDatabase = new PdoDatabase(
            $pdo,
            $config,
            function () {
                return $this->user->reveal();
            }
        );

        $user = $pdoDatabase->authenticate('test', 'password');
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertEquals('test', $user->getIdentity());
        $this->assertEquals(['user', 'admin'], $user->getRoles());
    }

    public function testAuthenticateWithRoleRuntimeError()
    {
        $pdo = new PDO('sqlite:'. __DIR__ . '/../TestAssets/pdo_role.sqlite');
        $config = $this->getConfig();
        // add a mistake in the configuration
        $config['sql_get_roles'] = 'SELECT role FROM user WHERE foo = :identity';
        $pdoDatabase = new PdoDatabase(
            $pdo,
            $config,
            function () {
                return $this->user->reveal();
            }
        );

        $this->expectException(RuntimeException::class);
        $user = $pdoDatabase->authenticate('test', 'password');
    }

    public function testGetRolesFromUserWithEmptySql()
    {
        $pdo = new PDO('sqlite:'. __DIR__ . '/../TestAssets/pdo_roles.sqlite');
        $config = $this->getConfig();

        $pdoDatabase = new PdoDatabase(
            $pdo,
            $config,
            function () {
                return $this->user->reveal();
            }
        );
        $roles = $pdoDatabase->getUserRoles('foo');
        $this->assertEmpty($roles);
    }

    public function testGetRolesFromUserWithNoIdentityParam()
    {
        $pdo = new PDO('sqlite:'. __DIR__ . '/../TestAssets/pdo_roles.sqlite');
        $config = $this->getConfig();
        $config['sql_get_roles'] = 'SELECT role FROM user_role';

        $pdoDatabase = new PdoDatabase(
            $pdo,
            $config,
            function () {
                return $this->user->reveal();
            }
        );

        $this->expectException(InvalidConfigException::class);
        $roles = $pdoDatabase->getUserRoles('foo');
    }
}
