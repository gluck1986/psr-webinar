<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 30.09.2018
 * Time: 15:16
 */
declare(strict_types=1);

namespace App\Http\Handlers;

use App\Http\Render\RenderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;

class IndexHandler implements RequestHandlerInterface
{

    private $render;
    public function __construct(RenderInterface $render)
    {
        $this->render = $render;
    }

    /**
     * Handle the request and return a response.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new HtmlResponse($this->render->render('index.twig'));
    }
}
