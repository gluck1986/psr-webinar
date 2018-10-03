<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 02.10.2018
 * Time: 22:48
 */
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface;
use Zend\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
use Zend\ServiceManager\ServiceManager;



require '../vendor/autoload.php';

$serverRequest = \Zend\Diactoros\ServerRequestFactory::fromGlobals();

$di = new ServiceManager([
    'abstract_factories'=>[ReflectionBasedAbstractFactory::class],
    'factories' => [],
    'services' => []
]);


$pipeline = new \Zend\Stratigility\MiddlewarePipe();
$router = new \App\Http\Router\AuraRouterAdapter(new \Aura\Router\RouterContainer());
$resolver = new \App\Http\Resolver\Resolver($di);

/** todo routes */

$serverRequest = $router->match($serverRequest);

$handler = $serverRequest->getAttribute('handler');

$pipeline->pipe($resolver->resolve($handler));

$response = $pipeline->handle($serverRequest);

$emitter = new \Narrowspark\HttpEmitter\SapiEmitter();

$emitter->emit($response);
