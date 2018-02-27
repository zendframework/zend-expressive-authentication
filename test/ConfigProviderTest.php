<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\Expressive\Authentication;

use PHPUnit\Framework\TestCase;
use Zend\Expressive\Authentication\AuthenticationMiddleware;
use Zend\Expressive\Authentication\ConfigProvider;
use Zend\Expressive\Authentication\UserRepository;

class ConfigProviderTest extends TestCase
{
    /** @var ConfigProvider */
    private $provider;

    protected function setUp()
    {
        $this->provider = new ConfigProvider();
    }

    public function testProviderDefinesExpectedFactoryServices()
    {
        $config = $this->provider->getDependencies();
        $factories = $config['factories'];

        $this->assertArrayHasKey(AuthenticationMiddleware::class, $factories);
        $this->assertArrayHasKey(UserRepository\Htpasswd::class, $factories);
        $this->assertArrayHasKey(UserRepository\PdoDatabase::class, $factories);
    }

    public function testInvocationReturnsArrayWithDependencies()
    {
        $config = ($this->provider)();

        $this->assertInternalType('array', $config);
        $this->assertArrayHasKey('authentication', $config);
        $this->assertInternalType('array', $config['authentication']);

        $this->assertArrayHasKey('dependencies', $config);
        $this->assertInternalType('array', $config['dependencies']);
        $this->assertArrayHasKey('aliases', $config['dependencies']);
        $this->assertArrayHasKey('factories', $config['dependencies']);
    }
}
