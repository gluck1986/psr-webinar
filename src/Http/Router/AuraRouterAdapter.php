<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 02.10.2018
 * Time: 23:08
 */
declare(strict_types=1);

namespace App\Http\Router;

use Aura\Router\Route;
use Aura\Router\RouterContainer;
use Psr\Http\Message\ServerRequestInterface;

class AuraRouterAdapter implements RouterInterface
{

    private $adapter;

    public function __construct(RouterContainer $routerContainer)
    {
        $this->adapter = $routerContainer;
    }

    public function match(ServerRequestInterface $serverRequest)
    {
        $route = $this->adapter->getMatcher()->match($serverRequest);
        if ($route) {
            $serverRequest = $serverRequest->withAttribute('route', $route);
            $serverRequest = $serverRequest->withAttribute('handler', $route->handler);
            foreach ($route->attributes as $name => $value) {
                $serverRequest = $serverRequest->withAttribute($name, $value);
            }
        }

        return $serverRequest;
    }

    public function generate($name, $params = []): string
    {
        $this->adapter->getGenerator()->generate($name, $params);
    }

    public function add(array $methods, string $name, $path, $handler, $tokens = [])
    {
        $map = $this->adapter->getMap();
        $route = new Route();
        $route->allows($methods);
        $route->name($name);
        $route->path($path);
        $route->handler($handler);
        $route->tokens($tokens);
        $map->addRoute($route);
    }
}
