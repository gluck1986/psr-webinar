<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 03.10.2018
 * Time: 17:54
 */
declare(strict_types=1);

namespace App\Services;

use Psr\Http\Message\ServerRequestInterface;

class CalcService
{
    public function process(ServerRequestInterface $serverRequest): ServerRequestInterface
    {
        $a = $serverRequest->getAttribute('a');
        $b = $serverRequest->getAttribute('b');
        $result = $a + $b;

        return $serverRequest->withAttribute('calc_result', $result);
    }
}
