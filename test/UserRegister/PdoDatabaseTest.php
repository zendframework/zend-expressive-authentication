<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */
namespace ZendTest\Expressive\Authentication\UserRegister;

use PDO;
use PHPUnit\Framework\TestCase;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Authentication\UserRegisterInterface;
use Zend\Expressive\Authentication\UserRegister\PdoDatabase;

class PdoDatabaseTest extends TestCase
{
    public function testConstructor()
    {
        $pdoDatabase = new PdoDatabase(new PDO('sqlite::memory:'), []);
        $this->assertInstanceOf(UserRegisterInterface::class, $pdoDatabase);
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
}
