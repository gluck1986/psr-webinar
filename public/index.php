<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 02.10.2018
 * Time: 22:48
 */
declare(strict_types=1);

require '../vendor/autoload.php';

$serverRequest = \Zend\Diactoros\ServerRequestFactory::fromGlobals();

$emitter = new \Narrowspark\HttpEmitter\SapiEmitter();

$router = new \App\Http\Router\AuraRouterAdapter(new \Aura\Router\RouterContainer());

$router->add(['GET'], 'page', '/page/{id}', 'handler', ['id'=>'\d+'] );

$serverRequest = $router->match($serverRequest);

$response = new \Zend\Diactoros\Response\TextResponse(print_r($serverRequest->getAttributes(), true));

$emitter->emit($response);
