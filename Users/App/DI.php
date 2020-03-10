<?php declare(strict_types=1);

namespace App;

use App\Exception\MissingReferenceException;
use App\Services\Payloads\Payload;
use App\Services\Service;
use ReflectionClass;

class DI
{
    /** @var \Closure[] $instances */
    private static array $instances = [];

    public static function executeService(Service $service, Payload $payload): void
    {
        $serviceRef = new \ReflectionMethod($service, 'run');
        $parameters = $serviceRef->getParameters();
        $parameterInstances = [];
        foreach ($parameters as $parameter) {
            if ($parameter->getClass()->getName() === Payload::class) {
                $parameterInstances[] = $payload;
            } else {
                $parameterInstances[] = static::getInstance($parameter->getClass()->getName());
            }
        }

        $service->run(...$parameterInstances);
    }

    public static function assign(string $interface, \Closure $instantiationProcess): void
    {
        static::$instances[$interface] = $instantiationProcess;
    }

    private static function getInstance(string $interface)
    {
        if (!isset(static::$instances[$interface])) {
            throw new MissingReferenceException('No instructions provided on how to provide a `' . $interface . '` instance');
        }

        return (static::$instances[$interface])();
    }
}
