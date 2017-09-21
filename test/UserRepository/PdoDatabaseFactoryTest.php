<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */
namespace ZendTest\Expressive\Authentication\UserRepository;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Authentication\UserRepository\PdoDatabase;
use Zend\Expressive\Authentication\UserRepository\PdoDatabaseFactory;


class PdoDatabaseFactoryTest extends TestCase
{
    protected function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->factory = new PdoDatabaseFactory();
    }

    /**
     * @expectedException Zend\Expressive\Authentication\Exception\InvalidConfigException
     */
    public function testInvokeWithEmptyConfig()
    {
        $this->container->get('config')->willReturn([]);
        $pdoDatabase = ($this->factory)($this->container->reveal());
    }

    public function getPdoConfig()
    {
        return [
            [[]],
            [[
                'dsn' => 'mysql:dbname=testdb;host=127.0.0.1'
            ]],
            [[
                'dsn' => 'mysql:dbname=testdb;host=127.0.0.1',
                'table' => 'test'
            ]],
            [[
                'dsn' => 'mysql:dbname=testdb;host=127.0.0.1',
                'table' => 'test',
                'field' => []
            ]],
            [[
                'dsn' => 'mysql:dbname=testdb;host=127.0.0.1',
                'table' => 'test',
                'field' => [
                    'username' => 'email'
                ]
            ]]
        ];
    }

    /**
     * @dataProvider getPdoConfig
     * @expectedException Zend\Expressive\Authentication\Exception\InvalidConfigException
     */
    public function testInvokeWithInvalidConfig($pdoConfig)
    {
        $this->container->get('config')->willReturn([
            'user_register' => [ 'pdo' => $pdoConfig ]
        ]);
        $pdoDatabase = ($this->factory)($this->container->reveal());
    }

    public function testInvokeWithValidConfig()
    {
        $this->container->get('config')->willReturn([
            'user_register' => [
                'pdo' =>  [
                    'dsn' => 'sqlite:'. __DIR__ . '/../TestAssets/pdo.sqlite',
                    'table' => 'user',
                    'field' => [
                        'username' => 'username',
                        'password' => 'password'
                    ]
                ]
            ]
        ]);
        $pdoDatabase = ($this->factory)($this->container->reveal());
        $this->assertInstanceOf(PdoDatabase::class, $pdoDatabase);
    }
}
