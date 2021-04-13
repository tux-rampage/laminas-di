<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Di;

use ArrayAccess;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Stdlib\Parameters;
use Traversable;

use function array_pop;
use function assert;
use function class_exists;
use function interface_exists;
use function is_array;
use function is_iterable;
use function is_string;
use function strpos;
use function trigger_error;

use const E_USER_DEPRECATED;

/**
 * Provides a migration config from laminas-di 2.x configuration arrays
 */
class LegacyConfig extends Config
{
    /**
     * @param array|ArrayAccess|Traversable $config
     */
    public function __construct($config)
    {
        parent::__construct([]);

        if ($config instanceof Traversable) {
            $config = ArrayUtils::iteratorToArray($config);
        }

        if (! is_array($config)) {
            throw new Exception\InvalidArgumentException('Config data must be an array or implement Traversable');
        }

        if (isset($config['instance']) && is_iterable($config['instance'])) {
            $this->configureInstance($config['instance']);
        }
    }

    /**
     * @param iterable<array-key, mixed> $parameters
     * @return array<array-key, mixed>
     */
    private function prepareParametersArray($parameters): array
    {
        $prepared = [];

        /** @var mixed $value */
        foreach ($parameters as $key => $value) {
            if (is_string($key) && strpos($key, ':') !== false) {
                trigger_error('Full qualified parameter positions are no longer supported', E_USER_DEPRECATED);
            }

            /** @psalm-var mixed */
            $prepared[$key] = $value;
        }

        return $prepared;
    }

    /**
     * @param iterable<array-key, mixed> $config
     */
    private function configureInstance($config): void
    {
        foreach ($config as $target => $data) {
            switch ($target) {
                case 'aliases':
                case 'alias':
                    foreach ($data as $name => $class) {
                        if (class_exists($class) || interface_exists($class)) {
                            $this->setAlias($name, $class);
                        }
                    }

                    break;

                case 'preferences':
                case 'preference':
                    foreach ($data as $type => $pref) {
                        $preference = is_array($pref) ? array_pop($pref) : $pref;
                        $this->setTypePreference($type, $preference);
                    }

                    break;

                default:
                    assert(is_string($target));

                    $config     = new Parameters($data);
                    $parameters = $config->get('parameters', $config->get('parameter'));

                    if (is_array($parameters) || $parameters instanceof Traversable) {
                        $parameters = $this->prepareParametersArray($parameters, $target);
                        $this->setParameters($target, $parameters);
                    }

                    break;
            }
        }
    }

    /**
     * Export the configuraton to an array
     */
    public function toArray(): array
    {
        return [
            'preferences' => $this->preferences,
            'types'       => $this->types,
        ];
    }
}
