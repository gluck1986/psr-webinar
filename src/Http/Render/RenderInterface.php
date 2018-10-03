<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 01.10.2018
 * Time: 22:30
 */
namespace App\Http\Render;
interface RenderInterface
{
    public function render(string $template, array $variables = []);
}
