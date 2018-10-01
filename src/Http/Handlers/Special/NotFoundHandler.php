<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 30.09.2018
 * Time: 16:39
 */
declare(strict_types=1);

namespace App\Http\Handlers\Special;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\TextResponse;

class NotFoundHandler implements RequestHandlerInterface
{

    /**
     * Handle the request and return a response.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new TextResponse('not find', 404);
    }
}
