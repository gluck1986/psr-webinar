<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 30.09.2018
 * Time: 15:50
 */
declare(strict_types=1);

namespace App\Http\Router;

use Aura\Router\Route;
use Aura\Router\RouterContainer;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Router implements RouterInterface
{

    private $aura;

    public function __construct(RouterContainer $aura)
    {
        $this->aura = $aura;
    }

    public function add(array $methods, $name, $path, $handler, $tokens = [])
    {
        $route = new Route();
        $route->name($name);
        $route->path($path);
        $route->allows(array_map('strtoupper', $methods));
        $route->tokens($tokens);
        $route->handler($handler);
        $this->aura->getMap()->addRoute($route);
    }

    public function generate($name, $params = []): string
    {
        $this->aura->getGenerator()->generate($name, $params);
    }

    public function match(ServerRequestInterface $serverRequest): ServerRequestInterface
    {
        if ($route = $this->aura->getMatcher()->match($serverRequest)) {
            $serverRequest = $serverRequest->withAttribute('route', $route);
            $serverRequest = $serverRequest->withAttribute('handler', $route->handler);
            foreach ($route->attributes as $key=>$value) {
                $serverRequest = $serverRequest->withAttribute($key,$value);
            }
        }

        return $serverRequest;
    }
}
