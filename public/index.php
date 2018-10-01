<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 30.09.2018
 * Time: 14:33
 */

declare(strict_types=1);

use App\Http\Handlers\BlogHandler;
use App\Http\Handlers\IndexHandler;

use App\Http\Handlers\Special\NotFoundHandler;
use Zend\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
use Zend\ServiceManager\ServiceManager;

require '../vendor/autoload.php';


$config = (new \Zend\ConfigAggregator\ConfigAggregator([
    new \Zend\ConfigAggregator\PhpFileProvider('../config/*{global, local}.php')
]))->getMergedConfig();


$serverRequest = \Zend\Diactoros\ServerRequestFactory::fromGlobals();
$services = $config['config'] ?? [];
$config = array_filter($config, function ($key) {return $key != 'config';}, ARRAY_FILTER_USE_KEY);

$di = new ServiceManager([
    'abstract_factories'=>[ReflectionBasedAbstractFactory::class],
    'factories' => $config,
    'services' => $services
]);

$pipeline = new \Zend\Stratigility\MiddlewarePipe();

$router = new \App\Http\Router\Router(new \Aura\Router\RouterContainer());

$resolever = new \App\Http\Resolver\Resolver($di);

$router->add(['GET'], 'home', '/', IndexHandler::class);
$router->add(['GET'], 'blog', '/blog/{id}', BlogHandler::class, ['id'=>'\d+']);

$serverRequest = $router->match($serverRequest);
$handler = $serverRequest->getAttribute('handler') ?? NotFoundHandler::class;

$pipeline->pipe($resolever->resolve(\Middlewares\BasicAuthentication::class));
$pipeline->pipe($resolever->resolve($handler));

$response = $pipeline->handle($serverRequest);

$emitter = new \Narrowspark\HttpEmitter\SapiEmitter();
$emitter->emit($response);
