<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Di\Resolver;

use Laminas\Di\Resolver\AbstractInjection;
use Laminas\Di\Resolver\InjectionInterface;
use PHPUnit\Framework\Error\Deprecated;
use PHPUnit\Framework\TestCase;

use function sprintf;

class AbstractInjectionTest extends TestCase
{
    public function testUsageIsDeprecated(): void
    {
        $this->expectDeprecation();
        $this->expectDeprecationMessage(sprintf(
            '%s is deprecated, please migrate to %s',
            AbstractInjection::class,
            InjectionInterface::class
        ));

        new class () extends AbstractInjection
        {
            public function export(): string
            {
                return '';
            }

            public function isExportable(): bool
            {
                return true;
            }
        };
    }
}
