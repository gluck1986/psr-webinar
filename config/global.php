<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 01.10.2018
 * Time: 22:02
 */

declare(strict_types=1);

use App\Http\Render\RenderInterface;
use Middlewares\BasicAuthentication;

return [
    'config' => [
        'users' => ['user' => 'user', 'user1' => 'user1'],
        'templates' => '../templates'
    ],

    BasicAuthentication::class => function (\Psr\Container\ContainerInterface $container) {
        return new BasicAuthentication($container->get('users'));
    },
    RenderInterface::class => function (\Psr\Container\ContainerInterface $container) {
        $loader = new Twig_Loader_Filesystem($container->get('templates'));
        $environment =  new Twig_Environment($loader);

        return new App\Http\Render\TwigAdapter($environment);
    }
];
