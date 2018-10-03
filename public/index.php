<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 02.10.2018
 * Time: 22:48
 */
declare(strict_types=1);

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
$di = new ServiceManager([
    'abstract_factories'=>[ReflectionBasedAbstractFactory::class],
    'factories' => [
        RouterInterface::class=> function (ContainerInterface $container) {
            return new AuraRouterAdapter($container->get(RouterContainer::class));
        },
        Resolver::class => function (ContainerInterface $container) {
            return new App\Http\Resolver\Resolver($container);
        }
    ],
    'services' => []
]);

//################### лямбда для сокращения кода
$r = function ($handle) use ($di): MiddlewareInterface {
    /** @var Resolver $resolver */
    $resolver = $di->get(Resolver::class);

    return $resolver->resolve($handle);
};
//###################  создание и настройка конвеера

$pipeline = new \Zend\Stratigility\MiddlewarePipe();


//################### настройка маршрутизатора
$router = $di->get(RouterInterface::class);
$router->add(['GET'], 'home','/', $r(function () {
    return new \Zend\Diactoros\Response\TextResponse('i am a simple home page');
}));

//################### получение текущего маршрута
$serverRequest = $router->match($serverRequest);

//################### получение обработчика маршрута
$handler = $serverRequest->getAttribute('handler');

//################### диспетчерезация
$pipeline->pipe($r($handler));

//################### запуск конвеера
$response = $pipeline->handle($serverRequest);

//################### отправка результата в браузер
$emitter = new \Narrowspark\HttpEmitter\SapiEmitter();
$emitter->emit($response);
