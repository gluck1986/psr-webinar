<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 02.10.2018
 * Time: 22:48
 */
declare(strict_types=1);

use App\Http\Handlers\CalcHandler;
use App\Http\Handlers\IndexHandler;
use App\Http\Handlers\NotFoundHandler;
use App\Http\Render\RenderInterface;
use App\Http\Resolver\Resolver;
use App\Http\Router\AuraRouterAdapter;
use App\Http\Router\RouterInterface;
use Aura\Router\RouterContainer;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Zend\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
use Zend\ServiceManager\ServiceManager;

require '../vendor/autoload.php';

$serverRequest = \Zend\Diactoros\ServerRequestFactory::fromGlobals();
//################### Инициализация, настройка инверсии

$config = (new \Zend\ConfigAggregator\ConfigAggregator([
    new \Zend\ConfigAggregator\PhpFileProvider('../config/{{,*.}global,{,*.}local}.php')
]))->getMergedConfig();
$services = $config['config'] ?? [];
$config = array_filter($config, function ($key) {return $key != 'config';}, ARRAY_FILTER_USE_KEY);

$di = new ServiceManager([
    'abstract_factories'=>[ReflectionBasedAbstractFactory::class],
    'factories' => $config,
    'services' => $services
]);

//################### лямбда для сокращения кода
$r = function ($handle) use ($di): MiddlewareInterface {
    /** @var Resolver $resolver */
    $resolver = $di->get(Resolver::class);

    return $resolver->resolve($handle);
};
//###################  создание и настройка конвеера

$pipeline = new \Zend\Stratigility\MiddlewarePipe();
$pipeline->pipe($r(\Middlewares\ClientIp::class));
$pipeline->pipe($r(\Middlewares\BasicAuthentication::class));

//################### настройка маршрутизатора
$router = $di->get(RouterInterface::class);
$router->add(['GET'], 'home','/', $r([IndexHandler::class]));
$router->add(['GET'], 'calc','/calc/{a}/{b}', $r(CalcHandler::class), ['a'=>'\d+', 'b'=>'\d+']);

//################### получение текущего маршрута
$serverRequest = $router->match($serverRequest);

//################### получение обработчика маршрута
$handler = $serverRequest->getAttribute('handler')?? NotFoundHandler::class;

//################### диспетчерезация
$pipeline->pipe($r($handler));

//################### запуск конвеера
$response = $pipeline->handle($serverRequest);

//################### отправка результата в браузер
$emitter = new \Narrowspark\HttpEmitter\SapiEmitter();
$emitter->emit($response);
