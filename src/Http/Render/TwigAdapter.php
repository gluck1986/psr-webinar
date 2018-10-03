<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 01.10.2018
 * Time: 22:32
 */
declare(strict_types=1);

namespace App\Http\Render;

class TwigAdapter implements RenderInterface
{
    private $environment;
    public function __construct(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }
    public function Render(string $template, array $variables = [])
    {
        return $this->environment->render($template, $variables);
    }
}
