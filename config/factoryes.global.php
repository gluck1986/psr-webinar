<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 03.10.2018
 * Time: 20:06
 */

declare(strict_types=1);

use App\Http\Render\RenderInterface;
use App\Http\Resolver\Resolver;
use App\Http\Router\AuraRouterAdapter;
use App\Http\Router\RouterInterface;
use Aura\Router\RouterContainer;
use Middlewares\BasicAuthentication;
use Psr\Container\ContainerInterface;

return [
    BasicAuthentication::class => function (\Psr\Container\ContainerInterface $container) {
        return new BasicAuthentication($container->get('users'));
    },
    RenderInterface::class => function (\Psr\Container\ContainerInterface $container) {
        $loader = new Twig_Loader_Filesystem($container->get('templates'));
        $environment = new Twig_Environment($loader);
        return new App\Http\Render\TwigAdapter($environment);
    },
    RouterInterface::class => function (ContainerInterface $container) {
        return new AuraRouterAdapter($container->get(RouterContainer::class));
    },
    Resolver::class => function (ContainerInterface $container) {
        return new App\Http\Resolver\Resolver($container);
    },
];
