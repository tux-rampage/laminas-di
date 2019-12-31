<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di\ServiceLocator;

use Laminas\Di\Di;
use Laminas\Di\ServiceLocator\DependencyInjectorProxy;
use LaminasTest\Di\TestAsset\SetterInjection\A;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Tests used to verify DependencyInjectorProxy functionality
 */
class DependencyInjectorProxyTest extends TestCase
{
    public function testWillDiscoverInjectedMethodParameters()
    {
        $di = new Di();
        $a = new A();
        $di->instanceManager()->setParameters(
            'LaminasTest\Di\TestAsset\SetterInjection\B',
            ['a' => $a]
        );
        $proxy = new DependencyInjectorProxy($di);
        $b = $proxy->get('LaminasTest\Di\TestAsset\SetterInjection\B');
        $methods = $b->getMethods();
        $this->assertSame('setA', $methods[0]['method']);
        $this->assertSame($a, $methods[0]['params'][0]);
    }
}
