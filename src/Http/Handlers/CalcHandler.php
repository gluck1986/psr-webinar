<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 30.09.2018
 * Time: 15:16
 */
declare(strict_types=1);

namespace App\Http\Handlers;

use App\Services\CalcService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\TextResponse;

class CalcHandler implements RequestHandlerInterface
{
    private $calcService;

    public function __construct(CalcService $calcService)
    {
        $this->calcService = $calcService;
    }

    /**
     * Handle the request and return a response.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $request = $this->calcService->process($request);

        return new TextResponse('result = '. $request->getAttribute('calc_result'));
    }
}
