<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\Expressive\Authentication\UserRepository;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Authentication\Exception\InvalidConfigException;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Authentication\UserRepository\PdoDatabase;
use Zend\Expressive\Authentication\UserRepository\PdoDatabaseFactory;

class PdoDatabaseFactoryTest extends TestCase
{
    protected function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->user = $this->prophesize(UserInterface::class);
        $this->factory = new PdoDatabaseFactory();
    }

    public function testInvokeWithMissingConfig()
    {
        // We cannot throw a ContainerExceptionInterface directly; this
        // approach simply mimics `get()` throwing _any_ exception, which is
        // what will happen if `config` is not defined.
        $this->container->get('config')->willThrow(new InvalidConfigException());

        $this->expectException(InvalidConfigException::class);
        ($this->factory)($this->container->reveal());
    }

    public function testInvokeWithEmptyConfig()
    {
        $this->container->get('config')->willReturn([]);

        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('PDO values are missing in authentication config');
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
                    'identity' => 'email'
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
            'authentication' => [ 'pdo' => $pdoConfig ]
        ]);
        $this->container->get(UserInterface::class)->willReturn(
            function () {
                return $this->user->reveal();
            }
        );
        $pdoDatabase = ($this->factory)($this->container->reveal());
    }

    public function testInvokeWithValidConfig()
    {
        $this->container->get('config')->willReturn([
            'authentication' => [
                'pdo' => [
                    'dsn' => 'sqlite:'. __DIR__ . '/../TestAssets/pdo.sqlite',
                    'table' => 'user',
                    'field' => [
                        'identity' => 'username',
                        'password' => 'password'
                    ]
                ]
            ]
        ]);
        $this->container->get(UserInterface::class)->willReturn(
            function () {
                return $this->user->reveal();
            }
        );
        $pdoDatabase = ($this->factory)($this->container->reveal());
        $this->assertInstanceOf(PdoDatabase::class, $pdoDatabase);
    }
}
