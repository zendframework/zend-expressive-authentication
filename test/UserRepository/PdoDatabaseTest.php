<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\Expressive\Authentication\UserRepository;

use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Zend\Expressive\Authentication\DefaultUser;
use Zend\Expressive\Authentication\Exception\InvalidConfigException;
use Zend\Expressive\Authentication\Exception\RuntimeException;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Authentication\UserRepositoryInterface;
use Zend\Expressive\Authentication\UserRepository\PdoDatabase;

class PdoDatabaseTest extends TestCase
{
    protected function setUp()
    {
        $this->userFactory = function ($identity, $roles, $details) {
            return new DefaultUser($identity, $roles, $details);
        };
    }

    public function testConstructor()
    {
        $pdoDatabase = new PdoDatabase(
            new PDO('sqlite::memory:'),
            [],
            $this->userFactory
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
        $pdoDatabase = new PdoDatabase(
            $pdo,
            $this->getConfig(),
            $this->userFactory
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
            $this->userFactory
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
            $this->userFactory
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
            $this->userFactory
        );

        $user = $pdoDatabase->authenticate('invalidusername', 'password');
        $this->assertNull($user);
    }

    public function testAuthenticateWithRole()
    {
        $pdo = new PDO('sqlite:'. __DIR__ . '/../TestAssets/pdo_role.sqlite');
        $config = $this->getConfig();
        $config['sql_get_roles'] = 'SELECT role FROM user WHERE username = :identity';

        $pdoDatabase = new PdoDatabase(
            $pdo,
            $config,
            $this->userFactory
        );

        $user = $pdoDatabase->authenticate('test', 'password');
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertEquals(['admin'], $user->getRoles());
    }

    public function testAuthenticateWithRoles()
    {
        $pdo = new PDO('sqlite:'. __DIR__ . '/../TestAssets/pdo_roles.sqlite');
        $config = $this->getConfig();
        $config['sql_get_roles'] = 'SELECT role FROM user_role WHERE username = :identity';


        $pdoDatabase = new PdoDatabase(
            $pdo,
            $config,
            $this->userFactory
        );
        $user = $pdoDatabase->authenticate('test', 'password');
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertEquals(['user', 'admin'], $user->getRoles());
    }

    public function testAuthenticateWithDetails()
    {
        $pdo = new PDO('sqlite:'. __DIR__ . '/../TestAssets/pdo_role.sqlite');
        $config = $this->getConfig();
        $config['sql_get_details'] = 'SELECT email FROM user WHERE username = :identity';

        $pdoDatabase = new PdoDatabase(
            $pdo,
            $config,
            $this->userFactory
        );

        $user = $pdoDatabase->authenticate('test', 'password');
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertEquals(['email' => 'test@foo.com'], $user->getDetails());
        $this->assertEquals('test@foo.com', $user->getDetail('email'));
    }

    public function testAuthenticateWithRolesAndDetails()
    {
        $pdo = new PDO('sqlite:'. __DIR__ . '/../TestAssets/pdo_roles.sqlite');
        $config = $this->getConfig();
        $config['sql_get_roles'] = 'SELECT role FROM user_role WHERE username = :identity';
        $config['sql_get_details'] = 'SELECT email FROM user WHERE username = :identity';

        $pdoDatabase = new PdoDatabase(
            $pdo,
            $config,
            $this->userFactory
        );

        $user = $pdoDatabase->authenticate('test', 'password');
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertEquals(['email' => 'test@foo.com'], $user->getDetails());
        $this->assertEquals('test@foo.com', $user->getDetail('email'));
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
            $this->userFactory
        );

        $this->expectException(RuntimeException::class);
        $user = $pdoDatabase->authenticate('test', 'password');
    }

    public function testAuthenticateWithEmptySql()
    {
        $pdo = new PDO('sqlite:'. __DIR__ . '/../TestAssets/pdo_roles.sqlite');
        $config = $this->getConfig();

        $pdoDatabase = new PdoDatabase(
            $pdo,
            $config,
            $this->userFactory
        );
        $user = $pdoDatabase->authenticate('test', 'password');
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertEquals('test', $user->getIdentity());
    }

    public function testAuthenticateWithNoIdentityParam()
    {
        $pdo = new PDO('sqlite:'. __DIR__ . '/../TestAssets/pdo_roles.sqlite');
        $config = $this->getConfig();
        $config['sql_get_roles'] = 'SELECT role FROM user_role';

        $pdoDatabase = new PdoDatabase(
            $pdo,
            $config,
            $this->userFactory
        );

        $this->expectException(InvalidConfigException::class);
        $user = $pdoDatabase->authenticate('test', 'password');
    }

    public function testHandlesNullPassword()
    {
        $stmt = $this->prophesize(PDOStatement::class);
        $stmt->bindParam(Argument::any(), Argument::any())->willReturn();
        $stmt->execute(Argument::any())->willReturn();
        $stmt->fetchObject()->willReturn((object)['password' => null]);

        $pdo = $this->prophesize(PDO::class);
        $pdo->prepare(Argument::any())->willReturn($stmt->reveal());

        $pdoDatabase = new PdoDatabase(
            $pdo->reveal(),
            $this->getConfig(),
            $this->userFactory
        );

        $user = $pdoDatabase->authenticate('null', null);
        $this->assertNull($user);
    }
}
