<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Di;

use BadMethodCallException;
use Exception;
use Laminas\Di\Di;
use PHPUnit_Framework_Error;
use SplStack;

class DiCompatibilityTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @dataProvider providesSimpleClasses
     * @param string $class
     */
    public function testDiSimple($class)
    {
        $di = new Di();

        $bareObject = new $class;

        $diObject = $di->get($class);

        $this->assertInstanceOf($class, $bareObject, 'Test instantiate simple');
        $this->assertInstanceOf($class, $diObject, 'Test $di->get');
    }

    /**
     * provides known classes invokable without parameters
     *
     * @return array
     */
    public function providesSimpleClasses()
    {
        return [
            [Di::class],
            [SplStack::class],
            [TestAsset\BasicClass::class],
        ];
    }

    /**
     *
     * error: Missing argument 1 for $class::__construct()
     * @dataProvider providesClassWithConstructionParameters
     * @param string $class
     */
    public function testRaiseErrorMissingConstructorRequiredParameter($class)
    {
        if (version_compare(PHP_VERSION, '7', '>=')) {
            $this->markTestSkipped('Errors have changed to E_FATAL, no longer allowing test to run');
        }

        $phpunit = $this;
        $caught  = false;
        set_error_handler(function ($errno, $errstr) use ($phpunit, &$caught) {
            if ($errno === E_WARNING && 0 !== strpos($errstr, 'Missing argument')) {
                $phpunit->fail('Unexpected error caught during instantiation');
                return false;
            }

            throw new BadMethodCallException('TRAPPED');
        }, E_WARNING|E_RECOVERABLE_ERROR);
        try {
            $bareObject = new $class;
        } catch (Exception $e) {
            if ($e instanceof PHPUnit_Framework_Error
                || ($e instanceof BadMethodCallException && $e->getMessage() === 'TRAPPED')
            ) {
                $caught = true;
            }
        }
        $this->assertTrue($caught);
    }

    /**
     *
     * @dataProvider providesClassWithConstructionParameters
     * @expectedException \Laminas\Di\Exception\MissingPropertyException
     * @param string $class
     */
    public function testWillThrowExceptionMissingConstructorRequiredParameterWithDi($class)
    {
        $di = new Di();
        $diObject = $di->get($class);
        $this->assertInstanceOf($class, $diObject, 'Test $di->get');
    }

    /**
     *
     * @dataProvider providesClassWithConstructionParameters
     * @param string $class
     */
    public function testCanCreateInstanceWithConstructorRequiredParameter($class, $args)
    {
        $reflection = new \ReflectionClass($class);
        $bareObject = $reflection->newInstanceArgs($args);
        $this->assertInstanceOf($class, $bareObject, 'Test instantiate with constructor required parameters');
    }

    /**
     * @dataProvider providesClassWithConstructionParameters
     * @param string $class
     */
    public function testCanCreateInstanceWithConstructorRequiredParameterWithDi($class, $args)
    {
        $di = new Di();
        $diObject = $di->get($class, $args);
        $this->assertInstanceOf($class, $diObject, 'Test $di->get with constructor required paramters');
    }

    public function providesClassWithConstructionParameters()
    {
        return [
            [TestAsset\BasicClassWithParam::class, ['foo' => 'bar']],
            [TestAsset\ConstructorInjection\X::class, ['one' => 1, 'two' => 2]],
        ];
    }
}
