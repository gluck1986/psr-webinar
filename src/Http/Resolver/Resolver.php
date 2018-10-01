<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 30.09.2018
 * Time: 16:14
 */
declare(strict_types=1);

namespace App\Http\Resolver;

use function Enalquiler\Middleware\lazy;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Stratigility\Middleware\CallableMiddlewareDecorator;
use Zend\Stratigility\Middleware\RequestHandlerMiddleware;
use Zend\Stratigility\MiddlewarePipe;

class Resolver
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function resolve($handler): MiddlewareInterface
    {
        if (is_array($handler)) {
            $pipe = new MiddlewarePipe();

            return array_reduce($handler,
                function(MiddlewarePipe $acc, $handler) { $acc->pipe($this->resolve($handler)); },
                $pipe
            );
        } elseif (is_string($handler)) {
            return lazy(function () use ($handler) {return $this->resolve($this->container->get($handler));});
        } elseif ($handler instanceof MiddlewareInterface) {
            return $handler;
        } elseif ($handler instanceof RequestHandlerInterface) {
            return new RequestHandlerMiddleware($handler);
        }elseif (is_callable($handler)) {
            return new CallableMiddlewareDecorator($handler);
        } else {
            throw new \Exception('unknown middleware' . get_class($handler));
        }
    }
}
