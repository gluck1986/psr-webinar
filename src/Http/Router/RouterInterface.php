<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 02.10.2018
 * Time: 23:02
 */
namespace App\Http\Router;

use Psr\Http\Message\ServerRequestInterface;

interface RouterInterface
{
    public function match(ServerRequestInterface $serverRequest);
    public function generate($name, $params): string;
    public function add(array $methods, string $name, $path, $handler, $tokens = []);
}
