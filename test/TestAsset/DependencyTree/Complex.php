<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Di\TestAsset\DependencyTree;

class Complex
{
    /**
     * @var Level1
     */
    public $result;

    /**
     * @var AdditionalLevel1
     */
    public $result2;

    public function __construct(Level1 $dep, AdditionalLevel1 $dep2)
    {
        $this->result = $dep;
        $this->result2 = $dep2;
    }
}
