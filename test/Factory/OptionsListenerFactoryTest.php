<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014-2017 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZFTest\Rest\Factory;

use PHPUnit\Framework\TestCase;
use ReflectionObject;
use Zend\ServiceManager\ServiceManager;
use ZF\Rest\Factory\OptionsListenerFactory;
use ZF\Rest\Listener\OptionsListener;

class OptionsListenerFactoryTest extends TestCase
{
    public function setUp()
    {
        $this->services = new ServiceManager();
        $this->factory  = new OptionsListenerFactory();
    }

    public function seedConfigService()
    {
        return ['zf-rest' => [
            'some-controller' => [
                'listener'                => 'SomeListener',
                'route_name'              => 'api.rest.some',
                'route_identifer_name'    => 'some_id',
                'entity_class'            => 'SomeEntity',
                'entity_http_methods'     => ['GET', 'PATCH', 'DELETE'],
                'collection_name'         => 'some',
                'collection_http_methods' => ['GET', 'POST'],
            ],
        ]];
    }

    public function testFactoryCreatesOptionsListenerFromRestConfiguration()
    {
        $config = $this->seedConfigService();
        $this->services->setService('config', $config);

        $listener = $this->factory->createService($this->services);

        $this->assertInstanceOf(OptionsListener::class, $listener);

        $r = new ReflectionObject($listener);
        $p = $r->getProperty('config');
        $p->setAccessible(true);
        $instanceConfig = $p->getValue($listener);
        $this->assertEquals($config['zf-rest'], $instanceConfig);
    }
}
