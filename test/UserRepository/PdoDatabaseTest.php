<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */
namespace ZendTest\Expressive\Authentication\UserRepository;

use PDO;
use PHPUnit\Framework\TestCase;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Authentication\UserRepositoryInterface;
use Zend\Expressive\Authentication\UserRepository\PdoDatabase;

class PdoDatabaseTest extends TestCase
{
    public function testConstructor()
    {
        $pdoDatabase = new PdoDatabase(new PDO('sqlite::memory:'), []);
        $this->assertInstanceOf(UserRepositoryInterface::class, $pdoDatabase);
    }

    public function testAuthenticate()
    {
        $pdo = new PDO('sqlite:'. __DIR__ . '/../TestAssets/pdo.sqlite');
        $pdoDatabase = new PdoDatabase($pdo, [
            'table' => 'user',
            'field' => [
                'username' => 'username',
                'password' => 'password'
            ]
        ]);

        $user = $pdoDatabase->authenticate('test', 'password');
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertEquals('test', $user->getUsername());
    }

    public function testAuthenticateInvalidUser()
    {
        $pdo = new PDO('sqlite:'. __DIR__ . '/../TestAssets/pdo.sqlite');
        $pdoDatabase = new PdoDatabase($pdo, [
            'table' => 'user',
            'field' => [
                'username' => 'username',
                'password' => 'password'
            ]
        ]);

        $user = $pdoDatabase->authenticate('test', 'foo');
        $this->assertNull($user);
    }

    public function testAuthenticateInvalidUsername()
    {
        $pdo = new PDO('sqlite:'. __DIR__ . '/../TestAssets/pdo.sqlite');
        $pdoDatabase = new PdoDatabase($pdo, [
            'table' => 'user',
            'field' => [
                'username' => 'username',
                'password' => 'password'
            ]
        ]);

        $user = $pdoDatabase->authenticate('invaliduser', 'foo');
        $this->assertNull($user);
    }

    public function testAuthenticateWithRole()
    {
        $pdo = new PDO('sqlite:'. __DIR__ . '/../TestAssets/pdo_role.sqlite');
        $pdoDatabase = new PdoDatabase($pdo, [
            'table' => 'user',
            'field' => [
                'username' => 'username',
                'password' => 'password'
            ],
            'sql_get_roles' => 'SELECT role FROM user WHERE username = :username'
        ]);

        $user = $pdoDatabase->authenticate('test', 'password');
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertEquals('test', $user->getUsername());
        $this->assertEquals(['admin'], $user->getUserRoles());
    }

    public function testAuthenticateWithRoles()
    {
        $pdo = new PDO('sqlite:'. __DIR__ . '/../TestAssets/pdo_roles.sqlite');
        $pdoDatabase = new PdoDatabase($pdo, [
            'table' => 'user',
            'field' => [
                'username' => 'username',
                'password' => 'password'
            ],
            'sql_get_roles' => 'SELECT role FROM user_role WHERE username = :username'
        ]);

        $user = $pdoDatabase->authenticate('test', 'password');
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertEquals('test', $user->getUsername());
        $this->assertEquals(['user', 'admin'], $user->getUserRoles());
    }
}
