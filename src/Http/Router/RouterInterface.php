<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 30.09.2018
 * Time: 15:46
 */

namespace App\Http\Router;

use Psr\Http\Message\ServerRequestInterface;

interface RouterInterface
{
    public function add(array $methods, $name, $path, $handler, $tokens);
    public function generate($name, $params): string;
    public function match(ServerRequestInterface $serverRequest): ServerRequestInterface;
}
