<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2013 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZFTest\Rest;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\EventManager\EventManager;
use ZF\Rest\Resource;

/**
 * @subpackage UnitTest
 */
class AbstractResourceListenerTest extends TestCase
{
    public $methodInvokedInListener;
    public $paramsPassedToListener;

    public function setUp()
    {
        $this->methodInvokedInListener = null;
        $this->paramsPassedToListener  = null;

        $this->resource = new Resource();
        $this->events   = $events = new EventManager();
        $this->resource->setEventManager($events);

        $this->listener = new TestAsset\TestResourceListener($this);
        $events->attach($this->listener);
    }

    public function events()
    {
        // Casting arrays to objects when the associated Resource method will
        // cast to object.
        return array(
            'create'      => array('create', array('data' => (object) array('foo' => 'bar'))),
            'update'      => array('update', array('id' => 'identifier', 'data' => (object) array('foo' => 'bar'))),
            'replaceList' => array('replaceList', array('data' => array((object) array('foo' => 'bar')))),
            'patch'       => array('patch', array('id' => 'identifier', 'data' => (object) array('foo' => 'bar'))),
            'delete'      => array('delete', array('id' => 'identifier')),
            'deleteList'  => array('deleteList', array('data' => array('foo' => 'bar'))),
            'fetch'       => array('fetch', array('id' => 'identifier')),
            'fetchAll'    => array('fetchAll', array()),
        );
    }

    /**
     * @dataProvider events
     */
    public function testResourceMethodsAreInvokedWhenEventsAreTriggered($method, $eventArgs)
    {
        $this->methodInvokedInListener = null;
        $this->paramsPassedToListener  = null;

        switch ($method) {
            case 'create':
                $this->resource->create($eventArgs['data']);
                break;
            case 'update':
                $this->resource->update($eventArgs['id'], $eventArgs['data']);
                break;
            case 'replaceList':
                $this->resource->replaceList($eventArgs['data']);
                break;
            case 'patch':
                $this->resource->patch($eventArgs['id'], $eventArgs['data']);
                break;
            case 'delete':
                $this->resource->delete($eventArgs['id']);
                break;
            case 'deleteList':
                $this->resource->deleteList($eventArgs['data']);
                break;
            case 'fetch':
                $this->resource->fetch($eventArgs['id']);
                break;
            case 'fetchAll':
                $this->resource->fetchAll($eventArgs);
                break;
        }

        $expectedMethod = get_class($this->listener) . '::' . $method;
        $expectedParams = array_values($eventArgs);

        $this->assertEquals($expectedMethod, $this->methodInvokedInListener);
        $this->assertEquals($expectedParams, $this->paramsPassedToListener, var_export($this->paramsPassedToListener, 1));
    }
}
